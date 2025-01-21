<?php
include('config.php');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch modules for dropdown
$modules = [];
$sql = "SELECT id, title FROM modules";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_id = $_POST['module_id'];
    $question = trim($_POST['question']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_option = strtoupper(trim($_POST['correct_option']));

    if (!empty($module_id) && !empty($question) && !empty($option_a) && !empty($option_b) && !empty($correct_option)) {
        $sql = "INSERT INTO quizzes (module_id, question, option_a, option_b, option_c, option_d, correct_option) 
                VALUES (:module_id, :question, :option_a, :option_b, :option_c, :option_d, :correct_option)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':module_id', $module_id);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':option_a', $option_a);
        $stmt->bindParam(':option_b', $option_b);
        $stmt->bindParam(':option_c', $option_c);
        $stmt->bindParam(':option_d', $option_d);
        $stmt->bindParam(':correct_option', $correct_option);

        if ($stmt->execute()) {
            $message = "Quiz added successfully.";
            // Redirect to avoid resubmission
            header('Location: add_quiz.php?success=1');
            exit();
        } else {
            $message = "Error: " . $stmt->errorInfo()[2];
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Quiz</title>
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
            color: #fff;
            height: 10vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
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

        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form h2 {
            margin-bottom: 20px;
            font-size: 1.5em;
            color: var(--main-color);
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        form input,
        form textarea,
        form select,
        form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            resize: vertical;
            height: 200px;
        }

        form button {
            background-color: var(--main-color);
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: var(--darkMain-color);
        }

        .message {
            margin-bottom: 15px;
            font-weight: bold;
            color: #d9534f;
        }

        .message.success {
            color: #5cb85c;
        }

        @media (max-width: 1024px) {
            header {
                height: 10vh;
            }

            .container {
                width: 95%;   
            }

            nav{
                display: flex;
                flex-wrap: wrap;
            }

            nav a{
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

            nav a{
                padding: 10px;
            }

        }
    </style>
    <link rel="icon" href="logo.png" type="image/x-icon">
</head>

<body>
    <header>
        <h1>Add Quiz</h1>
    </header>

    <div class="container">
    <nav>
            <a href="admin_dashboard.php">Home</a>
            <a href="view_admins.php">View Admins</a>
            <a href="view_users.php">View Users</a>
            <a href="view_scores.php">View Scores</a>
            <a href="add_course.php">Add Course</a>
            <a href="modify_course.php">Modify Course</a>
            <a href="add_module.php">Add Module</a>
            <a href="modify_module.php">Modify Module</a>
            <a href="add_quiz.php">Add Quiz</a>
            <a href="messages.php">Messages</a>
            <a href="logout.php">Logout</a>
        </nav>

        <?php if (isset($message)): ?>
            <div class="message <?= isset($stmt) && $stmt->execute() ? 'success' : 'error'; ?>">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <h2>Add Question</h2>
            <label for="module_id">Module:</label>
            <select name="module_id" id="module_id" required>
                <option value="">Select Module</option>
                <?php foreach ($modules as $module): ?>
                    <option value="<?= $module['id'] ?>"><?= $module['title'] ?></option>
                <?php endforeach; ?>
            </select>
            <label for="question">Question:</label>
            <textarea id="question" name="question" required></textarea>
            <label for="option_a">Option A:</label>
            <input type="text" id="option_a" name="option_a" required>
            <label for="option_b">Option B:</label>
            <input type="text" id="option_b" name="option_b" required>
            <label for="option_c">Option C:</label>
            <input type="text" id="option_c" name="option_c">
            <label for="option_d">Option D:</label>
            <input type="text" id="option_d" name="option_d">
            <label for="correct_option">Correct Option (A/B/C/D):</label>
            <input type="text" id="correct_option" name="correct_option" required maxlength="1">
            <button type="submit">Add Quiz</button>
        </form>
    </div>

</body>

</html>
