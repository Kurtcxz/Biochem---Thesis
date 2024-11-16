<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk' || !isset($_SESSION['processing_employees'])) {
    header('Location: login_script.php');
    exit();
}

$employeeData = $_SESSION['processing_employees'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    
    try {
        foreach ($employeeData as $employee) {
            // Get employee details
            $stmt = $conn->prepare("SELECT e.*, c.name as company_name, c.id as company_id 
                                  FROM employees e 
                                  LEFT JOIN companies c ON e.company_id = c.id 
                                  WHERE e.id = ?");
            $stmt->bind_param("i", $employee['id']);
            $stmt->execute();
            $employeeDetails = $stmt->get_result()->fetch_assoc();

            // Generate credentials
            $username = $employee['patient_id'] . '_' . explode(' ', $employee['name'])[1];
            $password = date('Ymd', strtotime($employeeDetails['birthday'])) . '_' . explode(' ', $employee['name'])[1];
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Update employee record with credentials and schedule
            $stmt = $conn->prepare("UPDATE employees 
                                  SET status = 'processed', 
                                      patient_id = ?,
                                      username = ?,
                                      password = ?,
                                      appointment_date = ?,
                                      appointment_time = ?
                                  WHERE id = ?");
            
            $stmt->bind_param("sssssi", 
                $employee['patient_id'],
                $username,
                $password, // Store unencrypted for company rep to view
                $employee['appointment_date'],
                $employee['appointment_time'],
                $employee['id']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating employee information: " . $stmt->error);
            }

            // Store processed data for confirmation
            $processedData[] = [
                'name' => $employee['name'],
                'patient_id' => $employee['patient_id'],
                'username' => $username,
                'password' => $password, // Store unencrypted for display
                'appointment_date' => $employee['appointment_date'],
                'appointment_time' => $employee['appointment_time'],
                'company' => $employeeDetails['company_name']
            ];
        }

        // If everything is successful, commit the transaction
        $conn->commit();
        
        // Store processed data in session for confirmation page
        $_SESSION['processed_data'] = $processedData;
        
        // Show success message using SweetAlert2
        echo "<script>
            Swal.fire({
                title: 'Success!',
                text: 'All employees have been processed successfully',
                icon: 'success',
                confirmButtonText: 'Continue'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'processing_confirmation.php';
                }
            });
        </script>";

    } catch (Exception $e) {
        // If there's an error, rollback the transaction
        $conn->rollback();
        
        // Show error message using SweetAlert2
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: '" . addslashes($e->getMessage()) . "',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then((result) => {
                window.location.href = 'front_desk_dashboard.php';
            });
        </script>";
    }
}
?> 