<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] != 'EMPLOYEE') {
    header("Location: ../sign_in_page.php");
    exit;
}
$user = $_SESSION["user"];
require ('../phplib.php');
$processData = processData();
$Usertasks = [];
$latetask = [];
date_default_timezone_set('Asia/Manila');
$dateToday = date("Y-m-d");
foreach ($processData["task"] as $item) {
    if ($item["worker"] === $user['Sname'] && $item["status"] === "Ongoing") {
        $Usertasks[] = $item;
    }
    if ($item["worker"] === $user['Sname'] && $item["status"] === "Ongoing" && $dateToday > $item['expectedDate']) {
        $latetask[] = $item['name'];
    }
}
usort($Usertasks, function ($a, $b) {
    return $a['start'] - $b['start'];
});

function formatDate($dateString)
{
    $date = DateTime::createFromFormat('Y-m-d', $dateString);
    $formattedDate = $date->format('M-d-Y');
    return $formattedDate;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchedSpace | Manager Dashboard</title>
    <link rel="shortcut icon" href="calendar-week-fill.svg" type="image/x-icon">
    <link rel="stylesheet" href="../gantt.css">
    <link rel="stylesheet" href="../stylesforall.css">
    <link rel="stylesheet" href="../bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .container {
            font-family: Verdana;
        }

        .tdcenter {
            vertical-align: middle;
        }

        .table-dark {
            --bs-table-bg: #31363F;
            --bs-table-border-color: #76ABAE;
        }

        .tasktoday {
            color: #76abae !important;
            font-size: 30px;
            font-weight: bold;
        }

        p {
            color: #76ABAE;
        }
    </style>
</head>

<body>
    <header>
        <div class="website-name item">
            <div class="sb-menu">
                <img id="toggleButton" class="sb-menu-but" onclick="toggleSideBar()" src=".././image/icons8-menu-32.png"
                    alt="Menu">
            </div>
            SchedSpace
        </div>
        <div class="company-name item">
            <?php
            echo $user['CompanyN'];
            ?>
        </div>
        <div class="rightHeader">
            <div class="notif-but" onclick="toggleNotification()">
                <img src=".././image/icons8-notification-28.png">
                <?php
                $latetaskCount = (!empty($latetask)) ? count($latetask) : 0; 
                $class = ($latetaskCount > 0) ? "activeNotif" : "Notif";
                ?>
                <div class="<?php echo $class; ?>"></div>
            </div>
            <div id="notifBox" class="notifBox" style="display: none;">
                <h5>Notification</h5>
                <div class="spaceBr"></div>
                <div class="notif">
                    <ul>
                        <li><img class="notif-icon" src=".././image/icons8-clock-21.png"><?php
                        $dateToday = strtotime(date("Y-m-d"));
                        foreach ($processData["task"] as $item) {
                            if ($item["worker"] === $user['Sname'] && $item["status"] === "Ongoing" && $dateToday > strtotime($item['expectedDate'])) {
                                $itemExpectedDate = strtotime($item['expectedDate']);
                                $diff = floor(($dateToday - $itemExpectedDate) / (60 * 60 * 24));
                                echo "Your task " . $item['name'] . " is late by " . $diff . ($diff > 1 ? " Days" : " Day") . "<br>";
                            }
                        }
                        ?></li>
                    </ul>
                </div>
            </div>
            <div class="dropdown item">
                <?php
                echo $user['Sname'];
                ?>
            </div>
    </header>


    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <ul>
            <li onclick="redirectToHome()"><img class="sb-icon" src=".././image/icons8-home-24.png">Home</li>
            <li onclick="redirectToUploadProject()"><img class="sb-icon" src=".././image/icons8-upload-22.png">Upload
                Project</li>
            <li onclick="redirectToUpdateTask()"><img class="sb-icon" src=".././image/icons8-update-20.png">Update Task
            </li>
            <li onclick="redirectToViewProjects()"><img class="sb-icon" src=".././image/icons8-view-22.png">My Tasks
            </li>
            <li><button id="logoutButton">Logout</button></li>
        </ul>
    </div>

    <!-- Main content area -->
    <div class="main-content">
        <!-- Content goes here -->
        <div class="home-content">
            <div class="container">
                <h1 class="label">Task Today</h1>
                <span style="font-size: 20px;">
                    <?php if (!empty($Usertasks[0]['name'])):
                        $prereqCount = count(explode(",", $Usertasks[0]['prereq'])); ?>
                        <table class="table table-dark table-bordered">
                            <thead>
                                <tr>
                                    <td rowspan="<?php echo $prereqCount + 2; ?>" class="tdcenter tasktoday">
                                        <?php echo $Usertasks[0]['name']; ?>
                                    </td>
                                    <td class="tdcenter">Duration</td>
                                    <td class="tdcenter">Start Date</td>
                                    <td class="tdcenter">Expected Finished Date</td>
                                    <td class="tdcenter">Priority</td>
                                    <td class="tdcenter">Description</td>
                                    <td class="tdcenter">Prerequisite/s</td>
                                    <td class="tdcenter">Prerequiste/s Worker</td>
                                </tr>
                                <tr>
                                    <td rowspan="<?php echo $prereqCount + 2; ?>" class="tdcenter">
                                        <?php echo $Usertasks[0]['duration']; ?>
                                    </td>
                                    <td rowspan="<?php echo $prereqCount + 2; ?>" class="tdcenter">
                                        <?php echo formatDate($Usertasks[0]['startDate']); ?>
                                    </td>
                                    <td rowspan="<?php echo $prereqCount + 2; ?>" class="tdcenter">
                                        <?php echo formatDate($Usertasks[0]['expectedDate']); ?>
                                    </td>
                                    <td rowspan="<?php echo $prereqCount + 2; ?>" class="tdcenter">
                                        <?php echo $Usertasks[0]['priority']; ?>
                                    </td>
                                    <td rowspan="<?php echo $prereqCount + 2; ?>" class="tdcenter">
                                        <?php echo $Usertasks[0]['description']; ?>
                                    </td>
                                </tr>
                                <?php if (!empty($Usertasks[0]['prereq'])) {
                                    $obj = [];
                                    $prereqArr = explode(",", $Usertasks[0]['prereq']);
                                    $filteredWorkers = array_filter($processData["task"], function ($item) use ($prereqArr) {
                                        return in_array($item["name"], $prereqArr);
                                    });
                                    $workers = array_map(function ($item) {
                                        return $item["worker"];
                                    }, $filteredWorkers);
                                    $prereqArray = explode(",", $Usertasks[0]['prereq']);
                                    $workers = array_values($workers);
                                    foreach ($prereqArray as $index => $key) {
                                        $obj[$key] = $workers[$index];
                                    }
                                    foreach ($obj as $key => $value) {
                                        echo "<tr>";
                                        echo "<td class='tdcenter'>$key</td>";
                                        echo "<td class='tdcenter'>$value</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr>";
                                    echo "<td>None</td>";
                                    echo "<td>N\A</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </thead>
                        </table>
                    <?php else: ?>
                        <p>No task available.</p>
                    <?php endif; ?>
                </span>


                <br><br><br>

                <h1 class="label">Next Task</h1>
                <span style="font-size: 20px;">
                    <?php if (!empty($Usertasks[1]['name'])):
                        $prereqCount = count(explode(",", $Usertasks[1]['prereq'])); ?>
                        <table class="table table-dark table-bordered">
                            <thead>
                                <tr>
                                    <td rowspan="<?php echo $prereqCount + 2; ?>" class="tdcenter tasktoday">
                                        <?php echo $Usertasks[1]['name']; ?>
                                    </td>
                                    <td class="tdcenter">Duration</td>
                                    <td class="tdcenter">Start Date</td>
                                    <td class="tdcenter">Expected Finished Date</td>
                                    <td class="tdcenter">Priority</td>
                                    <td class="tdcenter">Description</td>
                                    <td class="tdcenter">Prerequisite/s</td>
                                    <td class="tdcenter">Prerequiste/s Worker</td>
                                </tr>
                                <tr>
                                    <td rowspan="<?php echo $prereqCount + 2; ?>" class="tdcenter">
                                        <?php echo $Usertasks[1]['duration']; ?>
                                    </td>
                                    <td rowspan="<?php echo $prereqCount + 2; ?>" class="tdcenter">
                                        <?php echo formatDate($Usertasks[1]['startDate']); ?>
                                    </td>
                                    <td rowspan="<?php echo $prereqCount + 2; ?>" class="tdcenter">
                                        <?php echo formatDate($Usertasks[1]['expectedDate']); ?>
                                    </td>
                                    <td rowspan="<?php echo $prereqCount + 2; ?>" class="tdcenter">
                                        <?php echo $Usertasks[1]['priority']; ?>
                                    </td>
                                    <td rowspan="<?php echo $prereqCount + 2; ?>" class="tdcenter">
                                        <?php echo $Usertasks[1]['description']; ?>
                                    </td>
                                </tr>
                                <?php if (!empty($Usertasks[1]['prereq'])) {
                                    $obj = [];
                                    $prereqArr = explode(",", $Usertasks[1]['prereq']);
                                    $filteredWorkers = array_filter($processData["task"], function ($item) use ($prereqArr) {
                                        return in_array($item["name"], $prereqArr);
                                    });
                                    $workers = array_map(function ($item) {
                                        return $item["worker"];
                                    }, $filteredWorkers);
                                    $prereqArray = explode(",", $Usertasks[1]['prereq']);
                                    $workers = array_values($workers);
                                    foreach ($prereqArray as $index => $key) {
                                        $obj[$key] = $workers[$index];
                                    }
                                    foreach ($obj as $key => $value) {
                                        echo "<tr>";
                                        echo "<td class='tdcenter'>$key</td>";
                                        echo "<td class='tdcenter'>$value</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr>";
                                    echo "<td>None</td>";
                                    echo "<td>N\A</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </thead>
                        </table>
                    <?php else: ?>
                        <p>No task available.</p>
                    <?php endif; ?>
                </span>











            </div>
        </div>
    </div>







    <script>

        document.getElementById("logoutButton").addEventListener("click", function () {
            window.location.href = "../manager_logout.php";
        });
        // <!-- JavaScript to display the current date -->
        // Slidebar mechanism
        function toggleSideBar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("sidebar-close");
        }


        function redirectToHome() {
            window.location.href = 'employee_dashboard.php';
        }

        function redirectToUploadProject() {
            window.location.href = 'upload_project.php';
        }

        function redirectToViewProjects() {
            window.location.href = 'upload_project.php';
        }


        function toggleNotification() {
            var notif = document.getElementById("notifBox");
            if (notif.style.display === "none") {
                notif.style.display = "block";
            } else {
                notif.style.display = "none";
            }
        }

    </script>

</body>

</html>