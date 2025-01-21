<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the config to connect to the database
include('config.php');

// Fetch the user's data from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user data exists
if (!$user_data) {
    die("User data not found.");
}

// Update session data to reflect the database
$fullname = $user_data['fullname'];
$username = $user_data['username'];
$email = $user_data['email'];

// Fetch courses data including the image path
$sql_courses = "SELECT * FROM courses WHERE created_by = :user_id";
$stmt_courses = $pdo->prepare($sql_courses);
$stmt_courses->execute([':user_id' => $user_id]);
$courses = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
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
            background-color: var(--main-color);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: 10vh
        }

        header h1 {
            color: white;
            font-family: 'Feather Bold';
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;

        }

        nav {
            margin: 20px 0;
        }

        nav a {
            text-decoration: none;
            margin-right: 10px;
            color: #007BFF;
            padding: 5px 10px;
            border: 1px solid #007BFF;
            border-radius: 5px;
        }

        nav a:hover {
            background-color: #007BFF;
            color: #fff;
        }

        nav a:nth-last-child(1) {
            color: #B22222;
            border: 1px solid #B22222;
        }

        nav a:nth-last-child(1):hover {
            background-color: #B22222;
            color: #fff;
        }

        nav a:nth-child(1) {
            color: var(--main-color);
            border: 1px solid var(--main-color);
        }

        nav a:nth-child(1):hover {
            color: white;
            background-color: var(--main-color);
        }

        .container-white {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .teksto-container {
            width: 100%;
            display: flex;
            gap: 3%;
            flex-wrap: wrap;
        }

        .course-card {
            width: 31.3%;
            background-color: #f4f4f9;
            margin-bottom: 3%;
            display: flex;
            flex-direction: column;
            padding: 10px;
            border-radius: 10px;
        }

        .course-card h3 {
            margin: 10px 0px;
        }

        .course-card img {
            margin: 0 auto;
            width: 100%;
        }

        .course-card a {
            text-decoration: none;
            padding: 10px;
            width: 100%;
            text-align: center;
            background-color: var(--main-color);
            color: white;
            margin: 10px 0px;
        }

        .course-card p {
            color: #333;
        }

        h2 {
            margin: 3vh 0vw;
        }

        @media (max-width: 1024px) {
            header {
                height: 10vh;
            }

            .container {
                width: 95%;
            }

            nav {
                display: flex;
                flex-wrap: wrap;
            }

            nav a {
                margin: 5px;
                padding: 15px 25px;
            }
        }

        @media (max-width: 767px) {
            header {
                height: 7.5vh;
            }

            header h1 {
                font-size: 1.5rem;
            }

            nav a {
                padding: 10px;
            }

            .teksto-container {
                flex-direction: column;
            }

            .course-card {
                width: 100%;
            }


        }
    </style>
    <link rel="icon" href="logo.png" type="image/x-icon">
</head>

<body>
    <header>
        <h1>User Dashboard</h1>
    </header>
    <div class="container">
        <nav>
            <a href="dashboard.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="certificates.php">Certificates</a>
            <a class="logout-btn" href="logout.php">Logout</a>
        </nav>

        <div class="container-white">
            <h1><?php echo htmlspecialchars($fullname); ?></h1>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>

            <h2>Available Courses</h2>

    

            <div class="teksto-container">
                <?php
                // Fetch courses from the database
                $sql = "SELECT * FROM courses";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($courses) {
                    foreach ($courses as $course) {
                        echo "<div class='course-card'>";

                        // Check if an image exists for the course and display it
                        if (!empty($course['image'])) {
                            echo "<img src='" . htmlspecialchars($course['image']) . "' alt='" . htmlspecialchars($course['title']) . " Image' width='200'>";
                        } else {
                            echo "<p>No image available for this course.</p>";
                        }

                        // Display the course title and description
                        echo "<h3>" . htmlspecialchars($course['title']) . "</h3>
                  <p>" . htmlspecialchars($course['description']) . "</p>";

                        echo "<a href='view_course.php?id=" . $course['id'] . "'>View Course</a>
                </div>";
                    }
                } else {
                    echo "<p>No courses available at the moment.</p>";
                }
                ?>
            </div>



        </div>

    </div>
</body>

</html>