<?php
include('config.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle update and delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $course_id = $_POST['course_id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $image = $_FILES['image'];

        if (!empty($title) && !empty($description)) {
            // If a new image is uploaded
            if (!empty($image['name'])) {
                $targetDir = "uploads/"; // Directory to store images
                $targetFile = $targetDir . basename($image['name']);
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                // Validate image file
                $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($imageFileType, $validExtensions)) {
                    // Move uploaded file
                    if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                        $sql = "UPDATE courses SET title = :title, description = :description, image = :image WHERE id = :course_id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':title', $title);
                        $stmt->bindParam(':description', $description);
                        $stmt->bindParam(':image', $targetFile);
                        $stmt->bindParam(':course_id', $course_id);
                        $stmt->execute();
                    } else {
                        $message = "Failed to upload image.";
                    }
                } else {
                    $message = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
                }
            } else {
                // Update without changing the image
                $sql = "UPDATE courses SET title = :title, description = :description WHERE id = :course_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':course_id', $course_id);
                $stmt->execute();
            }

            $message = "Course updated successfully.";
        } else {
            $message = "Please fill in all fields.";
        }
    }

    // Handle delete action
    if (isset($_POST['delete'])) {
        $course_id = $_POST['course_id'];
        $sql = "DELETE FROM courses WHERE id = :course_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();

        $message = "Course deleted successfully.";
    }
}

// Fetch courses from the database
$sql = "SELECT * FROM courses";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Courses</title>
    <link rel="icon" href="logo.png" type="image/x-icon">
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
            margin: 3vh 0vw
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        form input,
        form textarea,
        form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            resize: vertical;
            /* Allow only vertical resizing */
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            min-width: 600px; /* Ensures a minimum width for better scrolling */
        }

        div[style="overflow-x: auto;"] {
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: var(--main-color);
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .message {
            margin-bottom: 15px;
            font-weight: bold;
            color: #d9534f;
        }

        .message.success {
            color: #5cb85c;
        }

        td form {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 10px 0px;
        }

        td form button {
            width: 100%;
            padding: 8px 12px;
            margin: 0 5px;
            /* Space between buttons */
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        /* Edit button */
        td form button[name="edit"] {
            background-color: #ffc107;
            /* Bootstrap yellow */
            color: white;
        }

        td form button[name="edit"]:hover {
            background-color: #e0a800;
            /* Darker yellow for hover effect */
        }

        /* Delete button */
        td form button[name="delete"] {
            background-color: #dc3545;
            /* Bootstrap red */
            color: white;
        }

        td form button[name="delete"]:hover {
            background-color: #c82333;
            /* Darker red for hover effect */
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

        }
    </style>
</head>

<body>
    <header>
        <h1>Modify Courses</h1>
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
            <div class="message <?= $stmt->execute() ? 'success' : 'error'; ?>">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <h2>Course List</h2>
        <div style="overflow-x: auto;">

        
        <table border="1">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= htmlspecialchars($course['title']); ?></td>
                        <td><?= htmlspecialchars($course['description']); ?></td>
                        <td><img src="<?= $course['image']; ?>" alt="Course Image" width="100"></td>
                        <td>
                            <!-- Edit and Delete buttons -->
                            <form action="modify_course.php" method="POST">
                                <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
                                <button type="submit" name="edit" value="edit">Edit</button>
                            </form>
                            <form action="modify_course.php" method="POST">
                                <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
                                <button type="submit" name="delete" value="delete"
                                    onclick="return confirm('Are you sure you want to delete this course?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <?php if (isset($_POST['edit'])): ?>
            <?php
            $course_id = $_POST['course_id'];
            $sql = "SELECT * FROM courses WHERE id = :course_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':course_id', $course_id);
            $stmt->execute();
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>

            <h2 style="margin: 3vh 0vw;">Edit Course</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($course['title']); ?>" required>
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="5"
                    required><?= htmlspecialchars($course['description']); ?></textarea>
                <label for="image">Image:</label>
                <input type="file" id="image" name="image" accept="image/*">
                <button type="submit" name="update" value="update">Update Course</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>