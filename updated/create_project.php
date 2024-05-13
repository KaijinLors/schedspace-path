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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchedSpace | Create Project</title>
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
        <div class="text">
            <h1>CREATE PROJECT</h1>
        </div>




        <form id="myForm" action="data_of_createtask.php" method="post">

            <div class="deleteform">
                <div class="projname">
                    <input type="text" name="ProjectN" class="form-control" placeholder="Project Name" required>
                </div>
                <div class="priority">
    <select name="PriorityProject" placeholder="Priority Project" required>
        <option value="" disabled selected>--Select Priority Level--</option>
        <option value="Priority">Priority Project</option>
        <option value="NotPriority">Non Priority Project</option>
    </select>
</div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Task Name</th>
                        <th>Description</th>
                        <th>Duration</th>
                        <th>Prerequisite</th>
                        <th>Worker</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="dataTable">
                    <tr>
                        <td><input type="text" name="taskname[]" value=""></td>
                        <td><input type="text" name="Description[]" value=""></td>
                        <td><input type="text" name="Due[]" value=""></td>
                        <td><input type="text" name="Prerequisite[]" value=""></td>
                        <td><input type="text" name="Userid[]" value=""></td>
                        <td><button class="button" id="deleterow">Delete</button></td>
                    </tr>

                </tbody>
            </table>
            <div class="save-button">
                <button id="saveButton" name="save">Save</button>
            </div>
        </form>
        <button id="Addothertask" onclick="addTask()">Add</button>



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
                window.location.href = 'upload_project.php';
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

          

        function addTask() {
        var table = document.getElementById("dataTable");
        var newRow = table.insertRow();
        
        var cell1 = newRow.insertCell();
        var cell2 = newRow.insertCell();
        var cell3 = newRow.insertCell();
        var cell4 = newRow.insertCell();
        var cell5 = newRow.insertCell();
        var cell6 = newRow.insertCell();

        cell1.innerHTML = '<input type="text" name="taskname[]" value="">';
        cell2.innerHTML = '<input type="text" name="Description[]" value="">';
        cell3.innerHTML = '<input type="text" name="Due[]" value="">';
        cell4.innerHTML = '<input type="text" name="Prerequisite[]" value="">';
        cell5.innerHTML = '<input type="text" name="Userid[]" value="">';
        cell6.innerHTML = '<button class="button" onclick="deleteRow(this)">Delete</button>';
    }

    function deleteRow(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }

 


        </script>
    </div>
</body>

</html>