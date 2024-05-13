<?php 

require("phplib.php");

$user = $_SESSION['user'];

$TasksWithEstEft = [];
$Successors = [];
$Modifiedtask = [];
//SET EST AND EFT FOR EACH TASK
foreach ($RawTasks as $RawTask) {
    $taskName = $RawTask['Taskn'];
    $description = $RawTask['Description'];
    $workers = $RawTask['Userid'];
    $prereq = $RawTask['Prerequisite'];
    $projectid = $RawTask['Projectid'];
    $priority = $RawTask['Priority'];
    $duration = (int) $RawTask['Due']; // Convert the duration to an integer
    $prerequisitesArray = !empty($prereq) ? explode(",", $prereq) : [];

    foreach ($prerequisitesArray as $pre) {
        $Successors[$pre][] = $taskName;

    }
    $TasksWithEstEft[$taskName] = [
        "description" => $description,
        "workers" => $workers,
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

function countAllSuccessors3($taskName, $Successors)
{
    if (!isset($Successors[$taskName])) {
        return 0;
    }

    $count = count($Successors[$taskName]);
    foreach ($Successors[$taskName] as $successor) {
        $count += countAllSuccessors3($successor, $Successors);
    }
    return $count;
}


foreach ($TasksWithEstEft as $taskName => $task) {
    $numSuccessors = countAllSuccessors3($taskName, $Successors);


    if ($numSuccessors > $maxSuccessorsCount) {
        $maxSuccessorsCount = $numSuccessors;
        $firstTask = $taskName;
    }
}

//echo "First Task: $firstTask, Max Successors Count: $maxSuccessorsCount";



if (!is_null($firstTask)) {
    // Initialize EST and EFT for the first task
    $TasksWithEstEft[$firstTask]["est"] = 0;
    $TasksWithEstEft[$firstTask]["eft"] = $TasksWithEstEft[$firstTask]["duration"];

    // Calculate EST and EFT only for tasks with no prerequisites
    foreach ($TasksWithEstEft as $taskName => $task) {
        $prerequisites = $task["prerequisites"];
        $duration = $task["duration"];


        if (empty($prerequisites) && $taskName !== $firstTask) {
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






    $lastTask = null;
    $highestEFT = 0;
    foreach ($TasksWithEstEft as $taskName => $task) {
        if ($task["eft"] > $highestEFT) {
            $highestEFT = $task["eft"];
            $lastTask = $taskName;
        }
    }

    echo "First Tasks: $firstTask, Last Task: $lastTask<br><br>";
} else {
    echo "No such task found.";
}





function calculateLFTandLST($taskName, &$TasksWithEstEft, $Successors)
{
    // Check if this task has successors
    if (isset($Successors[$taskName])) {
        $minLFT = PHP_INT_MAX;

        foreach ($Successors[$taskName] as $Successor) {
            if (!isset($TasksWithEstEft[$Successor]["lft"])) {
                // Calculate LFT and LST for the successor
                calculateLFTandLST($Successor, $TasksWithEstEft, $Successors);
            }

            // Update the minimum LFT based on the successor
            $minLFT = min($minLFT, $TasksWithEstEft[$Successor]["lst"]);
        }

        // Calculate LFT and LST for this task based on the minimum LFT
        $TasksWithEstEft[$taskName]["lft"] = $minLFT;
        $TasksWithEstEft[$taskName]["lst"] =
            $TasksWithEstEft[$taskName]["lft"] - $TasksWithEstEft[$taskName]["duration"];
    } else {
        // If the task has no successors, its LFT is equal to its EFT
        $TasksWithEstEft[$taskName]["lft"] = $TasksWithEstEft[$taskName]["eft"];
        $TasksWithEstEft[$taskName]["lst"] =
            $TasksWithEstEft[$taskName]["lft"] - $TasksWithEstEft[$taskName]["duration"];
    }
}





// Function to find the critical path from lastTask to firstTask
function findCriticalPath3($currentTask, $TasksWithEstEft, $criticalPath)
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
        return findCriticalPath3($maxPrerequisite, $TasksWithEstEft, $criticalPath);
    }

    return $criticalPath; // Return the critical path so far
}





$criticalPath = [];
$criticalPath = findCriticalPath3($lastTask, $TasksWithEstEft, $criticalPath);
$criticalPath = array_reverse($criticalPath);
$TasktoAdjust = [];
$maxcritpath = 0;

if ($lastTask !== null) {
    $TasksWithEstEft[$lastTask]["lft"] = $TasksWithEstEft[$lastTask]["eft"];
    $TasksWithEstEft[$lastTask]["lst"] =
        $TasksWithEstEft[$lastTask]["lft"] - $TasksWithEstEft[$lastTask]["duration"];

    // Iterate through all tasks, excluding the last task
    foreach ($TasksWithEstEft as $taskName => $task) {
        if ($taskName !== $lastTask) {
            // Calculate LFT and LST for this task and its dependencies
            calculateLFTandLST($taskName, $TasksWithEstEft, $Successors);
        }
    }
}







foreach ($Successors as $task => $successors) {
    if (isset($TasksWithEstEft[$task])) {
        $TasksWithEstEft[$task]['successors'] = $successors;
    }
}
//echo "<pre>";
//var_dump($TasksWithEstEft);





foreach ($TasksWithEstEft as $taskName => $task) {
    $duration = $task["duration"];
    $est = $task["est"];
    $eft = $task["eft"];
    $lst = $task["lst"];
    $lft = $task["lft"];
    $priority = $task['priority'];
    $worker = $task['workers'];
    $projectid = $task['projectid'];
    $description = $task['description'];
    $prereq = implode(",", $task['prerequisites']);
    $succesor = '';

    if (isset($task['successors'])) {
        $succesor = implode(", ", $task['successors']);
    }




    $Modifiedtask[] = [
        "taskname" => $taskName,
        "duration" => $duration,
        "description" => $description,
        "worker" => $worker,
        "projectid" => $projectid,
        "est" => $est,
        "lst" => $lst,
        "lft" => $lft,
        "eft" => $eft,
        "priority" => $priority,
        "prereq" => $prereq,
        "successors" => $succesor,
        "critical" => 0,
    ];


    echo "Task:  $taskName, Duration: $duration, EST: $est, EFT: $eft, LST: $lst, LFT: $lft, Preq:  $prereq, worker, $worker, succesors:, $succesor  <br>";

}

echo "<br><br>Critical Path: " . implode(" -> ", $criticalPath);

function adjustduplicates($taskname, $firstoffset, &$TasksWithEstEft)
{
    $maxpreqeft = 0;
    $TasksWithEstEft[$taskname]['est'] = $firstoffset;
    $TasksWithEstEft[$taskname]['eft'] = $TasksWithEstEft[$taskname]['duration'] + $firstoffset;
    if (!isset($TasksWithEstEft[$taskname]['successors'])) {
        return;
    }

    foreach ($TasksWithEstEft[$taskname]['successors'] as $presuccessors) {
        $succesorsprereq = $TasksWithEstEft[$presuccessors]['prerequisites'];
        foreach ($succesorsprereq as $sucpreq) {
            if ($TasksWithEstEft[$sucpreq]['eft'] > $maxpreqeft) {
                $maxpreqeft = $TasksWithEstEft[$sucpreq]['eft'];
            }
        }

        $TasksWithEstEft[$presuccessors]['est'] = $maxpreqeft;
        $TasksWithEstEft[$presuccessors]['eft'] = $TasksWithEstEft[$presuccessors]['duration'] + $TasksWithEstEft[$presuccessors]['est'];
    }
}


function checkworkeravailability($workerNames, $defaultworker, $TasksWithEstEft, $highestEFT)
{
    $freeperiods = [];
    foreach ($workerNames as $individualworker) {
        if ($individualworker !== $defaultworker) {
            $otherworker = $individualworker;
            foreach ($TasksWithEstEft as $workertask => $worker) {
                if ($worker['workers'] === $otherworker) {
                    $workerinfo[] = [
                        'taskname' => $workertask,
                        'est' => $worker['est'],
                        'eft' => $worker['eft'],
                    ];
                }
            }

        }
    }

    // Sort tasks based on 'est' in ascending order
    usort($workerinfo, function ($a, $b) {
        return $a['est'] - $b['est'];
    });


    if ($workerinfo[0]['est'] > 0) {
        $startDay = 0;
        $endDay = $workerinfo[0]['est'];
        $duration = $workerinfo[0]['est'] - $startDay;

        $freePeriods[] = [
            'FreeEst' => $startDay,
            'EndEft' => $endDay,
            'duration' => $duration,
        ];
    }

    for ($i = 1; $i < count($workerinfo); $i++) {



        $startDay = $workerinfo[$i - 1]['eft'];
        $endDay = $workerinfo[$i]['est'];
        $duration = $endDay - $startDay;


        $freePeriods[] = [
            'FreeEst' => $startDay,
            'EndEft' => $endDay,
            'duration' => $duration,
        ];
    }

    $lastWorkerEft = !empty($workerinfo) ? end($workerinfo)['eft'] : null;

    $freeBetweenLastAndHighest = [
        'FreeEst' => $lastWorkerEft,
        'EndEft' => $highestEFT, // Make sure $highestEFT is defined
        'duration' => $lastWorkerEft !== null ? $highestEFT - $lastWorkerEft : 0,
    ];
    $freePeriods[] = $freeBetweenLastAndHighest;
    foreach ($freePeriods as $freeday) {
        echo "Est:\t\t" . $freeday['FreeEst'] . "\t\tEft\t\t" . $freeday['EndEft'] . "\t\tDuration\t\t" . $freeday['duration'] . "<br>";
    }
}






function checkTaskOverlap($allworkerinfo, &$TasksWithEstEft)
{
    $overlapTasks = array();

    // Group tasks by worker
    $tasksByWorker = array();
    foreach ($allworkerinfo as $task) {
        $worker = $task['worker'];
        if (!isset($tasksByWorker[$worker])) {
            $tasksByWorker[$worker] = array();
        }
        $tasksByWorker[$worker][] = $task;
    }

    // Check for overlap for each worker's tasks
    foreach ($tasksByWorker as $workerTasks) {
        for ($i = 0; $i < count($workerTasks); $i++) {
            for ($j = $i + 1; $j < count($workerTasks); $j++) {
                $task1 = &$workerTasks[$i];
                $task2 = &$workerTasks[$j];
                // Check if tasks overlap
                if (
                    ($task1['est'] < $task2['eft'] && $task1['eft'] > $task2['est']) ||
                    ($task2['est'] < $task1['eft'] && $task2['eft'] > $task1['est'])
                ) {
                    if ($task2['est'] > $task1['est']) {
                        $TasksWithEstEft[$task2['taskname']]['est'] = $task1['eft'];
                        $TasksWithEstEft[$task2['taskname']]['eft'] = $TasksWithEstEft[$task2['taskname']]['est'] + $TasksWithEstEft[$task2['taskname']]['duration'];
                    } else {
                        $TasksWithEstEft[$task1['taskname']]['est'] = $task2['eft'];
                        $TasksWithEstEft[$task1['taskname']]['eft'] = $TasksWithEstEft[$task1['taskname']]['est'] + $TasksWithEstEft[$task1['taskname']]['duration'];
                    }
                    $overlapTasks[] = array($task1['taskname'], $task2['taskname']);
                }
            }
        }
    }

    return $overlapTasks;
}




while (true) {
    //Modification of criticalpath before saving to database
    $estValuesByWorker = array();
    foreach ($Modifiedtask as $taskName) {
        $workerNames = explode(',', $taskName['worker']); // Split sname into an array
        $workerName = $workerNames[0];
        $currentEst = $taskName['est'];
        $taskNameValue = $taskName['taskname'];

        // Check if worker exists in the array
        if (!isset($estValuesByWorker[$workerName])) {
            // If not, add the worker with the current est value
            $estValuesByWorker[$workerName] = array($currentEst => array($taskNameValue));
        } else {
            // If worker exists, check if the est value already exists
            if (isset($estValuesByWorker[$workerName][$currentEst])) {
                // If it does, add the taskname to the existing array
                $estValuesByWorker[$workerName][$currentEst][] = $taskNameValue;
            } else {
                // If not, add the est value for the worker
                $estValuesByWorker[$workerName][$currentEst] = array($taskNameValue);
            }
        }
    }

    echo "<br>task with same est: ";
    $criticalPrereq = [];
    $taskwithcritdup = [];
    $taskefttoadd = "null";
    $duplicatetask = [];
    $criticalduplicatename = 'null';

    foreach ($estValuesByWorker as $worker => $estValues) {
        foreach ($estValues as $est => $taskNames) {

            if (count($taskNames) > 1) {
                foreach ($taskNames as $taskN) {
                    echo $taskN;
                    $duplicatetask[] = $taskN;
                }
            }
        }
    }



    if (count($duplicatetask) === 0) {
        break;
    }



    echo "<br><br><br>";



    $succ = [];
    $order = [];
    $objectarray = [];
    $taskwithnosuccessors = [];

    while (count($duplicatetask) > 0) {

        foreach ($duplicatetask as $taskN) {
            $workerNames = explode(',', $TasksWithEstEft[$taskN]['workers']);
            $defaultworker = $workerNames[0];
            $workerinfo = [];
            if (count($workerNames) > 1) {
                checkworkeravailability($workerNames, $defaultworker, $TasksWithEstEft, $highestEFT);
            }


            if (!isset($TasksWithEstEft[$taskN]['successors'])) {
                $taskwithnosuccessors[] = $taskN;
                $duplicatetask = array_diff($duplicatetask, $taskwithnosuccessors);
                continue;
            }

            foreach ($TasksWithEstEft[$taskN]['successors'] as $tasksucc) {
                if (in_array($tasksucc, $criticalPath)) {
                    $succ[] = $taskN;
                }
            }
        }
        if (count($duplicatetask) === 1) {
            $firstElement = reset($duplicatetask);
            $order[] = $firstElement;
            $duplicatetask = array_diff($duplicatetask, $order);
        } else if (count($succ) === 1) {
            $order[] = $succ[0];
            $duplicatetask = array_diff($duplicatetask, $order);
        } else {
            foreach ($succ as $successorest) {
                foreach ($TasksWithEstEft[$successorest]['successors'] as $task) {
                    if (in_array($task, $criticalPath)) {
                        if (isset($objectarray[$successorest])) {
                            $objectarray[$successorest] = min($TasksWithEstEft[$task]['est'], $objectarray[$successorest]);
                        } else {
                            $objectarray[$successorest] = $TasksWithEstEft[$task]['est'];
                        }
                    }
                }

            }
            $minsuccessorest = null;
            foreach ($objectarray as $key => $objects) {
                if ($minsuccessorest === null) {
                    $minsuccessorest = $objects;
                } else {
                    $minsuccessorest = min($minsuccessorest, $objects);
                }

            }


            foreach ($objectarray as $key => $objects) {
                if ($minsuccessorest === $objects) {
                    $order[] = $key;
                    $duplicatetask = array_diff($duplicatetask, $order);
                }
            }

        }
    }
    $order = array_merge($order, $taskwithnosuccessors);





    for ($i = 0; $i < count($order); $i++) {
        if ($i != 0) {
            $firstoffset = $TasksWithEstEft[$order[$i - 1]]['eft'];
            adjustduplicates($order[$i], $firstoffset, $TasksWithEstEft);
        }
    }


    $Modifiedtask = array();
    foreach ($TasksWithEstEft as $taskName => $task) {
        $duration = $task["duration"];
        $est = $task["est"];
        $eft = $task["eft"];
        $lst = $task["lst"];
        $lft = $task["lft"];
        $worker = $task['workers'];
        $projectid = $task['projectid'];
        $description = $task['description'];
        $priority = $task['priority'];
        $prereq = implode(",", $task['prerequisites']);
        $succesor = '';

        if (isset($task['successors'])) {
            $succesor = implode(", ", $task['successors']);
        }


        $Modifiedtask[] = [
            "taskname" => $taskName,
            "duration" => $duration,
            "description" => $description,
            "worker" => $worker,
            "projectid" => $projectid,
            "est" => $est,
            "lst" => $lst,
            "lft" => $lft,
            "eft" => $eft,
            "priority" => $priority,
            "prereq" => $prereq,
            "successors" => $succesor,
            "critical" => 0,
        ];

        echo "Task:  $taskName, Duration: $duration, EST: $est, EFT: $eft, LST: $lst, LFT: $lft, Preq:  $prereq, worker, $worker, succesors:, $succesor  <br>";

    }



}




while (true) {
    $allWorkers = [];
    $allworkerinfo = [];

    foreach ($TasksWithEstEft as $taskName => $task) {
        $workerNames = explode(',', $task['workers']);

        foreach ($workerNames as $workerName) {
            $workerName = trim($workerName);

            if (!isset($allWorkers[$workerName])) {
                $allWorkers[$workerName] = $workerName;
            }
        }
    }


    

    foreach ($allWorkers as $indivwork) {
        foreach ($TasksWithEstEft as $taskname => $worker) {
            $implodedworker = explode(",", $worker['workers']);
            if ($implodedworker[0] === $indivwork) {
                $allworkerinfo[] = [
                    'taskname' => $taskname,
                    'est' => $worker['est'],
                    'eft' => $worker['eft'],
                    'worker' => $implodedworker[0],
                ];
            }

        }
    }
    $overlapTasks = checkTaskOverlap($allworkerinfo, $TasksWithEstEft);
    if(empty($overlapTasks)){
        break;
    }
}



$Modifiedtask = array();
foreach ($TasksWithEstEft as $taskName => $task) {
    $duration = $task["duration"];
    $est = $task["est"];
    $eft = $task["eft"];
    $lst = $task["lst"];
    $lft = $task["lft"];
    $worker = $task['workers'];
    $projectid = $task['projectid'];
    $description = $task['description'];
    $prereq = implode(",", $task['prerequisites']);
    $priority = $task['priority'];
    $succesor = '';

    if (isset($task['successors'])) {
        $succesor = implode(", ", $task['successors']);
    }


    $Modifiedtask[] = [
        "taskname" => $taskName,
        "duration" => $duration,
        "description" => $description,
        "worker" => $worker,
        "projectid" => $projectid,
        "est" => $est,
        "lst" => $lst,
        "lft" => $lft,
        "eft" => $eft,
        "priority" => $priority,
        "prereq" => $prereq,
        "successors" => $succesor,
        "critical" => 0,
    ];

    echo "Task:  $taskName, Duration: $duration, EST: $est, EFT: $eft, LST: $lst, LFT: $lft, Preq:  $prereq, worker, $worker, succesors:, $succesor  <br>";

}


if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
// show($Modifiedtask);
// exit();
$projectName; 
$projectPriority;
$Status;
$datetoday = date("Y-m-d");



// Iterate over each task in $Modifiedtask
foreach ($Modifiedtask as $task) {
    $taskName = $task['taskname'];
    $duration = $task['duration'];
    $description = $task['description'];
    $workers = explode(",",$task['worker']);
    $worker = $workers[0];
    $projectid = $task['projectid'];
    $est = $task['est'];
    $lst = $task['lst'];
    $lft = $task['lft'];
    $eft = $task['eft'];
    $prereq = $task['prereq'];
    $status = "Ongoing";
    $priority = $task['priority'];
    $companyname =  $user['CompanyN'];
    $UniqueCode = $user['Uc'];
    $startDate = date("Y-m-d", strtotime($datetoday . " +" . $task['est'] . " days"));
    $expectedFinishedDate = date("Y-m-d", strtotime($datetoday . " +" . $task['eft']-1 . " days"));
    $projectName =  $projectid;
    $projectPriority = $priority;
    $Status = $status;

    // Prepare SQL statement
    $sql = "INSERT INTO stask (Taskn, Description, Priority, Status, Due, Userid, Projectid, Prerequisite, EST, EFT, LST, LFT, CompanyN, Uc, startDate, expectedDate) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssisssiiiissss",
        $taskName,
        $description,
        $priority,
        $status,
        $duration,
        $worker,
        $projectid,
        $prereq,
        $est,
        $eft,
        $lst,
        $lft,
        $companyname,
        $UniqueCode,
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

    $stmt->bind_param("sssss", $projectName, $projectPriority, $status,$user['CompanyN'],$user['Uc']);

    $stmt->execute();
    // Check for errors
    if ($stmt->errno) {
        // Handle error
    }

    $stmt->close();
    $conn->close();




?>