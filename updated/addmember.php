<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] != 'MANAGER') {
    header("Location: sign_in_page.php");
    exit;
}

$user = $_SESSION["user"];
require_once "database.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';


function generateUniqueCode($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $code = '';
    $max = strlen($characters) - 1;

    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[mt_rand(0, $max)];
    }

    return $code;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchedSpace | Add Member</title>
    <link rel="shortcut icon" href="calendar-week-fill.svg" type="image/x-icon">
    <link rel="icon" href="rocket-takeoff-fill.svg" type="image/svg+xml">
    <link rel="stylesheet" href="stylesforall.css">
</head>

<body>
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

    <div class="main-content">
        <!-- Content goes here -->
        <div class="text">
            <h1>Add a Member</h1>
        </div>

        <form method="post">
            <div class="addform">
                <input type="text" name="Sname" class="form-control" placeholder="Employee Name" required>
                <br>
                <input type="text" name="Semail" class="form-control" placeholder="Employee Email" required>
                <br>
                <!-- <p>Member added successfully! An email has been sent to their registered <br>email address containing their default login credentials.</p> -->
                <br>
                <input type="submit" value="Add Member" name="addmember">
            </div>
        </form>

        <?php
         if (isset($_POST["addmember"])){
            $name = $_POST['Sname'];
            $email = $_POST['Semail'];
            $Uc = $user['Uc'];
            $CompanyN = $user['CompanyN'];
            $defaultPass = generateUniqueCode();
            $passwordHash = password_hash($defaultPass, PASSWORD_DEFAULT);
            $otpcode = 0;
            $subject = 'Account Default Password';

            $message = "Dear $name,<br><br>
            Your manager has successfully created your SchedSpace account. Your default password is: <div style='text-align: center;'><span style='font-size: 20px; font-weight: bold;'>$defaultPass</span></div><br>
            Please use this password to complete your account creation.<br><br>
            You can change it upon logging into your account<br><br>
            Best regards,<br>
            <span style='font-size: 24px; font-weight: bold; color: #76abae; font-family: Courier New;'>SchedSpace PMS</span>";


            $mail = new PHPMailer(true);

            $mail = new PHPMailer(true);
            $mail ->Host = 'smtp.gmail.com';
            $mail ->isSMTP();
            $mail->CharSet = "utf-8";
            $mail ->SMTPAuth = true;
            $mail ->Username = 'schedspace.pms@gmail.com';
            $mail ->Password = 'phxb pgdu inre wksg';
            $mail ->SMTPSecure = 'tls';
            $mail ->Port = 587;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                'allow_self_signed' => true
                )
            );
            $mail ->setFrom('schedspace.pms@gmail.com');
            $mail ->addAddress($email);
            $mail ->isHTML(true);
            $mail ->Subject = $subject;
            $mail ->Body = $message;

            if($mail ->send()){
                $sql = "SELECT * FROM sworkers";
                $result = mysqli_query($conn, $sql);
                if (!$result) {
                    die("Error: " . mysqli_error($conn)); 
                }

                $sql = "INSERT INTO sworkers (Sname, Semail, Spass, CompanyN, Uc, code) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                
                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $passwordHash, $CompanyN, $Uc, $otpcode);
                    mysqli_stmt_execute($stmt);
                    
                    echo "<div class='success'>Employee account was successfully created.</div>";
                } else {
                    die("Error: " . mysqli_error($conn)); 
            }
         }
        }
        ?>
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