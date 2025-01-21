<?php
include('config.php');
require 'fpdf/fpdf.php';
require 'fpdi/src/autoload.php';

use setasign\Fpdi\Fpdi; // Move this to the top, outside any code block


session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ensure module_id is provided
if (isset($_GET['module_id'])) {
    $module_id = (int) $_GET['module_id']; // Explicitly cast to integer

    // Fetch the course title, module title, and user's full name
    $sql_details = "
        SELECT 
            c.title AS course_title,
            m.title AS module_title,
            u.fullname AS user_fullname
        FROM 
            modules m
        INNER JOIN courses c ON m.course_id = c.id
        INNER JOIN users u ON u.id = :user_id
        WHERE m.id = :module_id
    ";
    $stmt_details = $pdo->prepare($sql_details);
    $stmt_details->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_details->bindValue(':module_id', $module_id, PDO::PARAM_INT);
    $stmt_details->execute();
    $details = $stmt_details->fetch(PDO::FETCH_ASSOC);

    if (!$details) {
        echo "Unable to fetch course/module details.";
        exit();
    }

    $course_title = $details['course_title'];
    $module_title = $details['module_title'];
    $user_fullname = $details['user_fullname'];

    // Check if the user has already taken the quiz for this module
    $sql_check = "SELECT COUNT(*) FROM user_scores WHERE user_id = :user_id AND module_id = :module_id";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_check->bindValue(':module_id', $module_id, PDO::PARAM_INT);
    $stmt_check->execute();
    $quiz_taken = $stmt_check->fetchColumn();

    if ($quiz_taken > 0) {
        // Retrieve the user's previous score and time_taken
        $sql_result = "SELECT score, total_questions, time_taken, quiz_timer FROM user_scores WHERE user_id = :user_id AND module_id = :module_id";
        $stmt_result = $pdo->prepare($sql_result);
        $stmt_result->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_result->bindValue(':module_id', $module_id, PDO::PARAM_INT);
        $stmt_result->execute();
        $result = $stmt_result->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            // Extract data from the result
            $score = $result['score'];
            $time_taken = $result['time_taken'];
            $quiz_timer = $result['quiz_timer'];
            $total_questions = $result['total_questions'];
    
            // Retrieve course and module details dynamically
            $sql_details = "
                SELECT 
                    c.title AS course_title,
                    m.title AS module_title,
                    u.fullname AS user_fullname
                FROM 
                    modules m
                INNER JOIN courses c ON m.course_id = c.id
                INNER JOIN users u ON u.id = :user_id
                WHERE m.id = :module_id
            ";
            $stmt_details = $pdo->prepare($sql_details);
            $stmt_details->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt_details->bindValue(':module_id', $module_id, PDO::PARAM_INT);
            $stmt_details->execute();
            $details = $stmt_details->fetch(PDO::FETCH_ASSOC);
    
            if ($details) {

                
                $module_name = $details['module_title'];
                $course_name = $details['course_title'];
                $user_fullname = $details['user_fullname'];
    
                $pdf = new Fpdi();
                $pdf->SetTextColor(43, 45, 66);
                $pdf->AddFont('AlexBrush', '', 'AlexBrush-Regular.php'); 
                $pdf->AddFont('Poppins', '', 'Poppins-Regular.php'); 
                $pdf->AddPage('L'); // 'L' stands for landscape
                $pdf->setSourceFile('certificate_template.pdf');
                $templateId = $pdf->importPage(1);
                $pdf->useTemplate($templateId);
    
                // Add Alex Brush font for the full name
                $pdf->AddFont('AlexBrush', '', 'AlexBrush-Regular.ttf', true);
                $pdf->SetFont('AlexBrush', '', 70); // Use Alex Brush with a larger font size
    
                // Get the width of the PDF
                $pageWidth = $pdf->GetPageWidth();
    
                // Add user's full name
                $pdf->SetXY(0, 87.5); // Adjust Y-coordinate as needed
                $pdf->Cell($pageWidth, 10, $user_fullname, 0, 1, 'C'); // Centered full name
    
                // Add Poppins font for details 
                $pdf->AddFont('Poppins', '', 'Poppins-Regular.ttf', true);
                $pdf->SetFont('Poppins', '', 16); // Use Poppins with a regular size
    
                // Add completion details
                $pdf->SetXY(0, 110); // Adjust Y-coordinate as needed
                $completion_text = "for completing the $module_name in $course_name";
                $pdf->Cell($pageWidth, 10, $completion_text, 0, 1, 'C'); // Centered text
    
                // Add score and time details
                $pdf->SetXY(0, 120); // Adjust Y-coordinate as needed
                $details_text = "with a score of $score out of $total_questions in just $time_taken";
                $pdf->Cell($pageWidth, 10, $details_text, 0, 1, 'C'); // Centered text
    
                // Instead of outputting the PDF, save it to a variable as binary data
        $pdf_content = $pdf->Output('S'); // 'S' means to return the file as a string, not output it

        // Save the PDF content into the database
        $sql_save_pdf = "
            INSERT INTO certificates (user_id, module_id, pdf_data, created_at)
            VALUES (:user_id, :module_id, :pdf_data, NOW())
        ";
        $stmt_save_pdf = $pdo->prepare($sql_save_pdf);
        $stmt_save_pdf->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_save_pdf->bindValue(':module_id', $module_id, PDO::PARAM_INT);
        $stmt_save_pdf->bindValue(':pdf_data', $pdf_content, PDO::PARAM_LOB);
        $stmt_save_pdf->execute();

        // Redirect to certificates.php to display available certificates
        header("Location: certificates.php");
        exit();
            } else {
                echo "Unable to fetch course and module details.";
            }
        } else {
            echo "Unable to fetch your previous quiz results.";
        }
    
        exit();
    }
    
    
    
    


    $sql = "SELECT id, question, option_a, option_b, option_c, option_d, correct_option FROM quizzes WHERE module_id = :module_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':module_id', $module_id, PDO::PARAM_INT);
    $stmt->execute();

    $quiz = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $sql_timer = "SELECT timer_minutes FROM modules WHERE id = :module_id";
    $stmt_timer = $pdo->prepare($sql_timer);
    $stmt_timer->bindValue(':module_id', $module_id, PDO::PARAM_INT);
    $stmt_timer->execute();
    $module = $stmt_timer->fetch(PDO::FETCH_ASSOC);

    $timer_minutes = $module['timer_minutes']; // Timer available in the quiz
} else {
    echo "Module ID not specified.";
    exit();
}

// Convert the quiz timer (in minutes) to HH:MM:SS format
$quiz_timer_seconds = $timer_minutes * 60; // Convert minutes to seconds
$hours = floor($quiz_timer_seconds / 3600);
$minutes = floor(($quiz_timer_seconds % 3600) / 60);
$seconds = $quiz_timer_seconds % 60;
$quiz_timer_formatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['answers']) && is_array($_POST['answers'])) {
        $answers = $_POST['answers'];
        $score = 0;

        // Calculate the time taken by subtracting the start time from the current time
        $start_time = isset($_POST['start_time']) ? (int) $_POST['start_time'] : time(); // Time in seconds
        $end_time = time(); // Time in seconds when the form is submitted
        $time_taken_seconds = $end_time - $start_time; // Time taken in seconds

        // Convert seconds to HH:MM:SS format
        $hours = floor($time_taken_seconds / 3600);
        $minutes = floor(($time_taken_seconds % 3600) / 60);
        $seconds = $time_taken_seconds % 60;
        $time_taken_formatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        // Check answers and calculate the score
        foreach ($quiz as $index => $question) {
            if (isset($answers[$index]) && $answers[$index] === $question['correct_option']) {
                $score++;
            }
        }

        // Prepare the SQL to insert score, time taken, and quiz timer
        $sql_insert = "INSERT INTO user_scores (user_id, module_id, score, total_questions, time_taken, quiz_timer) 
                       VALUES (:user_id, :module_id, :score, :total_questions, :time_taken, :quiz_timer)";
        $stmt_insert = $pdo->prepare($sql_insert);

        // Bind parameters using bindValue() to pass the values directly
        $stmt_insert->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_insert->bindValue(':module_id', $module_id, PDO::PARAM_INT);
        $stmt_insert->bindValue(':score', $score, PDO::PARAM_INT);
        $stmt_insert->bindValue(':total_questions', count($quiz), PDO::PARAM_INT); // Use count($quiz) here
        $stmt_insert->bindValue(':time_taken', $time_taken_formatted, PDO::PARAM_STR); // Store as HH:MM:SS
        $stmt_insert->bindValue(':quiz_timer', $quiz_timer_formatted, PDO::PARAM_STR); // Store the formatted timer

        if ($stmt_insert->execute()) {
            // Use count($quiz) instead of $result['total_questions'] here
            echo "Full Name: $user_fullname<br>";
            echo "Course: $course_title<br>";
            echo "Module: $module_title<br>";
            echo "Your score is: $score / " . count($quiz) . "<br>"; // Display total questions count here
            echo "Time taken: $time_taken_formatted<br>";
            echo "Quiz timer: $quiz_timer_formatted<br>";
            header('Location: dashboard.php');
        } else {
            echo "Failed to save your score. Please try again.";
        }
        exit();
    } else {
        echo "Please answer all questions.";
        exit();
    }
}

?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz</title>
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

        .container-white {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .timer {
            text-align: right;
            font-size: 18px;
            color: #ff6b6b;
            margin-bottom: 20px;
        }

        .question-container {
            margin: 3vh 0vw;
        }



        button[type="submit"] {
            display: block;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background: var(--main-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button[type="submit"]:hover {
            background: var(--darkMain-color);
        }

        @media (max-width: 1024px) {
            .container {
                width: 95%;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>User Dashboard</h1>
    </header>
    <div class="container">
        <nav>
            <a href="dashboard.php">Back to Dashboard</a>
        </nav>

        <div class="container-white">

            <h2>Take Quiz</h2>
            <form method="POST">
                <input type="hidden" name="start_time" id="start_time" value="">
                <h3>Timer: <span id="timer"><?= $timer_minutes ?>:00</span></h3>

                <?php foreach ($quiz as $index => $question): ?>
                    <div class="question-container">
                        <p><?= htmlspecialchars($question['question']) ?></p>
                        <label>
                            <input type="radio" name="answers[<?= $index ?>]" value="A" required>
                            <?= htmlspecialchars($question['option_a']) ?>
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="answers[<?= $index ?>]" value="B">
                            <?= htmlspecialchars($question['option_b']) ?>
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="answers[<?= $index ?>]" value="C">
                            <?= htmlspecialchars($question['option_c']) ?>
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="answers[<?= $index ?>]" value="D">
                            <?= htmlspecialchars($question['option_d']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>

                <button type="submit">Submit Quiz</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('start_time').value = Math.floor(Date.now() / 1000); // Time in seconds
        // Countdown timer script
        var timerMinutes = <?= $timer_minutes ?>;
        var timerSeconds = 0;

        function updateTimer() {
            if (timerSeconds === 0) {
                if (timerMinutes === 0) {
                    clearInterval(timerInterval);
                    alert("Time's up!");
                    document.querySelector("form").submit();
                } else {
                    timerMinutes--;
                    timerSeconds = 59;
                }
            } else {
                timerSeconds--;
            }

            document.getElementById('timer').textContent =
                (timerMinutes < 10 ? '0' : '') + timerMinutes + ':' + (timerSeconds < 10 ? '0' : '') + timerSeconds;
        }

        var timerInterval = setInterval(updateTimer, 1000);
    </script>
</body>

</html>