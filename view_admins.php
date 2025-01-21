<?php
include('config.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle deletion of a user
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    
    // Prepare the delete query
    $deleteQuery = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    
    // Execute the query
    if ($stmt->execute()) {
        echo "<script>alert('Admin deleted successfully!'); window.location.href = 'view_admins.php';</script>";
    } else {
        echo "<script>alert('Error deleting admin.');</script>";
    }
}

// Fetch user data from the database where role = 'user'
$query = "SELECT id, username, fullname, section, guro, email, timestamp FROM users WHERE role = 'admin'";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Fetch results
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
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

        /* Same fonts as messages.php */
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

        nav a:nth-child(1) {
            color: var(--main-color);
            border: 1px solid var(--main-color);
        }

        nav a:nth-child(1):hover {
            color: white;
            background-color: var(--main-color);
        }

        nav a:nth-last-child(1) {
            color: #B22222;
            border: 1px solid #B22222;
        }

        nav a:nth-last-child(1):hover {
            background-color: #B22222;
            color: #fff;
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

        td form button {
            width: 100%;
            padding: 8px 12px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
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
    </style>
    <link rel="icon" href="logo.png" type="image/x-icon">
</head>

<body>
    <header>
        <h1>View Admins</h1>
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

        <div style="overflow-x: auto;">
            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Section</th>
                        <th>Guro</th>
                        <th>Email</th>
                        <th>Timestamp</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['fullname']; ?></td>
                            <td><?php echo $row['section']; ?></td>
                            <td><?php echo $row['guro']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['timestamp']; ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="delete_id" value="<?= htmlspecialchars($row['id']) ?>">
                                    <button name="delete" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>