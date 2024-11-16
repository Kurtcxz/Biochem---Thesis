<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connection.php';

function addEmployee($conn, $employeeData, $labTests) {
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, age, gender, birthday, email, contact_number, status, company_id) VALUES (?, ?, ?, ?, ?, ?, ?, 'registered', ?)");
        $stmt->bind_param("ssissssi", $employeeData['first_name'], $employeeData['last_name'], $employeeData['age'], $employeeData['gender'], $employeeData['birthday'], $employeeData['email'], $employeeData['contact_number'], $_SESSION['company_id']);
        $stmt->execute();
        $employee_id = $conn->insert_id;

        if (!empty($labTests)) {
            $stmt = $conn->prepare("INSERT INTO employee_lab_tests (employee_id, lab_test_id) VALUES (?, ?)");
            foreach ($labTests as $test_id) {
                $stmt->bind_param("ii", $employee_id, $test_id);
                $stmt->execute();
                error_log("Inserted lab test: employee_id = $employee_id, lab_test_id = $test_id");
            }
        }

        $conn->commit();
        return $employee_id;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract employee data from $_POST
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $labTests = isset($_POST['lab_tests']) ? $_POST['lab_tests'] : [];

    // Log received data
    error_log("Received POST data: " . print_r($_POST, true));
    error_log("Lab tests: " . print_r($labTests, true));

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert employee data
        $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, age, gender, birthday, email, contact_number, status, company_id) VALUES (?, ?, ?, ?, ?, ?, ?, 'registered', ?)");
        $stmt->bind_param("ssissssi", $first_name, $last_name, $age, $gender, $birthday, $email, $contact_number, $_SESSION['company_id']);
        $stmt->execute();
        $employee_id = $conn->insert_id;

        // Handle lab tests
        if (!empty($labTests)) {
            $stmt = $conn->prepare("INSERT INTO employee_lab_tests (employee_id, lab_test_id) VALUES (?, ?)");
            foreach ($labTests as $test_id) {
                $stmt->bind_param("ii", $employee_id, $test_id);
                $stmt->execute();
                error_log("Inserted lab test: employee_id = $employee_id, lab_test_id = $test_id");
            }
        }

        // Commit the transaction
        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Employee added successfully']);
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();
        error_log('Error in add_employee.php: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>