<?php
include('config.php');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch courses for dropdown
$courses = [];
$sql = "SELECT id, title FROM courses";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $timer_minutes = intval($_POST['timer_minutes']);

    // Handle image upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_type = mime_content_type($image_tmp_name);

        // Validate file type (for example, allow only JPEG, PNG)
        if (in_array($image_type, ['image/jpeg', 'image/png'])) {
            $upload_dir = 'uploads/modules/';
            $image_path = $upload_dir . basename($image_name);
            move_uploaded_file($image_tmp_name, $image_path);
        } else {
            $message = "Invalid image type. Only JPEG and PNG are allowed.";
        }
    }

    if (!empty($course_id) && !empty($title) && !empty($content) && $timer_minutes > 0) {
        $sql = "INSERT INTO modules (course_id, title, content, timer_minutes, image_path) VALUES (:course_id, :title, :content, :timer_minutes, :image_path)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':timer_minutes', $timer_minutes);
        $stmt->bindParam(':image_path', $image_path);

        if ($stmt->execute()) {
            $message = "Module added successfully.";
        } else {
            $message = "Error: " . $stmt->errorInfo()[2];
        }
    } else {
        $message = "Please fill in all fields.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Module</title>
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

        nav a:nth-last-child(1){
            color: #B22222;
            border: 1px solid #B22222;
        }

        nav a:nth-last-child(1):hover{
            background-color: #B22222;
            color: #fff;
        }

        nav a:nth-child(1){
            color: var(--main-color);
            border: 1px solid var(--main-color);
        }

        nav a:nth-child(1):hover{
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
            color: var(--main-color);
            margin-bottom: 15px;
        }

        form label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        form input, form select, form textarea, form button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        textarea {
         resize: vertical; /* Allow only vertical resizing */
         height: 200px;
        }

        form button {
            background-color: var(--main-color);
            color: #fff;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #419b01;
        }

        .message {
            margin-bottom: 20px;
            color: #555;
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
        <h1>Add module</h1>
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

        <form method="POST" enctype="multipart/form-data">
            <h2>Add Module</h2>
            <?php if (isset($message)): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <label>Course:</label>
            <select name="course_id" required>
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['title']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Title:</label>
            <input type="text" name="title" required>
            <label>Content:</label>
            <textarea name="content" required></textarea>
            <label>Timer (minutes):</label>
            <input type="number" name="timer_minutes" required min="1">
            <label>Image:</label>
            <input type="file" name="image" accept="image/*">
            <button type="submit">Add Module</button>
        </form>
    </div>

</body>
</html>
