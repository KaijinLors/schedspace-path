<?php
require ("phplib.php");

$user = $_SESSION['user'];


$newProjectName = $RawTasks[0]['Projectid'];
$sql = "SELECT COUNT(*) as count FROM sproject WHERE Projectn = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $newProjectName);

$stmt->execute();

$stmt->bind_result($count);

$stmt->fetch();

$stmt->close();

if ($count > 0) {
    $_SESSION['messagetoshow'] = "Project Name Already Exist.";
    header("Location: projectalreadyexist.php");
    exit;
}

$objdata = array("task" => array());
$TasksWithEstEft = [];

//SET EST AND EFT FOR EACH TASK
foreach ($RawTasks as $RawTask) {
    $taskName = $RawTask['Taskn'];
    $description = $RawTask['Description'];
    $workers = $RawTask['Userid'];
    $workerName = $RawTask['Workername'];
    $prereq = $RawTask['Prerequisite'];
    $projectid = $RawTask['Projectid'];
    $priority = $RawTask['Priority'];
    $duration = (int) $RawTask['Due']; // Convert the duration to an integer
    $prerequisitesArray = !empty($prereq) ? explode(",", $prereq) : [];

    foreach ($prerequisitesArray as $pre) {
        $Successors[$pre][] = $taskName;

    }
    //put only est eft as null
    $TasksWithEstEft[$taskName] = [
        "description" => $description,
        "workers" => $workerName,
        "projectid" => $projectid,
        "prerequisites" => $prerequisitesArray,
        "duration" => $duration,
        "priority" => $priority,
        "est" => null, // Initialize EST to null
        "eft" => null, // Initialize EFT to null
    ];

}


// Find the first task with the most successors
$maxSuccessorsCount = 0;
$firstTask = null;




if (!is_null($firstTask)) {
    // Initialize EST and EFT for the first task
    $TasksWithEstEft[$firstTask]["est"] = 0;
    $TasksWithEstEft[$firstTask]["eft"] = $TasksWithEstEft[$firstTask]["duration"];

    // Calculate EST and EFT only for tasks with no prerequisites
    foreach ($TasksWithEstEft as $taskName => $task) {
        $prerequisites = $task["prerequisites"];
        $duration = $task["duration"];


        if (empty($prerequisites) && $taskName !== $firstTask) { ///////////////////////////////////////////////////////////////////////////////////////////////// PUT AND THIS TASK IS NOT THE FIRST TASK :) :)
            // Task has no prerequisites, set est to 0 and eft to est + duration
            $TasksWithEstEft[$taskName]["est"] = 0;
            $TasksWithEstEft[$taskName]["eft"] = $TasksWithEstEft[$taskName]["est"] + $duration;
        } else {
            // Task has prerequisites, calculate EST and EFT based on the existing logic
            $maxEFTOfPrerequisites = 0;
            foreach ($prerequisites as $pre) {
                if ($TasksWithEstEft[$pre]["eft"] > $maxEFTOfPrerequisites) {
                    $maxEFTOfPrerequisites = $TasksWithEstEft[$pre]["eft"];
                }
            }
            $TasksWithEstEft[$taskName]["est"] = $maxEFTOfPrerequisites;
            $TasksWithEstEft[$taskName]["eft"] = $TasksWithEstEft[$taskName]["est"] + $duration;
        }
    }

}


$insertedTask = [];
foreach ($TasksWithEstEft as $taskname => $taskinfo) {
    $insertedTask[] = [
        'taskname' => $taskname,
        'start' => $taskinfo['est'],
        'duration' => $taskinfo['duration'],
        'worker' => $taskinfo['workers'],
        'description' => $taskinfo['description'],
        'projectid' => $taskinfo['projectid'],
        'priority' => $taskinfo['priority'],
        'prereq' => $taskinfo['prerequisites'],

    ];

}
// show($insertedTask);
// exit();
function sortWorkerTask($arrData)
{
    $workerTask = [];
    foreach ($arrData as $task) {
        if (!isset($workerTask[$task['worker']])) {
            $workerTask[$task['worker']] = [];
        }
        $workerTask[$task['worker']][] = $task;
    }
    foreach ($workerTask as $worker => $tasks) {
        usort($workerTask[$worker], function ($a, $b) {
            return $a['start'] - $b['start'];
        });
    }
    return $workerTask;
}

function getWorkerAvailability($arrData)
{
    $workerSortedTask = sortWorkerTask($arrData);
    $workerAvailable = [];
    foreach ($workerSortedTask as $worker => $tasks) {
        $available = [];
        foreach ($tasks as $task) {
            for ($i = $task['start']; $i < $task['start'] + $task['duration']; $i++) {
                $available[] = $i;
            }
        }
        $workerAvailable[$worker][] = $available;
    }
    $result = [];
    foreach ($workerAvailable as $worker => $availabilities) {
        $result[$worker] = [];
        foreach ($availabilities as $available) {
            $counter = 0;
            foreach ($available as $value) {
                if ($counter === $value) {
                    $counter++;
                } else {
                    $result[$worker][] = [
                        "start" => $counter,
                        "duration" => $value - $counter
                    ];
                    $counter = $value;
                    $counter++;
                }
            }
        }
    }
    return $result;
}

function checkAvailable($duration, $worker, $workerAvailability, $minDay)
{
    $start = null;
    $result = array(
        'available' => false,
        'start' => $start
    );

    if (array_key_exists($worker, $workerAvailability)) {
        foreach ($workerAvailability[$worker] as $index => $available) {
            $endDay = $minDay + $duration;
            $available['end'] = $available['start'] + $available['duration'];
            if ($available['duration'] >= $duration && ($available['start'] >= $minDay || $available['end'] >= $endDay) && $result['available'] === false) {
                $result = array(
                    'available' => true,
                    'index' => $index,
                    'start' => max($minDay, $available['start']),
                    'worker' => $worker
                );
            }
        }
    }
    return $result;
}

function getWorkerTask($worker, $arrData)
{
    $workerTask = [];
    foreach ($arrData as $task) {
        if ($task['worker'] === $worker) {
            $workerTask[] = $task;
        }
    }
    return $workerTask;
}

function getHighestEFT($worker, $arrData, $highestFinishedEft)
{
    $highestEFT = 0;
    $tasks = getWorkerTask($worker, $arrData);
    if (!empty($tasks)) {
        foreach ($tasks as $task) {
            $highestEFT = max($task['eft'], $highestEFT, $highestFinishedEft);
        }
    } else {
        $highestEFT = max($highestEFT, $highestFinishedEft);
    }

    return $highestEFT;
}



function getTaskEft($taskname, $arrGotSlot)
{
    $objItem = [];
    foreach ($arrGotSlot as $item) {
        if ($item['taskname'] === $taskname) {
            $objItem = $item;
            break;
        }
    }
    return ($objItem['start'] ?? 0) + ($objItem['duration'] ?? 0);
}


function getHighestPreq($arrPreq, $arrGotSlot)
{
    if (!$arrPreq || count($arrPreq) === 0)
        return "";
    return array_reduce($arrPreq, function ($a, $b) use ($arrGotSlot) {
        return getTaskEft($a, $arrGotSlot) > getTaskEft($b, $arrGotSlot) ? $a : $b;
    });
}

function findMinStartWorkerIndex($perWorkerObj, $minDay, $duration)
{
    if (!$perWorkerObj) {
        return null;
    }
    return array_reduce($perWorkerObj, function ($prev, $curr) use ($minDay, $duration) {
        if ($curr['start'] >= $minDay && $curr['duration'] >= $duration) {
            if ($prev === null) {
                return $curr;
            } else {
                return $curr['start'] < $prev['start'] ? $curr : $prev;
            }
        } else {
            return $prev;
        }
    }, null);
}

function findNearestValue($availability, $minDay, $duration)
{
    // Check if $availability is empty, return null if so
    if (empty($availability)) {
        return null;
    }

    // Initialize $prev with the first key of $availability
    $firstKey = array_key_first($availability);
    $prev = $firstKey;

    return array_reduce(array_keys($availability), function ($prev, $curr) use ($availability, $minDay, $duration) {
        $currPerWorkerObj = findMinStartWorkerIndex($availability[$curr], $minDay, $duration);
        $prevWorkerObj = findMinStartWorkerIndex($availability[$prev], $minDay, $duration);
        if ($currPerWorkerObj !== null && ($prevWorkerObj === null || $currPerWorkerObj['start'] < $prevWorkerObj['start'])) {
            return $curr;
        } else {
            return $prev;
        }
    }, $prev);
}



function selectWorkers($availability, $workerArr)
{
    $result = [];
    foreach ($workerArr as $worker) {
        $result[$worker] = $availability[$worker] ?? null;
    }
    return $result;
}

function getSlot($insertedTask, $existingTask)
{
    global $highestFinishedEft;
    $arrGotSlot = [];
    $globalWorker = [];
    foreach ($insertedTask as $task) {
        $availability = getWorkerAvailability([...$existingTask, ...$arrGotSlot]);
        $task['assignedWorker'] = $task['worker'];
        $Highprereq = getHighestPreq($task['prereq'] ?? [], $arrGotSlot);
        $minDay = 0;
        $minDay = max($minDay, $highestFinishedEft);
        if ($Highprereq !== "") {
            $minDay = getTaskEft($Highprereq, $arrGotSlot);
        }
        $workerArr = explode(",", $task['worker']);
        $selectedWorker = selectWorkers($availability, $workerArr);
        $defaultworker = findNearestValue($selectedWorker, $minDay, $task['duration']);
        if ($defaultworker === "") {
            $defaultworker = $workerArr[0];
        }
        $check = checkAvailable($task['duration'], $defaultworker, $availability, $minDay);
        if (!isset($globalWorker[$defaultworker])) {
            $globalWorker[$defaultworker] = [];
        }
        if (!isset($globalWorker[$defaultworker]['highestEFT'])) {
            $globalWorker[$defaultworker]['highestEFT'] = getHighestEFT($defaultworker, $existingTask, $highestFinishedEft);
        }
        if ($check['available']) {
            $availability[$defaultworker][$check['index']]['start'] += $task['duration'];
            $availability[$defaultworker][$check['index']]['duration'] -= $task['duration'];
            $arrGotSlot[] = array_merge($task, [
                "start" => $check['start'],
                "worker" => $defaultworker,
                "prereq" => implode(",", $task['prereq'])
            ]);
        } else {
            $arrGotSlot[] = array_merge($task, [
                'start' => max($minDay, $globalWorker[$defaultworker]['highestEFT']),
                "worker" => $defaultworker,
                "prereq" => implode(",", $task['prereq'])
            ]);
            $globalWorker[$defaultworker]['highestEFT'] += $task['duration'];
        }
    }
    return $arrGotSlot;
}


$objData2 = processData();

$finishtask = array_filter($objData2['task'], function ($item) {
    return $item['status'] === "Finished";
});

$highestFinishedEft = 0;
if (!empty($finishtask)) {
    $highestFinishedEft = array_reduce($finishtask, function ($prev, $curr) {
        return ($curr['eft'] > $prev['eft']) ? $curr : $prev;
    }, $objData2['task'][0])['eft'];
}

$objData2['task'] = array_filter($objData2['task'], function ($item) {
    return $item['status'] === "Ongoing";
});


$Prioritytasks = [];
$Notprioritytasks = [];
foreach ($objData2['task'] as $item) {
    if ($item['priority'] === 'Priority') {
        $Prioritytasks[] = $item;
    } else if ($item['priority'] === 'NotPriority') {
        $Notprioritytasks[] = $item;
    }
}

if ($insertedTask[0]['priority'] === "Priority") {

    $result = getSlot($insertedTask, $Prioritytasks);
    $resultFormat = [];
    foreach ($result as $res) {
        $resultFormat[] = [
            'name' => $res['taskname'],
            'worker' => $res['worker'],
            'duration' => $res['duration'],
            'projectname' => $res['projectid'],
            'eft' => $res['start'] + $res['duration'],
            'start' => $res['start'],
            'prereq' => $res['prereq'],
        ];
    }

    $mergedresult = array_merge($Prioritytasks, $resultFormat);

    if (!empty($Notprioritytasks)) {
        $insertedTask2 = [];
        foreach ($Notprioritytasks as $task) {
            $insertedTask2[] = [
                'taskname' => $task['name'],
                'start' => null,
                'duration' => $task['duration'],
                'worker' => $task['worker'],
                'description' => $task['description'],
                'projectid' => $task['projectname'],
                'priority' => $task['priority'],
                'prereq' => explode(",", $task['prereq']),
            ];
        }
        $result2 = getSlot($insertedTask2, $mergedresult);

        foreach ($result2 as $res) {
            $taskname = mysqli_real_escape_string($conn, $res['taskname']);
            $start = mysqli_real_escape_string($conn, $res['start']);
            $companyN = mysqli_real_escape_string($conn, $user['CompanyN']);
            $uc = mysqli_real_escape_string($conn, $user['Uc']);

            $sql = "UPDATE stask SET est = '$start' WHERE Taskn = '$taskname' AND CompanyN = '$companyN' AND Uc = '$uc'";
            mysqli_query($conn, $sql);
        }
    }
} else {
    $result = getSlot($insertedTask, $objData2['task']);
}


$projectName;
$projectPriority;
$Status;
$datetoday = date("Y-m-d");


foreach ($result as $res) {
    $taskName = $res['taskname']; // Corrected variable name to match SQL query
    $priority = $res['priority'];
    $status = "Ongoing";
    $worker = getWorker($res['worker']);
    $projectname = $res['projectid'];
    $prereq = $res['prereq'];
    $description = $res['description'];
    $est = $res['start'];
    $duration = $res['duration'];
    $eft = $est + $duration;
    $companyName = $user['CompanyN'];
    $uc = $user['Uc'];
    $startDate = date("Y-m-d", strtotime($datetoday . " +" . $est . " days"));
    $expectedFinishedDate = date("Y-m-d", strtotime($datetoday . " +" . $est - 1 + $duration . " days"));
    $projectName = $projectname;
    $projectPriority = $priority;
    $Status = $status;

    // Prepare SQL statement
    $sql = "INSERT INTO stask (Taskn, Description, Priority, Status, Prerequisite, Due, Userid, Projectid, EST, EFT, CompanyN, Uc , startDate, expectedDate) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssissiissss", // Corrected binding order to match SQL query
        $taskName,
        $description,
        $priority,
        $status,
        $prereq,
        $duration,
        $worker,
        $projectname,
        $est,
        $eft,
        $companyName,
        $uc,
        $startDate,
        $expectedFinishedDate,
    );

    // Execute the statement
    $stmt->execute();

    // Close statement
    $stmt->close();
}


$sql = "INSERT INTO sproject (Projectn, Priority, Status, CompanyN, Uc) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

$stmt->bind_param("sssss", $projectName, $projectPriority, $status, $user['CompanyN'], $user['Uc']);

$stmt->execute();
// Check for errors
if ($stmt->errno) {
    // Handle error
}

$stmt->close();
$conn->close();



?>