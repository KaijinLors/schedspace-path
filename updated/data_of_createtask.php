<?php
session_start(); // Start the session to access session variables
if (isset($_SESSION["user"])) {
    // User is logged in
    $user = $_SESSION["user"];
} else {

    header("Location: sign_in_page.php");
    exit;
}
require_once "database.php";
function getIdsByWorkers($workerNames, $conn)
{
    if (!$conn) {

        return false;
    }
    $workerNamesArray = explode(",", $workerNames);
    $workers = array();

    $stmtp = $conn->prepare("SELECT Id FROM sworkers WHERE Sname = ?");

    if (!$stmtp) {
        return false;
    }


    foreach ($workerNamesArray as $workerName) {
        $stmtp->bind_param("s", $workerName);

        $stmtp->execute();

        if ($stmtp->errno) {
            return false;
        }
        $result = $stmtp->get_result();
        $row = $result->fetch_assoc();
        if ($row) {
            $workers[] = $row['Id'];
        } else {
            $workers[] = null;
        }
        $stmtp->reset();
    }
    return $workers;
}



$RawTasks = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve project details
    $projectName = $_POST["ProjectN"];
    $priority = $_POST["PriorityProject"];

    // Retrieve task details
    $taskNames = $_POST["taskname"];
    $descriptions = $_POST["Description"];
    $duration = $_POST['Due'];
    $prerequisites = $_POST["Prerequisite"];
    $workerNames = $_POST["Userid"][0];
    $workerName = $_POST["Userid"];

    for ($i = 0; $i < count($taskNames); $i++) {


        $stmtp = $conn->prepare("SELECT Id FROM sproject WHERE Projectn = ?");
        $stmtp->bind_param("s", $projectName);
        $stmtp->execute();
        $resultp = $stmtp->get_result();
        $row_project = $resultp->fetch_assoc();
        if ($row_project) {
            // Store the imploded worker IDs and project ID in variables
            $project_id = $row_project["Id"];
            $workers = getIdsByWorkers($_POST["Userid"][$i], $conn);
            $workerIdsImploded = implode(",", $workers);

            $task = array(
                "Taskn" => $taskNames[$i],
                "Description" => $descriptions[$i],
                "Due" => $duration[$i],
                "Prerequisite" => $prerequisites[$i],
                "Userid" => $workerIdsImploded,
                "Projectid" => $projectName,
                "Workername" => $workerName[$i],
                'Priority' => $priority,
            );

            $RawTasks[] = $task;
        } else {

            $workers = getIdsByWorkers($_POST["Userid"][$i], $conn);
            $workerIdsImploded = implode(",", $workers);
            $task = array(
                "Taskn" => $taskNames[$i],
                "Description" => $descriptions[$i],
                "Due" => $duration[$i],
                "Prerequisite" => $prerequisites[$i],
                "Userid" => $workerIdsImploded,
                "Projectid" => $projectName,
                "Workername" =>  $workerName[$i],
                'Priority' => $priority,
            );

            $RawTasks[] = $task;

        }
    }
}




    $count_query = "SELECT COUNT(*) as count FROM stask";
    $count_result = mysqli_query($conn, $count_query);
        // Fetch the count result
    $row = mysqli_fetch_assoc($count_result);
    $count = $row['count'];
    $TasksWithEstEft = [];
    // If the count is greater than 0, select all rows
    if ($count > 0) {
     include_once "uploadhasexisting.php";
    }
    else{
     include_once "uploadnoexisting.php";
    }
    header("Location: manager_dashboard.php");
    exit();



?>