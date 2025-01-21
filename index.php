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
            --darkMain-color: #419b01;
            --darkSecond-color: #0382bd;
            --lightMain-color: #61df07;
            --lightSecond-color: #15b5ff;
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

        header nav a:nth-of-type(1) {
            background-color: white;
            color: black;
        }

        header nav a:hover {
            background-color: white;
            color: var(--main-color);
        }

        #main-hero {
            height: 90vh;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0% 5%;
            padding-top: 10vh;
        }


        .mbox1 {
            display: flex;
            width: 45%;
            justify-content: center;
            align-items: center;
            height: 70%;
        }

        .mbox1 img {
            width: 50%;
        }

        .mbox2 {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 70%;
            gap: 30px;
        }

        .mbox2 h1 {
            text-align: center;
            font-size: 2rem;
        }

        .main-button-container {
            width: 100%;
            display: flex;
            justify-content: space-between;
        }

        .main-button-container a {
            width: 48%;
            text-align: center;
            padding: 20px;
            text-transform: uppercase;
            text-decoration: none;
            color: white;
            font-weight: bold;
            border-radius: 20px;
            transition: 0.5s ease;
        }

        .main-button-container a:nth-of-type(1) {
            background-color: var(--main-color);
            border-bottom: 5px solid var(--darkMain-color);
        }

        .main-button-container a:nth-of-type(1):hover {
            background-color: var(--lightMain-color);
        }

        .main-button-container a:nth-of-type(1):active {
            border: none;
        }

        .main-button-container a:nth-of-type(2) {
            background-color: var(--second-color);
            border-bottom: 5px solid var(--darkSecond-color);
        }

        .main-button-container a:nth-of-type(2):hover {
            background-color: var(--lightSecond-color);
        }

        .main-button-container a:nth-of-type(2):active {
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

            header nav a:nth-of-type(1) {
                background-color: white;
                color: black;
            }

            header nav a:hover {
                background-color: var(--main-color);
                color: white;
            }




            #main-hero {
                height: 90vh;
                flex-direction: column;
            }

            .mbox1 {
                width: 100%;
                height: 50%;
            }

            .mbox1 img {
                height: 80%;
            }

            .mbox2 {
                width: 100%;
            }

            .mbox2 h1 {
                font-size: 2.5rem;
            }

            .main-button-container {
                align-items: center;
                flex-direction: column;
                gap: 10px;
            }

            .main-button-container a {
                width: 90%;
                padding: 25px;
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

            header nav a {
                padding: 20px;
                font-size: 1.5rem;
            }

            #main-hero {
                padding-top: 0vh;
                height: 100vh;
                flex-direction: column;
            }

            .mbox1 {
                width: 100%;
                height: 50%;
                align-items: end;
            }

            .mbox1 img {
                height: 70%;
                width: 80%;
            }

            .mbox2 {
                width: 100%;
                height: 50%;
            }

            .mbox2 h1 {
                font-size: 1.5rem;
            }

            .main-button-container {
                align-items: center;
                flex-direction: column;
                gap: 10px;
            }

            .main-button-container a {
                width: 90%;
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

    <section id="main-hero">
        <div class="mbox1">
            <img src="https://i.pinimg.com/originals/e1/2c/61/e12c61a7a7a0a4bcdf63892376484461.gif"
                alt="">
        </div>
        <div class="mbox2">
            <h1>Ang libre, masaya, at epektibong paraan para matuto ng Filipino!</h1>
            <div class="main-button-container">
                <a href="register.php">Mag Register</a>
                <a href="login.php">Mag Login</a>
            </div>
        </div>
    </section>

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