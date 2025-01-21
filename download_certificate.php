<?php
include('config.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
// Get the certificate ID from the query string
if (isset($_GET['id'])) {
    $certificate_id = $_GET['id'];

    // Fetch the certificate PDF data from the database
    $sql_fetch_pdf = "SELECT pdf_data FROM certificates WHERE id = :certificate_id AND user_id = :user_id";
    $stmt_fetch_pdf = $pdo->prepare($sql_fetch_pdf);
    $stmt_fetch_pdf->bindValue(':certificate_id', $certificate_id, PDO::PARAM_INT);
    $stmt_fetch_pdf->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_fetch_pdf->execute();
    $pdf_data = $stmt_fetch_pdf->fetchColumn();

    if ($pdf_data) {
        // Set headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="certificate.pdf"');
        echo $pdf_data;
        exit();
    } else {
        echo "Certificate not found or you don't have permission to access it.";
    }
} else {
    echo "Invalid certificate ID.";
}
?>
