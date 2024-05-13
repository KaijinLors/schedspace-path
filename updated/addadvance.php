<?php
require "phplib.php";
$user = $_SESSION['user'];
$CompanyN =  $user['CompanyN'];
$Uc =  $user['Uc'];
$User = $user['Mname'];
$objData = processData();
$startDate = $objData['startdate'];
$endDate = $objData['highestEft'];
$date = new DateTime($startDate);
$date->modify('+' . ($endDate - 1) . ' days');
$newTaskEft = $date->format('Y-m-d');

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchedSpace | Create Advanced Project</title>
    <link rel="shortcut icon" href="calendar-week-fill.svg" type="image/x-icon">
    <link rel="icon" href="rocket-takeoff-fill.svg" type="image/svg+xml">
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
        <div class="company-name item"><?php echo $CompanyN?></div>
        <div class="rightHeader">
           
            <div id="notifBox" class="notifBox" style="display: none;">
                <h5>Notification</h5>
                <div class="spaceBr"></div>
                <div class="notif">
                    <ul>
                        <li><img class="notif-icon" src="icons8-clock-21.png">Late Task Alert</li>
                    </ul>
                </div>
            </div>
            <div class="dropdown item"><?php echo $User?></div>
        </div>
    </header>

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
        <button id="logoutButton" class="logoutButton">Logout</button>
    </div>

    <div class="main-content">
        <!-- Content goes here -->
        <div class="text">
            <h1>Create Advanced Project</h1>
        </div>

        <form id="myForm" action="addadvance_process.php" method="post">
            <div class="projDetails">
                <div class="projname">
                    <input type="text" name="ProjectN" class="form-control" placeholder="Project Name" required>
                </div>
                <div class="priority">
                    <select name="PriorityProject" placeholder="Priority Project" required>
                        <option value="" disabled selected>--Select Priority Level--</option>
                        <option value="Priority">Priority Project</option>
                        <option value="notPriority">Non Priority Project</option>
                    </select>
                </div>
                <div class="projStartDate">
    <label for="projStartDate">Start Date:</label>
    <input type="date" id="projStartDate" name="projStartDate" required min="<?php echo date('Y-m-d', strtotime($newTaskEft . ' + 1 day')); ?>">
</div>
                <div class="expectedDate">
                Project start date must be after: <span class="red-text"><?php echo date("F j, Y", strtotime($newTaskEft))?></span>
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
                        <td><button class="button" id="deleterow" onclick="deleteRow()">Delete</button></td>
                    </tr>
                </tbody>
            </table>
            <div class="save-button">
                <button id="saveButton">Save</button>
            </div>
        </form>
        <button id="Addothertask" onclick="addTask()">Add</button>
        <script>

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


            // Slidebar mechanism
            function toggleSideBar() {
                const sidebar = document.getElementById("sidebar");
                sidebar.classList.toggle("sidebar-close");
            }

            // Notification box
            function toggleNotification() {
                var notif = document.getElementById("notifBox");
                if (notif.style.display === "none") {
                    notif.style.display = "block";
                } else {
                    notif.style.display = "none";
                }
            }

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