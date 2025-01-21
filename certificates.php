<?php
include('config.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Fetch certificates from the database
$sql_fetch_certificates = "SELECT id, user_id, module_id, created_at FROM certificates WHERE user_id = :user_id";
$stmt_fetch_certificates = $pdo->prepare($sql_fetch_certificates);
$stmt_fetch_certificates->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt_fetch_certificates->execute();
$certificates = $stmt_fetch_certificates->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Certificates</title>
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
            src: url("https://db.onlinewebfonts.com/t/14936bb7a4b6575fd2eee80a3ab52cc2.eot?#iefix") format("embedded-opentype"),
                url("https://db.onlinewebfonts.com/t/14936bb7a4b6575fd2eee80a3ab52cc2.woff2") format("woff2"),
                url("https://db.onlinewebfonts.com/t/14936bb7a4b6575fd2eee80a3ab52cc2.woff") format("woff"),
                url("https://db.onlinewebfonts.com/t/14936bb7a4b6575fd2eee80a3ab52cc2.ttf") format("truetype"),
                url("https://db.onlinewebfonts.com/t/14936bb7a4b6575fd2eee80a3ab52cc2.svg#Feather Bold") format("svg");
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
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            background-color: var(--main-color);
            color: white;
        }

        td form button:hover{
            background-color: var(--darkMain-color);
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

        }
    </style>
    <link rel="icon" href="logo.png" type="image/x-icon">
</head>

<body>
    <header>
        <h1>Your Certificates</h1>
    </header>

    <div class="container">
        <nav>
            <a href="dashboard.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="certificates.php">Certificates</a>
            <a class="logout-btn" href="logout.php">Logout</a>
        </nav>

        <div class="container-white">
        <h2>Your Certificates</h2>

<?php if ($certificates): ?>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Module</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($certificates as $certificate): ?>
                    <?php
                        // Fetch course and module details
                        $sql_course_details = "
                            SELECT c.title AS course_title, m.title AS module_title
                            FROM modules m
                            INNER JOIN courses c ON m.course_id = c.id
                            WHERE m.id = :module_id
                        ";
                        $stmt_course_details = $pdo->prepare($sql_course_details);
                        $stmt_course_details->bindValue(':module_id', $certificate['module_id'], PDO::PARAM_INT);
                        $stmt_course_details->execute();
                        $course_details = $stmt_course_details->fetch(PDO::FETCH_ASSOC);

                        if ($course_details) {
                            $course_title = $course_details['course_title'];
                            $module_title = $course_details['module_title'];
                        }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course_title); ?></td>
                        <td><?php echo htmlspecialchars($module_title); ?></td>
                        <td><?php echo htmlspecialchars($certificate['created_at']); ?></td>
                        <td>
                            <form action="download_certificate.php" method="get">
                                <button type="submit" name="id" value="<?php echo $certificate['id']; ?>">Download</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="message">You have no certificates.</p>
<?php endif; ?>

        </div>

    </div>
</body>

</html>
