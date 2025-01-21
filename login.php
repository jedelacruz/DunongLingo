<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_username = trim($_POST['email_or_username']);
    $password = trim($_POST['password']);

    if (empty($email_or_username) || empty($password)) {
        die("Both fields are required.");
    }

    // Check if input matches email or username
    $sql = "SELECT * FROM users WHERE email = :email_or_username OR username = :email_or_username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email_or_username' => $email_or_username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If user exists and password matches
    if ($user) {
        // Check if password is bcrypt (60 characters)
        if (strlen($user['password']) == 60) { // bcrypt password length
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    $_SESSION['admin_id'] = $user['id'];
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                die("Invalid password.");
            }
        } else { // MD5 password check
            $md5_password = md5($password);
            if ($md5_password === $user['password']) {
                // Update the password to bcrypt
                $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = :new_password WHERE id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':new_password' => $new_hashed_password, ':user_id' => $user['id']]);

                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    $_SESSION['admin_id'] = $user['id'];
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                die("Invalid password.");
            }
        }
    } else {
        die("Invalid email/username or password.");
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
            background-color: #f1f1f1;
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
                height: 50%;
            
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
                height: 60%;
                padding: 0% 10%;
                margin: 5vh 0vw;
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
    <link rel="icon" href="logo.png" type="image/x-icon">
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
        <form action="login.php" method="POST">
            <h1>Mag Login</h1>
            <input type="text" name="email_or_username" placeholder="Username / Email: " required>

            <input type="password" name="password" placeholder="Password: " required>
            <div class="main-button-container">
                <button type="submit">Login</button>
            </div>
            <?php if (isset($error_message)) { echo '<p style="color: red;">' . $error_message . '</p>'; } ?>
            <p>Walang account? <a href="register.php">Sign Up</a></p>
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