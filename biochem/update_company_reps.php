<?php
include 'db_connection.php';

// Fetch all company rep users without a company_id
$stmt = $conn->prepare("SELECT u.id, u.username, c.id AS company_id, c.name AS company_name 
                        FROM users u 
                        LEFT JOIN companies c ON u.username = c.name 
                        WHERE u.role = 'company_rep' AND u.company_id IS NULL");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    if ($row['company_id']) {
        // Update the user with the company_id
        $update_stmt = $conn->prepare("UPDATE users SET company_id = ? WHERE id = ?");
        $update_stmt->bind_param("ii", $row['company_id'], $row['id']);
        $update_stmt->execute();
        echo "Updated user {$row['username']} with company_id {$row['company_id']}<br>";
    } else {
        echo "No matching company found for user {$row['username']}<br>";
    }
}

$stmt->close();
$conn->close();

echo "Update process completed.";
?>