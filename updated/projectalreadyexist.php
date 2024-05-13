
<?php 
session_start();

// Check if the user is logged in
if (isset($_SESSION["user"])) {
    // User is logged in
    $user = $_SESSION["user"];
    $messagetoshow = $_SESSION['messagetoshow'];

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


    $count_query = "SELECT COUNT(*) as count FROM stask WHERE stask.CompanyN = '" . $user['CompanyN'] . "' AND stask.Uc = '" . $user['Uc'] . "' AND stask.status = 'Ongoing'";
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
    <style>
        /* Styling for header */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #31363F;
            z-index: 2;
            /* Ensures header is on top of sidebar */
            font-size: 56px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            color: #76ABAE;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #EEEEEE;
            position: relative;
            /* Set body position to relative for absolute positioning */
        }


        /* Styling for user dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
            font-size: 20px;
            color: white;
            font-family: Arial, sans-serif;
            font-weight: 50;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #76ABAE;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            font-size: 16px;
        }


        /* Styling for sidebar */
        .sb-menu {
            height: 32px;
            margin-top: 5px;
            margin-right: 10px;
            cursor: pointer;
        }

        .sb-menu:hover {
            background-color: #76ABAE;
            border: #555;
            border-radius: 5px;
            box-shadow: 0 1.5px 5px rgba(0, 0, 0, .5);
        }

        .sb-menu-but {
            height: 32px;
            width: 32px;
        }

        .sidebar {
            width: 200px;
            height: 100vh;
            background-color: #333;
            color: #fff;
            padding-top: 100px;
            left: 0;
            top: 20px;
            /* Adjusted the value */
            position: fixed;
            z-index: 1;
            transition: .1s;
            font-family: Arial, Helvetica, sans-serif;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            /* Ensure flex container is column-oriented */
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            padding: 10px 20px;
            cursor: pointer;
            display: flex;
        }

        .sidebar ul li:not(:last-child):hover {
            background-color: #555;
        }

        .sidebar ul li:last-child {
            margin-top: auto;
            /* Pushes the last li to the bottom */
        }

        .sb-icon {
            padding-right: 15px;
        }

        /* Main content area */
        .main-content {
            margin-left: 200px;
            /* Adjust based on sidebar width */
            padding-top: 140px;
            /* Ensure space for the header */
            display: grid;
            align-content: center;
            align-items: stretch;
        }

        .this-month {
            position: absolute;
            top: 130px;
            left: 220px;
            padding: 10px 20px;
            background-color: #faf6f6;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .upload-option {
            padding: 20px;
            display: grid;
            position: absolute;
            justify-self: center;
            align-content: space-evenly;
            justify-content: space-around;
            align-items: start;
            justify-items: stretch;
        }

        .upload-button {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px 30px;
            background-color: rgb(25, 106, 95);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .upload-button:hover {
            background-color: #5C8993;
        }

        .upload-button img {
            margin-right: 10px;
        }


        #logoutButton {
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }

        #logoutButton:hover {
            background-color: #d32f2f;
        }

        #logoutButton:focus {
            outline: none;
        }

        /* Styling for website name */
        .website-name {
            font-size: 36px;
            /* Adjust the font size as needed */
            color: #76ABAE;
            /* Use the website name color */
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            /* Format SB button with website-name*/
            display: flex;
        }

        /* Styling for company name */
        .company-name {
            font-size: 20px;
            /* Adjust the font size as needed */
            color: #76ABAE;
            /* Use the company name color */
            font-family: Arial, sans-serif;
        }

        .item {
            margin-right: 40px;
            /* Change the value to your desired spacing */
        }

        .file-input label {
            font-family: 'Courier New', Courier, monospace;
        }

        .upload-option {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .file-input,
        .import-button {
            padding: 15px 30px;
            background-color: rgb(25, 106, 95);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .file-input:hover,
        .import-button:hover {
            background-color: rgb(10, 86, 75);
        }
    </style>
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
            <p style="color: red;"><?php echo $messagetoshow; ?></p>
            <br>
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
                    window.location.href = 'upload_project.php';
                }

                function redirectToDeleteMember() {
                    window.location.href = 'upload_project.php';
                }



            </script>
        </div>
</body>

</html>

