<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_script.php');
    exit();
}

$companies = $conn->query("SELECT * FROM companies ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_POST['company_id'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rep_first_name = $_POST['rep_first_name'];
    $rep_last_name = $_POST['rep_last_name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, company_id, first_name, last_name, email, contact_number, address) VALUES (?, ?, 'company_rep', ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisssss", $username, $password, $company_id, $rep_first_name, $rep_last_name, $email, $contact_number, $address);
    
    if ($stmt->execute()) {
        $success_message = "Company representative created successfully.";
    } else {
        $error_message = "Error creating company representative.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Representative Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Company Representative Management</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <h3>Create Company Representative</h3>
        <form action="" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="company_id">Company</label>
                <select class="form-control" id="company_id" name="company_id" required onchange="fetchCompanyDetails()">
                    <option value="">Select a company</option>
                    <?php while ($company = $companies->fetch_assoc()): ?>
                        <option value="<?php echo $company['id']; ?>"><?php echo htmlspecialchars($company['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="rep_first_name">First Name</label>
                <input type="text" class="form-control" id="rep_first_name" name="rep_first_name" required readonly>
            </div>
            <div class="form-group">
                <label for="rep_last_name">Last Name</label>
                <input type="text" class="form-control" id="rep_last_name" name="rep_last_name" required readonly>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required readonly>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="tel" class="form-control" id="contact_number" name="contact_number" required readonly>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address" required readonly></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Company Representative</button>
        </form>
        
        <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function validateForm() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm_password").value;
        if (password != confirmPassword) {
            alert("Passwords do not match.");
            return false;
        }
        return true;
    }
    
    function fetchCompanyDetails() {
        var companyId = $('#company_id').val();
        if (companyId) {
            $.ajax({
                url: 'get_company_details.php',
                type: 'GET',
                data: { company_id: companyId },
                dataType: 'json',
                success: function(data) {
                    $('#rep_first_name').val(data.rep_first_name);
                    $('#rep_last_name').val(data.rep_last_name);
                    $('#email').val(data.email);
                    $('#contact_number').val(data.contact_number);
                    $('#address').val(data.address);
                },
                error: function() {
                    alert('Error fetching company details');
                }
            });
        } else {
            // Clear the form fields if no company is selected
            $('#rep_first_name, #rep_last_name, #email, #contact_number, #address').val('');
        }
    }
    </script>
</body>
</html>