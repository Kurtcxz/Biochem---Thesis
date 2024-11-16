<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'invoice_manager') {
    header('Location: login_script.php');
    exit();
}

// Get the filter status and date range from the query string
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Prepare the SQL query based on the filter
$sql = "SELECT i.*, e.first_name, e.last_name, e.tests 
        FROM invoices i 
        JOIN employees e ON i.employee_id = e.id 
        WHERE 1=1 ";

$params = array();
$types = '';

if ($filter_status !== 'all') {
    $sql .= "AND i.status = ? ";
    $params[] = $filter_status;
    $types .= 's';
}

if ($start_date && $end_date) {
    $sql .= "AND i.created_at BETWEEN ? AND ? ";
    $params[] = $start_date . ' 00:00:00';
    $params[] = $end_date . ' 23:59:59';
    $types .= 'ss';
}

$sql .= "ORDER BY i.created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$invoices = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Invoices - Invoice Manager Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Manage Invoices</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <div class="mb-3">
            <form action="" method="GET" class="form-inline">
                <label for="status" class="mr-2">Filter by status:</label>
                <select name="status" id="status" class="form-control mr-2">
                    <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>All</option>
                    <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="paid" <?php echo $filter_status === 'paid' ? 'selected' : ''; ?>>Paid</option>
                </select>
                <label for="start_date" class="mr-2">Start Date:</label>
                <input type="date" name="start_date" id="start_date" class="form-control mr-2" value="<?php echo $start_date; ?>">
                <label for="end_date" class="mr-2">End Date:</label>
                <input type="date" name="end_date" id="end_date" class="form-control mr-2" value="<?php echo $end_date; ?>">
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        
        <table id="invoicesTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Employee Name</th>
                    <th>Company</th>
                    <th>Tests</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($invoice['id']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['tests']); ?></td>
                        <td>$<?php echo number_format($invoice['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($invoice['status']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['created_at']); ?></td>
                        <td>
                            <?php if ($invoice['status'] === 'pending'): ?>
                                <a href="update_invoice.php?id=<?php echo $invoice['id']; ?>&action=mark_paid" class="btn btn-success btn-sm">Mark as Paid</a>
                            <?php endif; ?>
                            <a href="update_invoice.php?id=<?php echo $invoice['id']; ?>&action=edit" class="btn btn-primary btn-sm">Edit</a>
                            <a href="generate_invoice_pdf.php?id=<?php echo $invoice['id']; ?>" class="btn btn-secondary btn-sm">Generate PDF</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#invoicesTable').DataTable({
                "order": [[ 6, "desc" ]]
            });
        });
    </script>
</body>
</html>