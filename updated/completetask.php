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

require ("phplib.php");
$alltask = processData();
$taskOngoing = [];

foreach ($alltask['task'] as $task) {
    if ($task['status'] === "Ongoing") {
        $taskOngoing[] = $task['name'];
    }
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["task_select"])) {
        $selected_task = $_POST["task_select"];
        $sql = "UPDATE stask SET status = 'Finished' WHERE Taskn = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $selected_task);
        if ($stmt->execute()) {
            header("Location: manager_dashboard.php");
            exit();
        } else {
            echo "Error updating task status: " . $conn->error;
        }
        $stmt->close();
    }
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
    <style>
        .form-select {
            padding: 10px;
            width: 300px;
            font-size: 16px;
            border: 2px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            background-color: rgb(25, 106, 95);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .upload-option {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .upload-option form {
            text-align: center;
        }

        button[type="submit"]:hover {
            background-color: #5C8993;
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
            <form method="post">
                <select class="form-select" name="task_select">
                    <option selected>Select task to complete</option>
                    <!-- Assuming $alltask is an array containing the task values -->
                    <?php
                    foreach ($taskOngoing as $task) {
                        echo "<option>$task</option>";
                    }
                    ?>
                </select>
                <br>
                <br>
                <button type="submit">Submit</button>
            </form>
        </div>

    </div>

    <script>
        // Slidebar mechanism
        function toggleSideBar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("sidebar-close");
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
</body>

</html>