<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

$email = "";
$name = "";
$companyname = "";
$pass = "";
$confirmpass = "";
$code = "";

session_start(); 
$errors = array();
if(isset($_POST['submit']))
{   $name = $_POST['Mname'];
    $email = $_POST['Memail'];
    $companyname = $_POST['CompanyN'];
    $pass = $_POST['Mpass'];
    $confirmpass = $_POST['MpasscConfirm'];

    if (strlen($pass)<8) {
        array_push($errors,"Password must be at least 8 characters long");
       }
    
       if (empty($email) || empty($name) || empty($companyname)  || empty($pass)  || empty($confirmpass)) {
        array_push($errors,"All fields are required!");
      }
    
       if ($pass!==$confirmpass) {
        array_push($errors,"Password does not match");
       }
    
           if (count($errors) > 0) {
            foreach ($errors as $error) {
                //echo "<div class='errors'>$error</div>";
            }
         }
            else{

                $errors = array();


                        $code = rand(999999,111111);
                        $subject = 'Email Verification Code';
                        $message = "Dear $name,<br><br>
                        Thank you for registering with us. Your verification code is: <div style='text-align: center;'><span style='font-size: 20px; font-weight: bold;'>$code</span></div><br>
                        Please use this code to complete the verification process.<br><br>
                        If you did not request this verification code, please ignore this email.<br><br>
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


                        $message = "We've sent a verification code to your Email <br> $email";
                        $_SESSION['message'] = $message;

                        if($mail ->send()){
                            $message = "We've sent a verification code to your Email <br> $email";
                            $_SESSION['message'] = $message;
                            $_SESSION['email'] = $_POST['Memail'];
                            $_SESSION['name'] = $_POST['Mname'];
                            $_SESSION['companyname'] = $_POST['CompanyN'];
                            $_SESSION['password'] = $_POST['Mpass'];
                            $_SESSION['otp'] = $code;

                            header('location: verification-otp.php');
                        }
                        else{
                            array_push($errors, "Failed sending verification code. Check your internet connection");
                        }
        }
}
        

         

               

?>








<!DOCTYPE html>
<html>
<head>
    <title>SchedSpace | Sign Up</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="calendar-week-fill.svg" type="image/x-icon">
    <style>
        .header-container {
            position: fixed;
            top: 0;
            left: 0;
            margin: 0;
            width: 100%;
            background-color: #31363F; /* Dark blue-gray background */
            padding: 10px 0; /* Padding adjusted for links */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            z-index: 1000; /* Ensure it's above other content */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100px;
        }

        .header-container h1 {
            justify-self: center;
            margin: 0;
            font-family: 'Courier New', monospace;
            color: #76ABAE; /* Light blue text color */
            font-size: 56px;
        }

        .plogintext {
            display: flex;
            flex-wrap: wrap;
            color: black;
            font-size: 10px; /* Adjust the font size as needed */
            font-family: "Verdana", Times, serif;
            margin-top: 150px; /* Adjust the top margin */
            justify-content: center;
        }

        .login-container {
            align-content: center;
            justify-content: center;
            margin-top: 20px; /* Adjust the top margin */
            text-align: center;
        }

        input[type="text"],
        input[type="password"] {
            border-radius: 20px;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            width: 280px; /* Adjust width */
            display: inline-block;
            margin-bottom: 10px; /* Adjust margin */
        }

        input[type="checkbox"] {
            margin-top: 10px;
            display: inline-block;
            vertical-align: middle;
        }


        input[type="submit"] {
            padding: 10px 30px; /* Adjust the padding as needed */
            border-radius: 20px;
            font-size: 16px;
            background-color: rgb(25, 106, 95); /* Green background color for Submit button */
            color: white; /* White text color */
            border: none; /* Remove border */
            cursor: pointer; /* Add cursor on hover */
            margin-top: 20px; /* Adjust the top margin */
        }

        input[type="submit"]:hover {
            box-shadow: 0 1.5px 5px rgba(0, 0, 0, 1);
        }

        .forgot-password {
            font-size: 14px; /* Adjust the font size as needed */
            color: blue; /* Blue color for the link */
            text-decoration: underline; /* Underline the link */
            margin-top: 10px; /* Adjust the top margin */
            padding: 10px;
        }
        
        h5 {
            font-family: "Verdana", Times, serif;
            margin: 0;
            display: inline-block;
            justify-self: center;
        }

        .have-account {
            font-family: "Verdana", Times, serif;
            font-size: 14px; /* Adjust the font size as needed */
            color: blue; /* Blue color for the link */
            text-decoration: underline; /* Underline the link */
            margin-top: 10px; /* Adjust the top margin */
            padding: 10px;
        }
        
        body{
            background-color: #EEEEEE;
        }
        .errors{
            color: red;
        }

        @media only screen and (max-width: 470px){
            .header-container{
                height: 50px;
            }
            
            .header-container h1{
                font-size: 30px;
            }

            .plogintext{
                margin-top: 100px;
            }

            input[type="text"],
            input[type="password"] {
            width: 220px; /* Adjust width */
            display: inline-block;
            margin-bottom: 10px; /* Adjust margin */
        }
        }


    </style>
</head>
<body>
<
<div class="header-container">
    <div class="header-container h1">
        <h1>SchedSpace PMS</h1>
    </div>
</div>

<div class="plogintext">
    <h1>CREATE ACCOUNT</h1>
</div>
<form action="sign_up.php" method="post">
<div class="login-container">
    <div class="punamepass">
        <?php 
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo "<div class='errors'>$error</div>";
                }
             }
        ?>
        <input type="text" name="Mname" class="form-control" placeholder="Name">
        <br>
        <input type="text" name="Memail" class="form-control" placeholder="Email">
        <br>
        <input type="text" name="CompanyN" class="form-control" placeholder="Company Name">
        <br>
        <input type="password" name="Mpass" class="form-control" placeholder="Password">
        <br>
        <input type="password" name="MpasscConfirm" class="form-control" placeholder="Confirm Password">
        <br>
        <input type="submit" name = "submit">
        <br><br>
        <h5>Already Have an Account?<a href="sign_in_page.php" class="have-account">Sign In.</a></h5>
        <br>
        </div>
</div>

</form>



</body>
</html>