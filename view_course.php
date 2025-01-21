<?php 
// Include the config to connect to the database
include('config.php');

// Check if the 'id' parameter is passed in the URL
if (!isset($_GET['id'])) {
    die("Course ID is required.");
}

$course_id = $_GET['id'];

// Fetch the course details based on the provided course ID
$sql = "SELECT * FROM courses WHERE id = :course_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':course_id' => $course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Course not found.");
}

// Fetch the modules for this course
$sql_modules = "SELECT * FROM modules WHERE course_id = :course_id";
$stmt_modules = $pdo->prepare($sql_modules);
$stmt_modules->execute([':course_id' => $course_id]);
$modules = $stmt_modules->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - Course Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        :root {
            --main-color: #58cc07;
            --second-color: #03a9f4;
            --darkMain-color: #419b01;
            --darkSecond-color: #0382bd;
            --lightMain-color: #61df07;
            --lightSecond-color: #15b5ff;
        }

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        header {
            background-color: var(--main-color);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 10vh;
        }

        header h1 {
            font-family: 'Feather Bold';
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        nav {
            margin: 20px 0;
            display: flex;
            flex-wrap: wrap;
        }
        nav a {
            text-decoration: none;
            margin: 5px;
            padding: 15px 25px;
            color: #007BFF;
            padding: 5px 10px;
            border: 1px solid #007BFF;
            border-radius: 5px;
        }

        nav a:hover {
            background-color: #007BFF;
            color: #fff;
        }

        .container-white {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin: 20px 0;
        }

        .module-container{
            width: 100%;
            display: flex;
            gap: 3%;
            flex-wrap: wrap;
        }
        .module-box{
            width: 31.3%;
            background-color: #f4f4f9;
            margin-bottom: 3%;
            display: flex;
            flex-direction: column;
            padding: 10px;
            border-radius:  10px;
        }

        .module-box h3{
            margin: 10px 0px;
        }

        .module-box img{
            margin: 0 auto;
            width: 100%;
        }

        .module-box a{
            text-decoration: none;
            padding: 10px;
            width: 100%;
            text-align: center;
            background-color: var(--main-color);
            color: white;
        }

        .module-box a:hover{
            background-color: var(--darkMain-color);
        }

        .module-box p{
            color: #333;
        }

        @media (max-width: 1024px) {
            .container {
                width: 95%;
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
            .module-container{
                width: 100%;
                flex-direction: column;
            }
            .module-box{

                width: 100%;
                margin-bottom: 3%;
            }
            
        }
    </style>
    <link rel="icon" href="logo.png" type="image/x-icon">
</head>
<body>
    <header>
        <h1>Course Details</h1>
    </header>
    <div class="container">
        <nav>
            <a href="dashboard.php">Back to Dashboard</a>
        </nav>
        <div class="container-white">
            <h1><?php echo htmlspecialchars($course['title']); ?></h1>
            <p><?php echo htmlspecialchars($course['description']); ?></p>

            <h2>Available Modules</h2>
            <div class="module-container">
                <?php
                if ($modules) {
                    foreach ($modules as $module) {
                        // Fetch the image for each module (if exists)
                        $module_image = !empty($module['image_path']) ? htmlspecialchars($module['image_path']) : 'default_image.jpg'; // Default image if no image is set
                        echo "<div class='module-box'>
                                <img src='" . $module_image . "' alt='" . htmlspecialchars($module['title']) . " Image'>
                                <h3>" . htmlspecialchars($module['title']) . "</h3>
                                <a href='view_modules.php?id=" . $module['id'] . "'>Read Module</a>
                            </div>";

                    }
                } else {
                    echo "<div class='module-box'>No modules available for this course.</div>";
                }
                ?>
            </div>

        </div>
    </div>
</body>
</html>
