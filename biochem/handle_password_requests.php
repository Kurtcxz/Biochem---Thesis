<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_script.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE users u JOIN password_change_requests p ON u.id = p.user_id SET u.password = p.new_password WHERE p.id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
    }

    $stmt = $conn->prepare("DELETE FROM password_change_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();

    header('Location: handle_password_requests.php');
    exit();
}

$requests = $conn->query("SELECT p.id, u.username, p.created_at FROM password_change_requests p JOIN users u ON p.user_id = u.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handle Password Change Requests</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Password Change Requests</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Requested At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($request = $requests->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['username']); ?></td>
                        <td><?php echo htmlspecialchars($request['created_at']); ?></td>
                        <td>
                            <form action="" method="POST" class="d-inline">
                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                <button type="submit" name="action" value="deny" class="btn btn-sm btn-danger">Deny</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</body>
</html>
