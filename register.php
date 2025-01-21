<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $retype_password = trim($_POST['retype_password']);
    $section = trim($_POST['section']);
    $guro = trim($_POST['guro']);
    $timestamp = date('Y-m-d H:i:s');

    // Validate fields
    if (empty($username) || empty($fullname) || empty($email) || empty($password) || empty($retype_password) || empty($section) || empty($guro)) {
        die("All fields are required.");
    }

    if ($password !== $retype_password) {
        die("Passwords do not match.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into the database
    $sql = "INSERT INTO users (username, fullname, email, password, section, guro, timestamp) VALUES (:username, :fullname, :email, :password, :section, :guro, :timestamp)";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            ':username' => $username,
            ':fullname' => $fullname,
            ':email' => $email,
            ':password' => $hashed_password,
            ':section' => $section,
            ':guro' => $guro,
            ':timestamp' => $timestamp
        ]);
        
        // Display JavaScript alert instead of echo
        echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DunongLingo</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="icon" href="logo.png" type="image/x-icon">
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'RINRoundProBold';
        }
              
        html {
            overflow: hidden; 
            overflow-y: scroll; 
        }

        html::-webkit-scrollbar {
            display: none; 
        }

        html {
            scrollbar-width: none; 
        }

        html {
            -ms-overflow-style: none; 
        }

        @font-face {
            font-family: 'RINRoundPro';
            src: URL('font-family/DINRoundPro.ttf') format('truetype');
        }

        @font-face {
            font-family: 'RINRoundProBold';
            src: URL('font-family/DINRoundPro-Bold.ttf') format('truetype');
        }


        @import url(https://db.onlinewebfonts.com/c/14936bb7a4b6575fd2eee80a3ab52cc2?family=Feather+Bold);

        @font-face {
            font-family: "Feather Bold";
            src: url("https://db.onlinewebfonts.com/t/14936bb7a4b6575fd2eee80a3ab52cc2.eot");
            src: url("https://db.onlinewebfonts.com/t/14936bb7a4b6575fd2eee80a3ab52cc2.eot?#iefix")format("embedded-opentype"),
                url("https://db.onlinewebfonts.com/t/14936bb7a4b6575fd2eee80a3ab52cc2.woff2")format("woff2"),
                url("https://db.onlinewebfonts.com/t/14936bb7a4b6575fd2eee80a3ab52cc2.woff")format("woff"),
                url("https://db.onlinewebfonts.com/t/14936bb7a4b6575fd2eee80a3ab52cc2.ttf")format("truetype"),
                url("https://db.onlinewebfonts.com/t/14936bb7a4b6575fd2eee80a3ab52cc2.svg#Feather Bold")format("svg");
        }

        h1 {
            font-weight: 700;
            font-family: 'Feather Bold';
        }

        title {
            font-size: 2px;
        }

        :root {
            --main-color: #58cc07;
            --second-color: #03a9f4;
            --warningColor: #cc0717;
            --darkMain-color: #419b01;
            --darkSecond-color: #0382bd;
            --darkWarning-Color: #80040fc0;
            --lightMain-color: #61df07;
            --lightSecond-color: #15b5ff;
            --lightWarning-color: #e60b1d;
            
        }

        header {
            height: 10vh;
            position: fixed;
            width: 100%;
            padding: 0% 5%;
            display: flex;
            align-items: center;
            background-color: var(--main-color);
            justify-content: space-between;
            z-index: 1;
        }

        header a {
            text-decoration: none;
        }

        header h1 {
            color: white;
            font-size: 2rem;
            font-family: 'Feather Bold';
        }

        #bar {
            color: white;
            display: none;
            cursor: pointer;
        }

        #close {
            display: none;
            cursor: pointer;
        }

        header nav {
            display: flex;
            justify-content: space-between;
        }

        header nav a {
            color: white;
            text-decoration: none;
            padding: 10px 30px;
            border-radius: 20px;
            transition: 0.5s ease;
            margin: 0px 5px;

        }

        header nav a:hover {
            background-color: white;
            color: var(--main-color);
        }

        .contact-page{
            padding-top: 10vh;
            height: 90vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0% 20%;
        }

        form h1{
            padding-bottom: 5%;
        }

        form{
            
            width: 80%;
            height: 80%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 0% 10%;
            margin: 5vh 5vw;
            border-radius: 20px;
        }

        form input{
            width: 100%;
            padding: 10px;
            outline: none;
            resize: none;
            border-radius: 10px;
            border: 1px solid black ;
        }

        .main-button-container {
            margin-top: 5%;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }

        .main-button-container button {
            width: 100%;
            text-align: center;
            padding: 20px;
            text-transform: uppercase;
            text-decoration: none;
            color: white;
            font-weight: bold;
            border-radius: 20px;
            transition: 0.5s ease;
            border: none;
            cursor: pointer;
        }

        .main-button-container button:nth-of-type(1) {
            background-color: var(--main-color);
            border-bottom: 5px solid var(--darkMain-color);
        }

        .main-button-container button:nth-of-type(1):hover {
            background-color: var(--lightMain-color);
        }

        .main-button-container button:nth-of-type(1):active {
            border: none;
        }

        .main-button-container button:nth-of-type(2) {
            background-color: var(--warningColor);
            border-bottom: 5px solid var(--darkWarning-Color);
        }

        .main-button-container button:nth-of-type(2):hover {
            background-color: var(--lightWarning-color);
        }

        .main-button-container button:nth-of-type(2):active {
            border: none;
        }

        footer {
            height: 10vh;
            display: flex;
            justify-content: center;
            flex-direction: column;
            background-color: var(--main-color);
            color: white;
            gap: 10px;
            padding: 0% 5%;
        }


        footer div:nth-of-type(1) {
            display: flex;
            gap: 20px;
        }

        footer div:nth-of-type(1) a {
            color: white;
            text-decoration: none;
        }

        footer div:nth-of-type(1) a:hover {
            text-decoration: underline;
        }

        @media (min-width: 1025px) {

            #bar,
            #close {
                display: none !important;
            }

            header nav {
                display: flex !important;
                position: static;
                height: auto;
                width: auto;
                flex-direction: row;
                justify-content: flex-end;
                align-items: center;
            }
        }

        @media (max-width: 1024px) {
            header {
                height: 7.5vh;
            }

            #bar {
                display: block;
                font-size: 1.5rem;
            }

            #close {
                display: none;
                z-index: 2;
                font-size: 1.5rem;
            }

            header h1 {
                font-size: 1.5rem;
            }

            header nav {
                display: flex;
                position: fixed;
                top: 0;
                left: -200vw;
                background-color: var(--main-color);
                flex-direction: column;
                justify-content: center;
                height: 100vh;
                width: 100%;
                text-align: center;
                align-items: center;
            }

            header nav a {
                color: white;
                width: 50%;
                padding: 30px;
                font-size: 2rem;
            }


            header nav a:hover {
                background-color: var(--main-color);
                color: white;
            }

            .contact-page{
                margin: 0% 5%;
            }

            form{
            
                width: 80%;
                height: 80%;
            
                padding: 0% 5%;
                margin: 5vh 10vw;
            }
            
            form h1{
                padding-bottom: 5%;
            }

            .main-button-container {
                align-items: center;
                gap: 10px;
            }

            .main-button-container button {
                width: 100%;
                padding: 20px;
                font-size: 1.2rem;
            }


            footer {
                margin-top: 5vh;
                height: 5vh;
            }

            footer div:nth-of-type(1) {
                display: none;
            }
        }

        @media (max-width: 767px) {
            header {
                height: 7.5vh;
            }

            #bar {
                font-size: 1rem;
            }

            #close {

                font-size: 1rem;
            }

            header h1 {
                font-size: 1rem;
            }

            form{
                width: 100%;
                height: 100%;
                padding:5% 10%;
                margin: 0vh 0vw;
            }

            form h1{
                font-size: 1.5rem;
            }

            header nav a {
                padding: 20px;
                font-size: 1.5rem;
            }

            .main-button-container {
                align-items: center;
                flex-direction: column;
                gap: 10px;
            }

            .main-button-container button {
                width: 100%;
                font-size: 1rem;
                padding: 15px;
            }

        }

        @media (max-width: 480px) {}
    </style>
</head>

<body>

    <header>
        <a href="index.php">
            <h1>DunongLingo</h1>
        </a>

        <i id="bar" class="fa-solid fa-bars"></i>
        <i id="close" class="fa-solid fa-x"></i>

        <nav id="navbar">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
        </nav>
    </header>

    <div class="contact-page">

    <form action="" method="POST">
        <h1>Mag Register</h1>
        
        <form method="POST" action="">
        <input type="text" name="username" placeholder="Username: " required>

        <input type="text" name="fullname" placeholder="Full Name: " required>

        <input type="email" name="email" placeholder="Email: " required>

        <input type="text" name="section" placeholder="Section: " required>

        <input type="text" name="guro" placeholder="Guro: " required>

        <input type="password" name="password" placeholder="Password: " required>

        <input type="password" name="retype_password" placeholder="Re-Type Password: " required>

        <div class="main-button-container">
            <button type="submit">Register</button>
        </div>

        <p>May account na? <a href="login.php">Login</a></p>
    </form>
        
        
</div>



    <footer>
        <div>
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
            <a href="register.php">Mag Register</a>
            <a href="login.php">Mag Login</a>
        </div>
        <div>Dunonglingo © 2025</div>
    </footer>


    <script>
        const openBtn = document.getElementById("bar");
        const closeBtn = document.getElementById("close");
        const navbar = document.getElementById("navbar");


        openBtn.addEventListener("click", () => {
            navbar.style.left = "0";        
            openBtn.style.display = "none";
            closeBtn.style.display = "block";
        });

        closeBtn.addEventListener("click", () => {
            navbar.style.left = "-200vw";
            closeBtn.style.display = "none";
            openBtn.style.display = "block";
        });

        const resetNavbar = () => {
            if (window.innerWidth >= 1025) {
                navbar.style.left = "0"; // Ensure navbar is visible
                openBtn.style.display = "none"; // Hide open button
                closeBtn.style.display = "none"; // Hide close button
            } else {
                navbar.style.left = "-200vw"; // Hide navbar on smaller screens
                openBtn.style.display = "block"; // Show open button
                closeBtn.style.display = "none"; // Hide close button
            }
        };

        // Call resetNavbar on window resize
        window.addEventListener("resize", resetNavbar);

        // Initial call to set up the correct state
        resetNavbar();



    </script>
</body>

</html>