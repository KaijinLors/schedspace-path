<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function findCriticalPath($currentTask, $TasksWithEstEft, $criticalPath)
{
    global $firstTask;


    // Add the current task to the critical path
    $criticalPath[] = $currentTask;
    // Check if we've reached the firstTask
    if ($currentTask === $firstTask) {
        return $criticalPath;
    }

    // Find prerequisites of the current task
    $prerequisites = $TasksWithEstEft[$currentTask]["prerequisites"];

    $maxPrerequisite = null;
    $maxPrerequisiteDuration = 0;

    // Find the prerequisite with the maximum duration
    foreach ($prerequisites as $pre) {
        if (
            isset($TasksWithEstEft[$pre]) &&
            $TasksWithEstEft[$pre]["duration"] > $maxPrerequisiteDuration
        ) {
            $maxPrerequisiteDuration = $TasksWithEstEft[$pre]["duration"];
            $maxPrerequisite = $pre;
        }
    }

    // Continue the path by recursively calling the function with the maxPrerequisite
    if ($maxPrerequisite !== null) {
        return findCriticalPath($maxPrerequisite, $TasksWithEstEft, $criticalPath);
    }

    return $criticalPath; // Return the critical path so far
}



function countAllSuccessors($taskName, $Successors)
{
    if (!isset($Successors[$taskName])) {
        return 0;
    }

    $count = count($Successors[$taskName]);
    foreach ($Successors[$taskName] as $successor) {
        $count += countAllSuccessors($successor, $Successors);
    }
    return $count;
}

function getWorker($worker)
{
    global $conn;
    $user = $_SESSION['user'];
    $escapedWorker = mysqli_real_escape_string($conn, $worker);
    $sql = "SELECT sworkers.id FROM sworkers WHERE sworkers.CompanyN = '" . $user['CompanyN'] . "' AND sworkers.Uc = '" . $user['Uc'] . "' AND sworkers.Sname = '$escapedWorker'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die('Error: ' . mysqli_error($conn));
    }
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['id'] : null;
}


function show($arr)
{
    echo "<pre>";
    print_r($arr);
}



date_default_timezone_set('Asia/Manila');
$dateToday = date("Y-m-d");
function processData()
{
    global $conn;
    global $dateToday;
    // Access the user data
    $user = $_SESSION['user'];

    require_once "database.php";
    $tasksData = [];
    $highestEFT = 0;
    $lastTask = null;


    $sql2 = "SELECT stask.Userid, sworkers.Sname, stask.Taskn, stask.Description, stask.Due, stask.Status, stask.Priority, stask.EST, stask.EFT, stask.LST, stask.LFT, stask.Prerequisite, stask.Projectid, stask.date, stask.startDate, stask.expectedDate
    FROM stask JOIN sworkers ON sworkers.Id = stask.Userid JOIN sproject ON stask.projectid = sproject.Projectn
    WHERE stask.CompanyN = '" . $user['CompanyN'] . "' AND stask.Uc = '" . $user['Uc'] . "' AND sproject.status = 'Ongoing'";

    $result = $conn->query($sql2);
    $counter = 0;
    $startdate = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // echo "<pre>";
            // print_r($row);
            $taskName = $row["Taskn"];
            $duration = (int) $row["Due"];
            $workerName = $row["Sname"];
            $status = $row['Status'];
            $workerId = $row['Userid'];
            $description = $row['Description'];
            $lst = (int) $row["LST"];
            $est = (int) $row["EST"];
            $lft = (int) $row["LFT"];
            $eft = (int) $row["EFT"];
            $priority = $row['Priority'];
            $prereq = $row["Prerequisite"];
            $Projectid = $row['Projectid'];
            $startingDate = $row['startDate'];
            $expectedDate = $row['expectedDate'];
            $prerequisites = explode(",", $row["Prerequisite"]); // Split prerequisites into an array
            $prerequisitesArray = !empty($prereq) ? explode(",", $prereq) : [];
            $taskdate = $row['date'];
            if ($counter === 0) {
                $startdate = $row['date'];
            }
            $counter++;
            $tasksData[] = [
                "worker" => $workerName,
                "taskname" => $taskName,
                "duration" => $duration,
                "status" => $status,
                "description" => $description,
                'projectname' => $Projectid,
                "prerequisites" => $prereq,
                "priority" => $priority,
                "est" => $est,
                "lst" => $lst,
                "lft" => $lft,
                "eft" => $eft,
                "due" => $duration,
                "date" => $taskdate,
                "startDate" => $startingDate,
                "expectedDate" => $expectedDate,
            ];


            //put only est eft as null
            $TasksWithEstEft[$taskName] = [
                "eft" => $eft,
                "prerequisites" => $prerequisitesArray,
                "duration" => $duration,
            ];

            foreach ($TasksWithEstEft as $taskName => $task) {
                if ($task["eft"] > $highestEFT) {
                    $highestEFT = $task["eft"];
                    $lastTask = $taskName;
                }
            }

            // Group tasks by Projectid
            if (!isset($tasksByProject[$Projectid])) {
                $tasksByProject[$Projectid] = [];
            }
            $tasksByProject[$Projectid][] = $taskName;

        }
    }



    $criticalPath = [];
    if (!empty($tasksByProject)) {
        foreach ($tasksByProject as $Projectid => $tasks) {
            $TasksWithEstEftSubset = array_intersect_key($TasksWithEstEft, array_flip($tasks));
            $highestEFT2 = 0;
            foreach ($TasksWithEstEftSubset as $taskName => $task) {
                if ($task["eft"] > $highestEFT2) {
                    $highestEFT2 = $task["eft"];
                    $lastTask2 = $taskName;
                }
            }
            $criticalPath = findCriticalPath($lastTask2, $TasksWithEstEft, $criticalPath);
        }
    }
    $criticalPath = array_reverse($criticalPath);

    $task = [];
    $statusOftasks = [];
    $latetask = [];
    foreach ($tasksData as $taskName) {
        $task[] = [
            'name' => $taskName['taskname'],
            'worker' => $taskName['worker'],
            'duration' => $taskName['duration'],
            'status' => $taskName['status'],
            "description" => $taskName['description'],
            'projectname' => $taskName['projectname'],
            "priority" => $taskName['priority'],
            'eft' => $taskName['eft'],
            'start' => $taskName['est'],
            'critical' => 0,
            'prereq' => $taskName['prerequisites'],
            'date' => $taskName['date'],
            "startDate" => $taskName['startDate'],
            "expectedDate" => $taskName['expectedDate'],
        ];
        if ($taskName['status'] === "Finished") {
            $statusOftasks[] = $taskName['taskname'];
        }
        if ($taskName['status'] === "Ongoing" && $dateToday > $taskName['expectedDate']) {
            $latetask[] = $taskName['taskname'];
        }
    }




    $result = array(
        "task" => $task,
        "criticalPath" => $criticalPath,
        "finished" => $statusOftasks,
        "startdate" => $startdate,
        "highestEft" => $highestEFT,
        "latetask" => $latetask,
    );

    return $result;
}