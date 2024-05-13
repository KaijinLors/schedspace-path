<?php
session_start(); // Start the session to access session variables

// Check if the user is logged in
if (isset($_SESSION["user"])) {
    // User is logged in
    $user = $_SESSION["user"];

} else {

    header("Location: sign_in_page.php");
    exit;
}



require_once "database.php";
$RawTasks = [];

if (isset($_POST["import"])) {
    $fileName = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($fileName, "r");

        while (($column = fgetcsv($file, 10000, ",")) !== false) {
            // Fetch all IDs for the worker's names
            $workerIds = [];
            $workerNames = explode(',', $column[4]);

            $stmtn = $conn->prepare("SELECT Id FROM sworkers WHERE sname = ?");
            $stmtn->bind_param("s", $worker);
            foreach ($workerNames as $worker) {
                $stmtn->execute();
                $resultn = $stmtn->get_result();
                while ($row_worker = $resultn->fetch_assoc()) {
                    $workerId = $row_worker["Id"];
                    $workerIds[] = $workerId;
                }
            }
            $stmtn->close();

            // Fetch project ID
            $stmtp = $conn->prepare("SELECT Id FROM sproject WHERE Projectn = ?");
            $stmtp->bind_param("s", $column[5]);
            $stmtp->execute();
            $resultp = $stmtp->get_result();
            $row_project = $resultp->fetch_assoc();
            if ($row_project) {
                // Store the imploded worker IDs and project ID in variables
                $workerIdsImploded = implode(',', $workerIds);
                $project_id = $row_project["Id"];


                // Store task data in RawTasks array
                $taskData = [
                    'Taskn' => $column[0],
                    'Description' => $column[1],
                    'Due' => $column[2],
                    'Prerequisite' => $column[3],
                    'Userid' => $workerIdsImploded,
                    // 'Projectid' => $project_id,
                    'Projectid' => $column[5],
                    'Workername' => $column[4],
                    'Priority' => $column[6],
                ];
                $RawTasks[] = $taskData;
            } else {
                $workerIdsImploded = implode(',', $workerIds);
                $taskData = [
                    'Taskn' => $column[0],
                    'Description' => $column[1],
                    'Due' => $column[2],
                    'Prerequisite' => $column[3],
                    'Userid' => $workerIdsImploded,
                    // 'Projectid' => $project_id,
                    'Projectid' => $column[5],
                    'Workername' => $column[4],
                    'Priority' => $column[6],
                ];
                $RawTasks[] = $taskData;
            }
        }
        fclose($file);
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
    
} else {
    //echo "Import parameter not set.";
}




?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchedSpace | Upload Project</title>
    <link rel="shortcut icon" href="calendar-week-fill.svg" type="image/x-icon">
    <link rel="stylesheet" href="stylesforall.css">

</head>

<body>
    <!-- Header -->
    <header>
        <div class="website-name item">
            <div class="sb-menu">
                <img id="toggleButton" class="sb-menu-but" onclick="toggleSideBar()" src="./image/icons8-menu-32.png"
                    alt="Menu">
            </div>
            SchedSpace
        </div>
        <div class="company-name item">
            <?php
            echo $user['CompanyN'];
            ?>
        </div>
        <div class="dropdown item">
            <?php
            echo $user['Mname'];
            ?>
        </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <ul>
            <li onclick="redirectToHome()"><img class="sb-icon" src="./image/icons8-home-24.png">Home</li>
            <li onclick="redirectToUploadProject()"><img class="sb-icon" src="./image/icons8-upload-22.png">Upload
                Project</li>
            <li onclick="redirectToUpdateTask()"><img class="sb-icon" src="./image/icons8-update-20.png">Update Task
            </li>
            <li onclick="redirectToViewProjects()"><img class="sb-icon" src="./image/icons8-view-22.png">View Projects
            </li>
            <li onclick="redirectToAddMember()"><img class="sb-icon" src="./image/icons8-add-24.png">Add Member</li>
            <li onclick="redirectToDeleteMember()"><img class="sb-icon" src="./image/icons8-delete-22.png">Delete Member
            </li>
            <li><button id="logoutButton">Logout</button></li>
        </ul>
    </div>

    <!-- Main content area -->
    <div class="main-content">
        <!-- Content goes here -->
        <div class="upload-option">

            <form class="form-horizontal" action="" method="post" name="uploadCsv" enctype="multipart/form-data">

                <div style="display: flex;
            align-items: center;
            justify-content: center;">
                    <label style="font-family: Arial; margin-right: 10px;"></label>
                    <input type="file" name="file" accept=".csv" class="file-input"
                        style="margin-right: 10px; width: 300px;">
                    <button type="submit" name="import" class="import-button">Import</button>
                </div>

            </form>
            <br>
            <button class="upload-button" onclick="redirectToCreateProject()">
                <img src="./image/icons8-create-54.png" alt="Upload Icon" width="24">
                Create Project
            </button>
            <br>
            <button class="upload-button"  onclick="redirectToAdvanceCreateProject()">
                <img src="./image/icons8-future-50.png" alt="Upload Icon" width="24">
                Create Advanced Project
            </button>


            <script>
                // Slidebar mechanism
                function toggleSideBar() {
                    const sidebar = document.getElementById("sidebar");
                    sidebar.classList.toggle("sidebar-close");
                }


                function redirectToAdvanceCreateProject() {
                    window.location.href = 'addadvance.php';
                }

                function redirectToCreateProject() {
                    window.location.href = 'create_project.php';
                }

                document.getElementById("logoutButton").addEventListener("click", function () {
                    window.location.href = "manager_logout.php";
                });

                function redirectToHome() {
                    window.location.href = 'manager_dashboard.php';
                }

                function redirectToUploadProject() {
                    window.location.href = 'upload_project.php';
                }

                function redirectToUpdateTask() {
                    window.location.href = 'Updatetask.php';
                }

                function redirectToViewProjects() {
                    window.location.href = 'upload_project.php';
                }

                function redirectToAddMember() {
                    window.location.href = 'addmember.php';
                }

                function redirectToDeleteMember() {
                    window.location.href = 'upload_project.php';
                }



            </script>
        </div>
</body>

</html>