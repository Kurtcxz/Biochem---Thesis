<?php
session_start();
include 'db_connection.php';

if (!file_exists('fpdf/fpdf.php')) {
    die("FPDF library is not installed. Please install it in the 'fpdf' directory.");
}
require('fpdf/fpdf.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'invoice_manager') {
    header('Location: login_script.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: manage_invoices.php');
    exit();
}

$invoice_id = $_GET['id'];

$stmt = $conn->prepare("SELECT i.*, e.first_name, e.last_name, e.tests, c.name as company_name, c.address
                        FROM invoices i 
                        JOIN employees e ON i.employee_id = e.id 
                        JOIN companies c ON i.company_name = c.name
                        WHERE i.id = ?");

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $invoice_id);

if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}

$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

if (!$invoice) {
    die("Invoice not found for ID: " . $invoice_id);
}

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'BioChem Laboratory', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, '123 Lab Street, Science City, 12345', 0, 1, 'C');
        $this->Cell(0, 5, 'Phone: (123) 456-7890 | Email: info@biochemlab.com', 0, 1, 'C');
        $this->Ln(10);
    }
    
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(0, 10, 'Invoice #: ' . $invoice['id'], 0, 1);
$pdf->Cell(0, 10, 'Date: ' . $invoice['created_at'], 0, 1);

$pdf->Cell(90, 10, 'Bill To:', 0, 0);
$pdf->Cell(0, 10, 'Employee Information:', 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(90, 5, $invoice['company_name'] . "\n" . $invoice['address'], 0, 'L');
$pdf->SetXY($pdf->GetX() + 90, $pdf->GetY() - 10);
$pdf->MultiCell(0, 5, $invoice['first_name'] . ' ' . $invoice['last_name'] . "\nTests: " . $invoice['tests'], 0, 'L');

$pdf->Ln(10);

$pdf->SetFillColor(200, 220, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(90, 7, 'Description', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Quantity', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Unit Price', 1, 0, 'C', true);
$pdf->Cell(40, 7, 'Amount', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(90, 7, 'Laboratory Tests', 1, 0, 'L');
$pdf->Cell(30, 7, '1', 1, 0, 'C');
$pdf->Cell(30, 7, '$' . number_format($invoice['amount'], 2), 1, 0, 'R');
$pdf->Cell(40, 7, '$' . number_format($invoice['amount'], 2), 1, 1, 'R');

$pdf->Cell(150, 7, '', 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, 'Total: $' . number_format($invoice['amount'], 2), 1, 1, 'R');

$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, 'Thank you for your business. Please make payment within 30 days of the invoice date.', 0, 'L');

$pdf->Output('Invoice_' . $invoice['id'] . '.pdf', 'D');