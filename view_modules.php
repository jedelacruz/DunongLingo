<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the user's role and other session data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$fullname = $_SESSION['fullname'];
$role = $_SESSION['role'];

// Optionally, you can fetch more user-specific data from the database if needed.
include('config.php');

// Check if the module ID is passed in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Module ID is required.");
}

$module_id = $_GET['id'];

// Fetch module details from the database
$sql = "SELECT * FROM modules WHERE id = :module_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':module_id' => $module_id]);
$module = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if module exists
if (!$module) {
    die("Module not found.");
}

// Fetch related quizzes for the module
$sql_quizzes = "SELECT * FROM quizzes WHERE module_id = :module_id";
$stmt_quizzes = $pdo->prepare($sql_quizzes);
$stmt_quizzes->execute([':module_id' => $module_id]);
$quizzes = $stmt_quizzes->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($module['title']); ?> - Module Details</title>
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

        :root {
            --main-color: #58cc07;
            --second-color: #03a9f4;
            --darkMain-color: #419b01;
            --darkSecond-color: #0382bd;
            --lightMain-color: #61df07;
            --lightSecond-color: #15b5ff;
        }

        header {
            background-color: #58cc07;
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

        .container-white {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container-white img{
            width: 50%;
        }

        h2 {
            margin: 20px 0;
        }

        p {
            margin-bottom: 20px;
        }

        .container-white a{
            text-decoration: none;
            padding: 10px;
            width: 100%;
            text-align: center;
            background-color: var(--main-color);
            color: white;
        }

        .container-white a:hover{
            background-color: var(--darkMain-color);
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

            .container-white img{
                width: 100%;
            }
        }
    </style>
    <link rel="icon" href="logo.png" type="image/x-icon">
</head>
<body>
    <header>
        <h1>Module Details</h1>
    </header>
    <div class="container">
        <nav>
            <a href="dashboard.php">Back to Dashboard</a>
        </nav>
        <div class="container-white">
        
            <img src="<?php echo htmlspecialchars($module['image_path']); ?>" alt="<?php echo htmlspecialchars($module['title']); ?> Image">

            <h1><?php echo htmlspecialchars($module['title']); ?></h1>

            <p style="margin: 3vh 0vw;"><?php echo nl2br(htmlspecialchars($module['content'])); ?></p>
            <h3>Timer: <?php echo htmlspecialchars($module['timer_minutes']); ?> minutes</h3>

            <h2>Related Quizzes</h2>
            <a href="take_quiz.php?module_id=<?php echo isset($quizzes[0]['module_id']) ? htmlspecialchars($quizzes[0]['module_id']) : ''; ?>">Answer Quiz</a>
        </div>
    </div>
</body>
</html>