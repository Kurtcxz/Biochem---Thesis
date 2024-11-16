<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'invoice_manager') {
    header('Location: login_script.php');
    exit();
}

// Fetch all invoices
$stmt = $conn->prepare("SELECT i.id, e.first_name, e.last_name, i.company_name, e.tests, i.amount, i.status, i.created_at 
                        FROM invoices i 
                        JOIN employees e ON i.employee_id = e.id 
                        ORDER BY i.created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$invoices = $result->fetch_all(MYSQLI_ASSOC);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="invoice_report.csv"');

// Open the output stream
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, array('Invoice ID', 'Employee Name', 'Company', 'Tests', 'Amount', 'Status', 'Created At'));

// Add data to CSV
foreach ($invoices as $invoice) {
    $row = array(
        $invoice['id'],
        $invoice['first_name'] . ' ' . $invoice['last_name'],
        $invoice['company_name'],
        $invoice['tests'],
        $invoice['amount'],
        $invoice['status'],
        $invoice['created_at']
    );
    fputcsv($output, $row);
}

// Close the output stream
fclose($output);
exit();
