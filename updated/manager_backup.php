<?php

require_once "database.php";
$tasksData = [];
$highestEFT = 0;
$sql = "SELECT * FROM stask JOIN sworkers ON sworkers.Id = stask.Userid";


$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $taskName = $row["Taskn"];
        $duration = (int) $row["Due"];
        $workerName = $row["Sname"];
        $workerId = $row['Userid'];
        $lst = (int) $row["LST"];
        $est = (int) $row["EST"];
        $lft = (int) $row["LFT"];
        $eft = (int) $row["EFT"];
        $prereq = $row["Prerequisite"];
        $Projectid = $row['Projectid'];
        $prerequisites = explode(",", $row["Prerequisite"]); // Split prerequisites into an array
        $prerequisitesArray = !empty($prereq) ? explode(",", $prereq) : [];
        $tasksData[] = [
            "worker" => $workerName,
            "taskname" => $taskName,
            "duration" => $duration,
            "prerequisites" => $prereq,
            "est" => $est,
            "lst" => $lst,
            "lft" => $lft,
            "eft" => $eft,
            "due" => $duration,
        ];   

        if ($eft > $highestEFT) {
            $highestEFT = $eft;
            $lastTask = $taskName;
        }

    }
}







?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchedSpace</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="gantt.css">
    <link href="/website/css/uicons-outline-rounded.css" rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <div class="logo-details">
            <span class="logo_name">SchedSpace</span>
        </div>
        <ul class="nav-links">
            <li>
                <a href="#" class="active">
                    <span class="links_name">Home</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="links_name">Upload Project</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="links_name">Update Task</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="links_name">View Projects</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="links_name">Add Member</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="links_name">Delete Member</span>
                </a>
            </li>
            <li class="log_out">
                <a href="#">
                    <span class="links_name">Log out</span>
                </a>
            </li>
        </ul>
    </div>
    <section class="home-section">
        <nav>
            <div class="profile-details">
                <img src="./image/sample_profile.jpg" alt="">
                <span class="membername">Mark Laurence Seron</span>
            </div>
        </nav>
        <div class="home-content">
            <div class="container">
                <h1 class="label">Dash Board</h1>
                <div class="chart">
                    <div class="chart-row chart-period">
                        <div class="chart-row-item"></div>
                        <?php
                        $currentDate = strtotime(date('Y-m-d')); // Get current date in UNIX timestamp format
                        
                        for ($i = 1; $i <= $highestEFT; $i++) {
                            $date = date('M d', strtotime("+$i days", $currentDate));
                            echo '<span>' . $date . '</span>';
                        }
                        ?>
                    </div>
                    <div class="chart-row chart-lines">
                        <?php

                        for ($i = 1; $i <= $highestEFT + 1; $i++) {
                            echo '<span></span>';
                        }
                        ?>
                    </div>
                    <div id="dynamicRowsContainer" class="chart-dynamic-rows">
                        <div class="chart-row" template>
                            <div class="chart-row-item">lors</div>
                            <ul class="chart-row-bars">
                                <li class="days" template></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>

  

<?php

$task = [];
$critp = [];




foreach($tasksData as $taskName){
    $task[] = [
        'name' => $taskName['taskname'],
        'worker' => $taskName['worker'],
        'duration' => $taskName['duration'],
        'start' => $taskName['est'],
        'critical' => 0,
        'prereq' => $taskName['prerequisites'],
    ];
}





?>


var task = <?php echo json_encode($task); ?>;
var criticalPath = <?php echo json_encode($critp); ?>;

document.addEventListener("DOMContentLoaded", function () {

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
    template.setAttribute("class", "days " + (criticalPath.includes(taskItem.name) ? "days critical" : ""));
    template.removeAttribute("template")
    template.setAttribute("data-days", taskItem.duration)
    template.setAttribute("data-start", taskItem.start)
    template.innerHTML = taskItem.name
    chartRow.querySelector(".chart-row-bars").appendChild(template)
    console.log(template)
})

render()


});





function render() {
document.querySelectorAll(".days").forEach(function (li) {
    var days = li.getAttribute("data-days")
    var start = li.getAttribute("data-start")
    li.style.width = (50 * days) + "px"
    li.style.left = (50 * start) + "px"
})
}



</script>
</body>

</html>