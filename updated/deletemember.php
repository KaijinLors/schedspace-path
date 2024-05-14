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
$allworkers = [];
$query = "SELECT * FROM sworkers WHERE CompanyN = '" . $user['CompanyN'] . "' AND Uc = '" . $user['Uc'] . "'";

$result = mysqli_query($conn, $query);


if (mysqli_num_rows($result) > 0) {
    // Fetch data from each row and add it to the $allworkers array
    while ($row = mysqli_fetch_assoc($result)) {
        $allworkers[] = $row;
    }
}


if (isset($_POST['worker_id']) && !empty($_POST['worker_id'])) {
    $worker_id = $_POST['worker_id'];
    $query = "DELETE FROM sworkers WHERE id = $worker_id";

    // Execute the query
    if (mysqli_query($conn, $query)) {
        header('Location: deletemember.php');
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchedSpace | Deletemember</title>
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
        <table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Tasks Ongoing</th>
            <th>Finished Tasks</th>
            <th>Total Tasks</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($allworkers as $worker): ?>
            <tr>
                <td><?php echo $worker['Sname']; ?></td>
                <td><?php echo $worker['Semail']; ?></td>
                <td>
                    <?php
                    // Assuming $worker['tasks'] is an array of tasks for the worker
                    if (isset($worker['tasks'])) {
                        foreach ($worker['tasks'] as $task) {
                            echo $task . '<br>';
                        }
                    } else {
                        echo 'No tasks assigned';
                    }
                    ?>
                </td>
                <td><?php echo $worker['FinisihedT']; ?></td>
                <td><?php echo $worker['TotalT']; ?></td>
                <td>
                    <!-- Delete button -->
                    <form action="deletemember.php" method="post">
                        <input type="hidden" name="worker_id" value="<?php echo $worker['Id']; ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this worker?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


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
            window.location.href = 'addmember.php';
        }

        function redirectToDeleteMember() {
            window.location.href = 'deletemember.php';
        }



    </script>
</body>

</html>