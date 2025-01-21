<?php 
include('config.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch filters if they are set
$name_filter = isset($_GET['name_filter']) ? $_GET['name_filter'] : '';
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : 'DESC';

// SQL query with filters
$query = "SELECT id, name, subject, text, timestamp FROM messages WHERE name LIKE :name_filter ORDER BY timestamp $date_filter";
$stmt = $pdo->prepare($query);
$stmt->execute(['name_filter' => '%' . $name_filter . '%']);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
    $stmt->execute(['id' => $delete_id]);
    header('Location: messages.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Messages</title>
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

        form{
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        form div{
            width: 30%;
            display: flex;
            align-items: center;
            margin-top: 3vh;
        }

        form input, form select, form textarea, form button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
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

        td a{
            width: 100%;
            padding: 8px 12px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            background-color: #dc3545;  
            color: white;
        }
        td a:hover{
            background-color: #c82333;
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

            form{
                flex-direction: column;
            }

            form div{
                width: 100%;
            }

        }
    </style>
</head>

<body>
    <header>
        <h1>Messages</h1>
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

        <h2>Messages</h2>

        

        <form method="GET" action="messages.php">
            <div>
                <input type="text" id="name_filter" name="name_filter" placeholder="Name: " value="<?php echo htmlspecialchars($name_filter); ?>">
            </div>
            <div>
                <select id="date_filter" name="date_filter">
                    <option value="DESC" <?php echo $date_filter == 'DESC' ? 'selected' : ''; ?>>Newest to Oldest</option>
                    <option value="ASC" <?php echo $date_filter == 'ASC' ? 'selected' : ''; ?>>Oldest to Newest</option>
                </select>
            </div>
            <div>
                <button type="submit">Apply Filters</button>
            </div>
           
            
        </form>

        <?php if (count($messages) > 0): ?>
            <div style="overflow-x: auto;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $message): ?>
                <tr>
                    <td><?php echo htmlspecialchars($message['id']); ?></td>
                    <td><?php echo htmlspecialchars($message['name']); ?></td>
                    <td><?php echo htmlspecialchars($message['subject']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($message['text'])); ?></td>
                    <td><?php echo htmlspecialchars($message['timestamp']); ?></td>
                    <td><a href="?delete_id=<?php echo $message['id']; ?>" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

        <?php else: ?>
            <p>No messages found.</p>
        <?php endif; ?>
    </div>
</body>

</html>