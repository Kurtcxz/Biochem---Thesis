<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['patient_id'])) {
    header('Location: patient_login.php');
    exit();
}

$patient_id = $_SESSION['patient_id'];

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ? AND status = 'paid'");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    header('Location: patient_portal.php');
    exit();
}

$lab_results = json_decode($patient['test_results'], true);

// Generate PDF
require('fpdf/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Lab Results for ' . $patient['first_name'] . ' ' . $patient['last_name'], 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
foreach ($lab_results as $test => $result) {
    $pdf->Cell(0, 10, $test, 0, 1);
    $pdf->MultiCell(0, 10, $result, 0, 'L');
    $pdf->Ln(5);
}

$pdf->Output('D', 'lab_results.pdf');
