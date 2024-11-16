<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'marketing_manager') {
    header("Location: login_script.php");
    exit();
}

$result = $conn->query("SELECT * FROM companies ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Company List</h2>
        <a href="register_company.php" class="btn btn-primary mb-3">Register New Company</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Company Name</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Representative</th>
                    <th>Actions</th>
                    <th>Contact Number</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($company = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($company['name']); ?></td>
                    <td><?php echo htmlspecialchars($company['address']); ?></td>
                    <td><?php echo htmlspecialchars($company['email']); ?></td>
                    <td><?php echo htmlspecialchars($company['rep_first_name'] . ' ' . $company['rep_last_name']); ?></td>
                    <td>
                        <a href="edit_company.php?id=<?php echo $company['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete_company.php?id=<?php echo $company['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this company?')">Delete</a>
                    </td>
                    <td><?php echo htmlspecialchars($company['contact_number']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="marketing_manager_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</body>
</html>

<?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}
?>
