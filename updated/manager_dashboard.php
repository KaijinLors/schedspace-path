<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] != 'MANAGER') {
    header("Location: sign_in_page.php");
    exit;
}

$user = $_SESSION["user"];
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchedSpace | Manager Dashboard</title>
    <link rel="shortcut icon" href="calendar-week-fill.svg" type="image/x-icon">
    <link rel="stylesheet" href="gantt.css">
    <link rel="stylesheet" href="stylesforall.css">
    <link rel="stylesheet" href="bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .container {
            font-family: Verdana;
        }

        .bottom-right {
            position: absolute;
            top: 200px;
            right: 50px;
            font-size: 18px;
            color: #555;
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
        <div class="bottom-right">
            Today is: <span id="currentDate"></span>
        </div>
        <!-- Content goes here -->
        <div class="home-content">
            <div class="container">
                <h1 class="label">Dash Board</h1>
                <div class="chart">
                    <div class="chart-row chart-period">
                        <div class="chart-row-item"></div>
                    </div>
                    <div id="dynamicRowsContainer" class="chart-dynamic-rows">
                        <div class="chart-row" template>
                            <div class="chart-row-item"></div>
                            <ul class="chart-row-bars">
                                <li class="days" template></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="Legend">
                <li class="Legend-item">
                    <span class="Legend-colorBox" style="background-color: #3498db;">
                    </span>
                    <span class="Legend-label">
                        Ongoing/In Progress
                    </span>
                </li>
                <li class="Legend-item">
                    <span class="Legend-colorBox" style="background-color: #2ecc71;">
                    </span>
                    <span class="Legend-label">
                        Finished
                    </span>
                </li>
                <li class="Legend-item">
                    <span class="Legend-colorBox" style="background-color: #e74c3c;">
                    </span>
                    <span class="Legend-label">
                        Delayed
                    </span>
                </li>
                <li class="Legend-item">
                    <span class="Legend-colorBox" style="background-color: #f1c40f;">
                    </span>
                    <span class="Legend-label">
                        Critical Path
                    </span>
                </li>
            </ul>
        </div>

        <script>

            document.getElementById("logoutButton").addEventListener("click", function () {
                window.location.href = "manager_logout.php";
            });
            // <!-- JavaScript to display the current date -->
            // Slidebar mechanism
            function toggleSideBar() {
                const sidebar = document.getElementById("sidebar");
                sidebar.classList.toggle("sidebar-close");
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
                window.location.href = 'addmember.php';
            }

            function redirectToDeleteMember() {
                window.location.href = 'upload_project.php';
            }

            function activateTooltip(){
                var tooltipTriggerList3 = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                var tooltipList3 = [...tooltipTriggerList3].map(tooltipTriggerEl3=> new bootstrap.Tooltip(tooltipTriggerEl3))
            }



            // Get the current date
            var currentDate = new Date();
            // Format the date as "Month Day, Year"
            var formattedDate = currentDate.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            // Display the formatted date
            document.getElementById("currentDate").innerHTML = formattedDate;


            function formatDate(date) {
                // Array of month names
                var monthNames = ["January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];

                // Get month, day, and year
                var monthIndex = date.getMonth();
                var day = date.getDate();
                var year = date.getFullYear();

                // Format the date
                var formattedDate = monthNames[monthIndex] + " " + day;

                return formattedDate;
            }

            function Add1Day(date) {
                var date = new Date(date)
                var currentDate = date.getDate();
                date.setDate(currentDate + 1)
                return date;
            }

            // function Add1Day(date) {
            //     var date = new Date(date);
            //     date.setDate(date.getDate() + 1); // Add one day

            //     // Check if the new date is Saturday (6) or Sunday (0)
            //     if (date.getDay() === 6) { // Saturday
            //         date.setDate(date.getDate() + 2); // Skip to Monday
            //     } else if (date.getDay() === 0) { // Sunday
            //         date.setDate(date.getDate() + 1); // Skip to Monday
            //     }

            //     return date;
            // }

            function getHighestEFT(arrData) {
                var highestEFT = 0;
                arrData.forEach(v => {
                    highestEFT = Math.max(v.eft, highestEFT)
                })
                return highestEFT
            }


            var userData = {
                user: <?php echo json_encode($user); ?>
            };


            document.addEventListener("DOMContentLoaded", function () {
                fetch('/Scheduling_System_Folder/updated/api_manager.php', {
                    method: 'POST', // Assuming you want to use POST method
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(objData => {
                        var period = document.querySelector(".chart .chart-period");
                        var startDate = new Date(objData.startdate);
                        for (i = 0; i < getHighestEFT(objData.task); i++) {
                            var span = document.createElement("div");
                            span.innerText = formatDate(startDate);
                            startDate = Add1Day(startDate);
                            period.appendChild(span);
                        }
                        drawTile(objData);
                        //drawTile(objData);
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error);
                    });

            });

            function render() {
                var offset = 70;
                document.querySelectorAll(".days").forEach(function (li) {
                    var days = li.getAttribute("data-days")
                    var start = li.getAttribute("data-start")
                    li.style.width = (offset * days) + "px"
                    li.style.left = (offset * start) + "px"
                })
            }

            function drawTile(objData) {
                var task = objData.task;
                var criticalPath = objData.criticalPath;
                var finishedTask = objData.finished;
                var lateTask = objData.latetask;
                // Work with JSON data here
                var uniqueWorkers = new Set();

                worker = task.map(function (item) {
                    return item.worker;
                });

                worker.forEach(function (item) {

                    var workersArray = item.split(',');


                    workersArray.forEach(function (worker) {
                        uniqueWorkers.add(worker);
                    });
                });

                worker = Array.from(uniqueWorkers);

                document.querySelectorAll("#dynamicRowsContainer .chart-row:not([template])").forEach(function (item) {
                    item.remove();
                });


                worker.forEach(function (item) {
                    var chartRow = document.querySelector(".chart-row[template]").cloneNode(true)
                    chartRow.removeAttribute("template")
                    chartRow.id = "Worker" + item
                    chartRow.querySelector(".chart-row-item").innerHTML = item;
                    document.querySelector("#dynamicRowsContainer").appendChild(chartRow)
                })


                task.forEach(function (taskItem) {
                    var id = "Worker" + taskItem.worker
                    var chartRow = document.querySelector("#" + id)
                    var template = chartRow.querySelector(".days[template]").cloneNode(true)

                    if (finishedTask.includes(taskItem.name)) {
                        template.setAttribute("class", "days finished");
                    } else if(lateTask.includes(taskItem.name)){
                        template.setAttribute("class", "days late");
                    }
                    else {
                        if (criticalPath.includes(taskItem.name)) {
                            template.setAttribute("class", "days critical");
                        }
                    }
                    template.removeAttribute("template")
                    template.setAttribute("data-days", taskItem.duration)
                    template.setAttribute("data-start", taskItem.start)
                    template.innerHTML = `
                    <span class="overflowtext disblock" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Taskname: ${taskItem.name} Workers: ${taskItem.worker}">
                            ${taskItem.name}
                        </span>
                    <span class = "overflowtext">${taskItem.projectname}</span>`;
                    chartRow.querySelector(".chart-row-bars").appendChild(template)
                    console.log(template)
                })

                render()

                var today = new Date().toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric'
                });

                var divs = document.querySelectorAll('.chart-period > div');

                for (var i = 0; i < divs.length; i++) {
                    if (divs[i].textContent.trim() === today) {
                        var todayDiv = divs[i];
                        todayDiv.classList.add('datetoday');
                        console.log(todayDiv);
                    } else if (divs[i].classList.contains('datetoday')) {
                        divs[i].classList.remove('datetoday');
                    }
                }
                activateTooltip();
            }

        </script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"></script>
        <script src="bootstrap@5.3.3/bootstrap.bundle.min.js"></script>

    </div>
</body>

</html>