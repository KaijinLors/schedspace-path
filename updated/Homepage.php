<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchedSpace</title>
    <link rel="shortcut icon" href="calendar-week-fill.svg" type="image/x-icon">
    
    <style>
        /* Global Styles */
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #222831; /* Dark gray-blue background */
            color: #EEEEEE; /* Light gray text color */
            text-align: center;
        }

        /* Header Styles */
        .header-container {
            position: fixed;
            top: 0;
            left: 0;
            margin: 0;
            width: 100%;
            background-color: #31363F; /* Dark blue-gray background */
            padding: 10px 20px; /* Padding adjusted for links */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            z-index: 1000; /* Ensure it's above other content */
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            height: 100px;
        }

        .header-container h1 {
            margin: 0;
            font-family: 'Courier New', monospace;
            color: #76ABAE; /* Light blue text color */
            font-size: 56px;
        }

        .header-links {
            display: flex;
            margin-right: 50px; /* Move the links 50px to the left */
        }

        .header-links a {
            color: #76ABAE; /* Light blue text color */
            text-decoration: none; /* Remove underline */
            margin-left: 30px; /* Adjust spacing between links */
        }

        .header-links a:hover {
            color: #3ca49c; /* Darker blue on hover */
        }

        /* Body Styles */
        .body {
            margin-top: 190px; /* Adjusted top margin */
        }

        .body h1 {
            font-size: 52px;
            margin-bottom: 10px;
        }

        .body h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #EEEEEE; /* Light gray text color */
        }

        .body h3 {
            font-size: 18px;
            color: #3ca49c; /* Green text color */
        }

        .body button {
            background-color: rgb(25, 106, 95); /* Light blue button background */
            color: white; /* White button text color */
            padding: 10px 20px; /* Padding for button */
            border: none; /* No border */
            border-radius: 5px; /* Rounded corners */
            font-size: 16px; /* Button text size */
            cursor: pointer; /* Cursor style */
            transition: background-color 0.3s; /* Smooth transition */
            margin-top: 20px; /* Margin at the top */
        }

        .body button:hover {
            background-color: #5C8991; /* Darker blue on hover */
        }

        .creators-images {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }

        .creator-container {
            text-align: center;
            margin: 10px;
        }

        .creator-container img {
            width: 200px;
            height: 200px;
        }

        .creator-container p {
            font-size: 16px;
            margin-top: 5px;
            font-weight: bold;
        }

        .p{
            font-size: 16px;
        }

        @media only screen and (max-width: 470px){
            .header-container{
                height: 50px;
            }
            
            .header-container h1{
                font-size: 30px;
            }

            .header-links {
                margin-right: 20px;
            }

            .header-links a {
                margin-left: 15px;
            }

            .body {
                margin-top: 140px;
            }

            .body h1 {
                font-size: 40px;
            }

            .body h2 {
                font-size: 20px;
            }

            .body h3 {
                font-size: 16px;
            }

            .body button {
                font-size: 14px;
            }

            .creators-images img {
                width: 150px;
            }

            .body h4{
                font-size: 12px;
                font-weight: none;
            }
        }
    </style>
</head>
<body>

<div class="header-container">
    <h1>SchedSpace PMS</h1>
    <div class="header-links">
        <a href="#" id="aboutLink">About</a>
        <a href="sign_in_page.php">Sign In</a>
    </div>
</div>

<div class="body" id="bodyContent">
    <h1>SchedSpace.com</h1>
    <h2>Project Management System</h2>
    <h3>Good Day!</h3>
    <button onclick="location.href='sign_up.php'" type="button">Sign Up as Project Manager</button>
</div>

<script>
    let aboutContent = `
            <h1>About SchedSpace PMS</h1>
            <br>
            <p>SchedSpace PMS (Project Management System) is a Web-based Project Management Scheduling System designed to automate and optimize your project management processes.</p>
            <p>It gives way to create schedules with minimal to zero delay in terms of scheduling the designated projects and tasks. </p>
            <p>SchedSpace uses algorithms such as the Critical Path Method (CPM) and Ant Colony Optimization (ACO) to path out the optimized schedules.</p>
            <p>The main audience for this PMS are Project Managers working in companies that work on multiple projects, tasks, and workers.</p>
            <br><br>
            <h2>Creators:</h2>
            <div class="creators-images">
                <div class="creator-container">
                    <img src="./image/creator1.jpg" alt="Creator 1">
                    <p>Mark Laurence S. Seron</p>
                    <h4>(Lead Back-End Programmer)</h4>
                </div>
                <div class="creator-container">
                    <img src="./image/creator2.jpg" alt="Creator 2">
                    <p>Marianne Gayle R. Esguerra</p>
                    <h4>(Lead Front-End Programmer)</h4>
                </div>
                <div class="creator-container">
                    <img src="./image/creator3.jpg" alt="Creator 3">
                    <p>Shan Ariz R. Forca</p> 
                </div>
                <div class="creator-container">
                    <img src="./image/creator4.jpg" alt="Creator 4">
                    <p>Jamira Thania A. Ursaga</p>
                    <h4>(Assisting Front-End Programmer)</h4>
                </div>
                <div class="creator-container">
                    <img src="./image/creator5.jpg" alt="Creator 5">
                    <p>Jasmine Kaye M. Quintao</p>
                </div>
            </div>
        `;

    let defaultContent = `
            <h1>SchedSpace.com</h1>
            <h2>Project Management System</h2>
            <h3>Good Day!</h3>
            <button onclick="location.href='signup.html'" type="button">Sign Up as Project Manager</button>
        `;

    let isAboutVisible = false;

    document.getElementById("aboutLink").addEventListener("click", function(event){
        event.preventDefault(); 

        if (isAboutVisible) {
            document.getElementById("bodyContent").innerHTML = defaultContent;
            isAboutVisible = false;
        } else {
            document.getElementById("bodyContent").innerHTML = aboutContent;
            isAboutVisible = true;
        }
    });
</script>


</body>
</html>