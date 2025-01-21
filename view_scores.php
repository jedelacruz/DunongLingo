<?php
include('config.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    $deleteSql = "DELETE FROM user_scores WHERE id = :deleteId";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->bindValue(':deleteId', $deleteId, PDO::PARAM_INT);
    if ($deleteStmt->execute()) {
        echo "<script>alert('Record deleted successfully');</script>";
    } else {
        echo "<script>alert('Failed to delete record');</script>";
    }
}

// Initialize filter variables
$filterName = $_GET['filterName'] ?? '';
$filterGuro = $_GET['filterGuro'] ?? '';
$filterSection = $_GET['filterSection'] ?? '';
$filterTime = $_GET['filterTime'] ?? '';
$filterScore = $_GET['filterScore'] ?? '';

// Construct SQL query with filters
$sql = "
    SELECT 
        us.id,
        us.score,
        us.total_questions,
        us.time_taken,
        us.quiz_timer,
        u.fullname,
        u.section,
        u.guro,
        c.title AS course_title,
        m.title AS module_title
    FROM user_scores us
    JOIN users u ON us.user_id = u.id
    JOIN modules m ON us.module_id = m.id
    JOIN courses c ON m.course_id = c.id
    WHERE 1=1
";

if (!empty($filterScore)) {
    $sql .= " AND us.score = :filterScore";
}

if (!empty($filterName)) {
    $sql .= " AND u.fullname LIKE :filterName";
}
if (!empty($filterGuro)) {
    $sql .= " AND u.guro LIKE :filterGuro";
}
if (!empty($filterSection)) {
    $sql .= " AND u.section LIKE :filterSection";
}
if (!empty($filterTime)) {
    switch ($filterTime) {
        case 'under1min':
            $sql .= " AND us.time_taken < 60";
            break;
        case 'under3min':
            $sql .= " AND us.time_taken >= 60 AND us.time_taken < 180";
            break;
        case 'under5min':
            $sql .= " AND us.time_taken >= 180 AND us.time_taken < 300";
            break;
        case 'over5min':
            $sql .= " AND us.time_taken >= 300";
            break;
    }
}
$sql .= " ORDER BY us.id DESC";

$stmt = $pdo->prepare($sql);

// Bind parameters for filters
if (!empty($filterName)) {
    $stmt->bindValue(':filterName', "%$filterName%", PDO::PARAM_STR);
}
if (!empty($filterGuro)) {
    $stmt->bindValue(':filterGuro', "%$filterGuro%", PDO::PARAM_STR);
}
if (!empty($filterSection)) {
    $stmt->bindValue(':filterSection', "%$filterSection%", PDO::PARAM_STR);
}

if (!empty($filterScore)) {
    $stmt->bindValue(':filterScore', $filterScore, PDO::PARAM_INT);
}

$stmt->execute();
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Scores</title>
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

        #filter-form{
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 3vh 0vw;
            justify-content: space-between;
        }

        #filter-form input, select, button{
            width: 32%;
        }

        #filter-form input, form select, form button {
            padding: 10px;
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

        /* Responsive Design */
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

            #filter-form input, select, button{
            width: 100%;
        }

        }
    </style>
    <link rel="icon" href="logo.png" type="image/x-icon">
</head>

<body>
    <header>
        <h1>Scores</h1>
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

        <h2>Scores</h2>

        <form id="filter-form" method="GET" action="">
    <input type="text" name="filterName" placeholder="Filter by Name" value="<?= htmlspecialchars($filterName) ?>">
    <input type="text" name="filterGuro" placeholder="Filter by Teacher" value="<?= htmlspecialchars($filterGuro) ?>">
    <input type="text" name="filterSection" placeholder="Filter by Section" value="<?= htmlspecialchars($filterSection) ?>">
    <input type="text" name="filterScore" placeholder="Filter by Score" value="<?= htmlspecialchars($filterScore) ?>">
    <select name="filterTime">
        <option value="">Filter by Time</option>
        <option value="under1min" <?= $filterTime === 'under1min' ? 'selected' : '' ?>>Under 1 min</option>
        <option value="under3min" <?= $filterTime === 'under3min' ? 'selected' : '' ?>>Under 3 min</option>
        <option value="under5min" <?= $filterTime === 'under5min' ? 'selected' : '' ?>>Under 5 min</option>
        <option value="over5min" <?= $filterTime === 'over5min' ? 'selected' : '' ?>>Over 5 min</option>
    </select>
    <button type="submit">Filter</button>
</form>


        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Section</th>
                        <th>Guro</th>
                        <th>Course Title</th>
                        <th>Module Title</th>
                        <th>Score</th>
                        <th>Total Questions</th>
                        <th>Time Taken</th>
                        <th>Quiz Timer</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($scores as $score): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($score['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($score['section']); ?></td>
                            <td><?php echo htmlspecialchars($score['guro']); ?></td>
                            <td><?php echo htmlspecialchars($score['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($score['module_title']); ?></td>
                            <td><?php echo htmlspecialchars($score['score']); ?></td>
                            <td><?php echo htmlspecialchars($score['total_questions']); ?></td>
                            <td><?php echo htmlspecialchars($score['time_taken']); ?></td>
                            <td><?php echo htmlspecialchars($score['quiz_timer']); ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                    <input type="hidden" name="delete_id" value="<?= htmlspecialchars($score['id']) ?>">
                                    <button name="delete" type="submit">Delete</button>
                                </form>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($scores)): ?>
            <p>No scores found.</p>
        <?php endif; ?>
    </div>
</body>

</html>
