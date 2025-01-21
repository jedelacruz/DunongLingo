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

// Check if the form is submitted to update the profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated data from the form
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $guro = $_POST['guro'];
    $section = $_POST['section'];
    $email = $_POST['email'];

    // Update the user's information in the database
    $update_sql = "UPDATE users SET username = :username, fullname = :fullname, guro = :guro, section = :section, email = :email WHERE id = :user_id";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([
        ':username' => $username,
        ':fullname' => $fullname,
        ':guro' => $guro,
        ':section' => $section,
        ':email' => $email,
        ':user_id' => $user_id
    ]);

    // Update session data after the update
    $_SESSION['fullname'] = $fullname;

    // Redirect to profile page after updating
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
            background-color: #58cc07;
            /* Main color */
            color: white;
            text-align: center;
            padding: 20px;
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

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            /* Space between inputs */
        }

        /* Form Element Styles */
        form input,
        form textarea,
        form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Button Styles */
        form button {
            background-color: var(--main-color);
            /* This is the main color */
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: var(--darkMain-color);
            /* Darker version of the main color */
        }

        /* Link Styles */
        a {
            text-decoration: none;
            color: #58cc07;
        }

        a:hover {
            color: #419b01;
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
        <h1>Edit Profile</h1>
    </header>

    <div class="container">
        <nav>
            <a href="dashboard.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="certificates.php">Certificates</a>
            <a href="logout.php">Logout</a>
        </nav>

        <div class="container-white">
            <h2>Profile Information</h2>
            <form action="profile.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username"
                    value="<?php echo htmlspecialchars($user_data['username']); ?>" required>

                <label for="fullname">Full Name:</label>
                <input type="text" id="fullname" name="fullname"
                    value="<?php echo htmlspecialchars($user_data['fullname']); ?>" required>

                <label for="guro">Guro:</label>
                <input type="text" id="guro" name="guro" value="<?php echo htmlspecialchars($user_data['guro']); ?>"
                    required>

                <label for="section">Section:</label>
                <input type="text" id="section" name="section"
                    value="<?php echo htmlspecialchars($user_data['section']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>"
                    required>

                <button type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</body>

</html>