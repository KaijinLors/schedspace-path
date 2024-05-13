<!DOCTYPE html>
<html>

<head>
    <title>SchedSpace | Sign In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="calendar-week-fill.svg" type="image/x-icon">
    <style>
        .header-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #31363F;
            /* Dark blue-gray background */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
            z-index: 1000;
            /* Ensure it's above other content */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 120px;
        }

        .header-container h1 {
            margin: 0;
            font-family: 'Courier New', monospace;
            color: #76ABAE;
            /* Light blue text color */
            font-size: 56px;
        }

        body {
            background-color: #EEEEEE;
        }

        .plogintext {
            display: flex;
            flex-wrap: wrap;
            color: black;
            font-size: 12px;
            /* Adjust the font size as needed */
            font-family: "Verdana", Times, serif;
            margin-top: 170px;
            /* Adjust the top margin */
            justify-content: center;
        }

        .login-container {
            align-content: center;
            justify-content: center;
            margin-top: 25px;
            /* Adjust the top margin */
            text-align: center;
        }

        input[type="text"],
        input[type="password"] {
            border-radius: 20px;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            width: 280px;
            /* Adjust width */
            display: inline-block;
            margin-bottom: 10px;
            /* Adjust margin */
        }

        input[type="checkbox"] {
            margin-top: 10px;
            display: inline-block;
            vertical-align: middle;
        }

        input[type="submit"] {
            padding: 10px 30px;
            /* Adjust the padding as needed */
            border-radius: 20px;
            font-size: 16px;
            background-color: rgb(25, 106, 95);
            /* Green background color for Submit button */
            color: white;
            /* White text color */
            border: none;
            /* Remove border */
            cursor: pointer;
            /* Add cursor on hover */
            margin-top: 20px;
            /* Adjust the top margin */
        }

        .forgot-password {
            font-family: "Verdana", Times, serif;
            font-size: 14px;
            /* Adjust the font size as needed */
            color: blue;
            /* Blue color for the link */
            text-decoration: underline;
            /* Underline the link */
            margin-top: 10px;
            /* Adjust the top margin */
            padding: 10px;
        }

        @media only screen and (max-width: 470px) {
            .header-container {
                height: 50px;
            }

            .header-container h1 {
                font-size: 30px;
            }

            .plogintext {
                margin-top: 100px;
            }

            input[type="text"],
            input[type="password"] {
                width: 220px;
                /* Adjust width */
                display: inline-block;
                margin-bottom: 10px;
                /* Adjust margin */
            }
        }

        .alert {
            color: red;
        }
    </style>
</head>

<body>

    <div class="header-container">
        <h1>SchedSpace PMS</h1>
    </div>


    <div class="plogintext">
        <h1>SIGN IN</h1>
    </div>

    <div class="login-container">
        <?php
        session_start();
        require_once "database.php";
        if (isset($_POST["login"])) {
            $email = $_POST["Memail"];
            $pass = $_POST["Mpass"];
            $sql_manager = "SELECT * FROM smanagers WHERE Memail = '$email'";
            $result_manager = mysqli_query($conn, $sql_manager);
            $user_manager = mysqli_fetch_array($result_manager, MYSQLI_ASSOC);

            if ($user_manager) {
                if (password_verify($pass, $user_manager["Mpass"])) {
                    $_SESSION["user"] = $user_manager;
                    $_SESSION["role"] = "MANAGER";
                    header("Location: manager_dashboard.php");
                    die();
                }
            } else {
                $sql_worker = "SELECT * FROM sworkers WHERE Semail = '$email'";
                $result_worker = mysqli_query($conn, $sql_worker);
                $user_worker = mysqli_fetch_array($result_worker, MYSQLI_ASSOC);
                if ($user_worker) {
                    if (password_verify($pass, $user_worker["Spass"])) {
                        $_SESSION["user"] = $user_worker;
                        $_SESSION["role"] = "EMPLOYEE";
                        header("Location: employeefiles/employee_dashboard.php");
                        die();
                    }
                }
            }
            echo "<div class='alert'>Invalid Credentials</div>";
        }
        ?>
        <form method="post" action="">
            <div class="punamepass">
                <input type="text" name="Memail" class="form-control" placeholder="Email">
                <br>
                <input type="password" name="Mpass" class="form-control" placeholder="Password">
                <br>
                <input type="checkbox" id="rememberMe" name="rememberMe">
                <label for="rememberMe">Remember Me</label>
                <br>
                <input type="submit" value="Login" name="login">
                <br><br>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>
        </form>
    </div>

</body>

</html>