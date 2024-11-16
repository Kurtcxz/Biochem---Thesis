<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'junior_medtech') {
    header('Location: login_script.php');
    exit();
}

$patient_id = $_GET['patient_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tests = $_POST['tests'] ?? [];
    $results = [];

    foreach ($tests as $test) {
        $results[$test] = $_POST[$test] ?? '';
    }

    $results_json = json_encode($results);

    $stmt = $conn->prepare("UPDATE employees SET test_results = ?, status = 'results_entered' WHERE id = ?");
    $stmt->bind_param("si", $results_json, $patient_id);
    $stmt->execute();

    header('Location: junior-medtech-dashboard.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

include 'test_prices.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Lab Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Input Lab Results for <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h1>
        <form action="" method="POST">
            <?php
            $patient_tests = explode(',', $patient['tests']);
            foreach ($patient_tests as $test) {
                $test = trim($test);
                if (isset($test_prices[$test])) {
                    echo '<div class="form-group">';
                    echo '<label for="' . htmlspecialchars($test) . '">' . htmlspecialchars($test) . '</label>';
                    echo '<textarea class="form-control" id="' . htmlspecialchars($test) . '" name="' . htmlspecialchars($test) . '" rows="3"></textarea>';
                    echo '<input type="hidden" name="tests[]" value="' . htmlspecialchars($test) . '">';
                    echo '</div>';
                }
            }
            ?>
            <button type="submit" class="btn btn-primary">Submit Results</button>
        </form>
    </div>
</body>
</html>