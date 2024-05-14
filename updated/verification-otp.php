<?php
session_start(); 

function generateUniqueCode($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    $max = strlen($characters) - 1;

    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[mt_rand(0, $max)];
    }

    return $code;
}

$email = $_SESSION['email'];
$name = $_SESSION['name'];
$companyname = $_SESSION['companyname'];
$password = $_SESSION['password'];
$otp = $_SESSION['otp']; 
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$Uniquecode = generateUniqueCode();

require_once "database.php";
?>



<!DOCTYPE html>
<html>
<head>
    <title>SchedSpace | Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="calendar-week-fill.svg" type="image/x-icon">
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
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
            height: 120px;
        }

        .header-container h1 {
            justify-self: center;
            margin: 0;
            font-family: 'Courier New', monospace;
            color: #76ABAE; /* Light blue text color */
            font-size: 56px;
            font-weight: bold;
        }

        .login-container {
            align-content: center;
            justify-content: center;
            margin-top: 150px; /* Adjust the top margin */
            text-align: center;
        }
        
        body{
            background-color: #EEEEEE;
        }

        .verify-text{
            font-weight: light;
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
        }

        .otp-field {
            flex-direction: row;
            column-gap: 10px;
            display: flex;    
            align-items: center;
            justify-content: center;
        }

        .otp-field input {
            height: 45px;
            width: 42px;
            border-radius: 6px;
            outline: none;
            font-size: 1.125rem;
            text-align: center;
            border: 1px solid #ddd;
        }

        .otp-field input:focus {
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
        }

        .otp-field input::-webkit-inner-spin-button,
        .otp-field input::-webkit-outer-spin-button {
            display: none;
        }

        .resend {
            font-size: 12px;
        }

        .footer {
            position: absolute;
            bottom: 10px;
            right: 10px;
            color: black;
            font-size: 12px;
            text-align: right;
            font-family: monospace;
        }

        .footer a {
            color: black;
            text-decoration: none;
        }

        .errors{
            color: red;
        }

        .success{
            color: green;
        }

    </style>
</head>
<body>

<div class="header-container">
    <div class="header-container h1">
        <h1>SchedSpace PMS</h1>
    </div>
</div>

<div class="login-container">
    <div class="verify-text">
        
        <section class="container-fluid bg-body-tertiary d-block">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6 col-lg-4" style="min-width: 500px;">
                  <div class="card bg-white mb-5 mt-5 border-0" style="box-shadow: 0 12px 15px rgba(0, 0, 0, 0.02);">
                    <div class="card-body p-5 text-center">
                      <h4>VERIFY EMAIL</h4>
                      <p>A verification code has been sent to your email <?php echo $email;?>.<br>Enter the code below to verify your email and finish creating your account.</p>
                      <form action="verification-otp.php" method="post">
                      <div class="otp-field mb-4">
                      <input type="number" name="digit1" />
                      <input type="number" name="digit2" disabled />
                      <input type="number" name="digit3" disabled />
                      <input type="number" name="digit4" disabled />
                      <input type="number" name="digit5" disabled />
                      <input type="number" name="digit6" disabled />
                      </div>


                        <?php 
                        
                        if(isset($_POST['Verify'])){
                          $digit1 = $_POST['digit1'];
                          $digit2 = $_POST['digit2'];
                          $digit3 = $_POST['digit3'];
                          $digit4 = $_POST['digit4'];
                          $digit5 = $_POST['digit5'];
                          $digit6 = $_POST['digit6'];
                          $userotp = intval($digit1 . $digit2 . $digit3 . $digit4 . $digit5 . $digit6);
                          if($userotp === $otp){
                              $otpcode = 0;
                              $sql = "SELECT * FROM smanagers";
                              $result = mysqli_query($conn, $sql);
                              
                              if (!$result) {
                                  die("Error: " . mysqli_error($conn)); // Handle query error gracefully
                              }
                              
                              $sql = "INSERT INTO smanagers (Mname, Memail, Mpass, CompanyN, Uc, code) VALUES (?, ?, ?, ?, ?, ?)";
                              $stmt = mysqli_stmt_init($conn);
                              
                              if (mysqli_stmt_prepare($stmt, $sql)) {
                                  mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $passwordHash, $companyname, $Uniquecode, $otpcode);
                                  mysqli_stmt_execute($stmt);
                                  
                                  echo "<div class='success'>You are registered successfully.</div>";
                                  echo "<script>setTimeout(function() { window.location.href = 'sign_in_page.php'; }, 2000);</script>";
                              } else {
                                  die("Error: " . mysqli_error($conn)); // Handle prepared statement error gracefully
                              }
                          } else {
                              echo "<div class='errors'>Invalid OTP</div>";
                          }
                      }


                        ?>

                      <br>
                      <button class="btn btn-primary mb-3" name ="Verify">
                        Verify
                      </button>
                      </form>
                    
                      <!-- <p class="resend text-muted mb-0">
                        Didn't receive a code? <a href="">Request again.</a>
                      </p> -->
                    </div>
                  </div>
                </div>
              </div>
          </section>
        
        </div>
</div>

<script>
const inputs = document.querySelectorAll(".otp-field > input");
const button = document.querySelector(".btn");

window.addEventListener("load", () => inputs[0].focus());
button.setAttribute("disabled", "disabled");

inputs[0].addEventListener("paste", function (event) {
  event.preventDefault();

  const pastedValue = (event.clipboardData || window.clipboardData).getData(
    "text"
  );
  const otpLength = inputs.length;

  for (let i = 0; i < otpLength; i++) {
    if (i < pastedValue.length) {
      inputs[i].value = pastedValue[i];
      inputs[i].removeAttribute("disabled");
      inputs[i].focus;
    } else {
      inputs[i].value = ""; // Clear any remaining inputs
      inputs[i].focus;
    }
  }
});

inputs.forEach((input, index1) => {
  input.addEventListener("keyup", (e) => {
    const currentInput = input;
    const nextInput = input.nextElementSibling;
    const prevInput = input.previousElementSibling;

    if (currentInput.value.length > 1) {
      currentInput.value = "";
      return;
    }

    if (
      nextInput &&
      nextInput.hasAttribute("disabled") &&
      currentInput.value !== ""
    ) {
      nextInput.removeAttribute("disabled");
      nextInput.focus();
    }

    if (e.key === "Backspace") {
      inputs.forEach((input, index2) => {
        if (index1 <= index2 && prevInput) {
          input.setAttribute("disabled", true);
          input.value = "";
          prevInput.focus();
        }
      });
    }

    button.classList.remove("active");
    button.setAttribute("disabled", "disabled");

    const inputsNo = inputs.length;
    if (!inputs[inputsNo - 1].disabled && inputs[inputsNo - 1].value !== "") {
      button.classList.add("active");
      button.removeAttribute("disabled");

      return;
    }
  });
});

</script>

</body>
</html>
