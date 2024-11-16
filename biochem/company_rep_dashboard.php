<?php
session_start();
include 'db_connection.php';

// Get the current tab from URL, default to 'pending'
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'pending';

// Get counts for dashboard stats
$counts_query = $conn->prepare("
    SELECT 
        COUNT(*) as total_employees,
        SUM(CASE WHEN status = 'pending_tests' THEN 1 ELSE 0 END) as pending_tests,
        SUM(CASE WHEN status = 'tests_assigned' THEN 1 ELSE 0 END) as tests_assigned,
        SUM(CASE WHEN status = 'sent_to_marketing' THEN 1 ELSE 0 END) as sent_to_marketing,
        SUM(CASE WHEN status = 'processed' THEN 1 ELSE 0 END) as processed
    FROM employees 
    WHERE company_id = ?
");

$counts_query->bind_param("i", $_SESSION['company_id']);
$counts_query->execute();
$counts = $counts_query->get_result()->fetch_assoc();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Modify the employee query based on the selected tab
$status_condition = match($current_tab) {
    'pending' => "AND e.status = 'pending_tests'",
    'processed' => "AND e.status = 'processed'",
    'front_desk' => "AND e.status = 'sent_to_front_desk'",
    default => "AND e.status = 'pending_tests'"
};

// Get employees with pagination
$employee_query = $conn->prepare("
    SELECT e.id, e.first_name, e.last_name, e.email, e.status, 
           GROUP_CONCAT(t.name) as assigned_tests
    FROM employees e
    LEFT JOIN employee_tests et ON e.id = et.employee_id
    LEFT JOIN tests t ON et.test_id = t.id
    WHERE e.company_id = ? 
    {$status_condition}
    GROUP BY e.id, e.first_name, e.last_name, e.email, e.status
    ORDER BY e.created_at DESC
    LIMIT ? OFFSET ?
");

$employee_query->bind_param("iii", $_SESSION['company_id'], $per_page, $offset);
$employee_query->execute();
$employees = $employee_query->get_result();

// Get total pages for pagination
$total_query = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM employees e
    WHERE company_id = ? {$status_condition}
");
$total_query->bind_param("i", $_SESSION['company_id']);
$total_query->execute();
$total_employees = $total_query->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_employees / $per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <h1><i class="fas fa-tachometer-alt"></i> Company Dashboard</h1>

        <!-- Dashboard Actions -->
        <div class="dashboard-actions">
            <button class="btn btn-primary" onclick="openAddSingleEmployee()">
                <i class="fas fa-user-plus"></i> Add Single Employee
            </button>
            <button class="btn btn-success" onclick="openBulkUpload()">
                <i class="fas fa-file-upload"></i> Bulk Upload
            </button>
        </div>

        <!-- Dashboard Statistics -->
        <div class="dashboard-stats">
            <div class="stat-box">
                <h3><i class="fas fa-users"></i> Total Employees</h3>
                <p><?php echo $counts['total_employees']; ?></p>
            </div>
            <div class="stat-box">
                <h3><i class="fas fa-clock"></i> Pending Tests</h3>
                <p><?php echo $counts['pending_tests']; ?></p>
            </div>
            <div class="stat-box">
                <h3><i class="fas fa-tasks"></i> Tests Assigned</h3>
                <p><?php echo $counts['tests_assigned']; ?></p>
            </div>
            <div class="stat-box">
                <h3><i class="fas fa-paper-plane"></i> Sent to Marketing</h3>
                <p><?php echo $counts['sent_to_marketing']; ?></p>
            </div>
            <div class="stat-box">
                <h3><i class="fas fa-check-circle"></i> Processed</h3>
                <p><?php echo $counts['processed']; ?></p>
            </div>
        </div>

        <!-- Employees Table -->
        <div class="employees-table">
            <h2><i class="fas fa-list"></i> Employee List</h2>
            
            <!-- Status Tabs -->
            <div class="tabs">
                <a href="?tab=pending" class="tab-link <?php echo $current_tab === 'pending' ? 'active' : ''; ?>">
                    <i class="fas fa-clock"></i> Pending Tests
                </a>
                <a href="?tab=processed" class="tab-link <?php echo $current_tab === 'processed' ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle"></i> Processed
                </a>
                <a href="?tab=front_desk" class="tab-link <?php echo $current_tab === 'front_desk' ? 'active' : ''; ?>">
                    <i class="fas fa-desktop"></i> Front Desk
                </a>
            </div>

            <!-- Add this button above the table -->
            <div class="bulk-actions">
                <button class="btn btn-primary" id="assignSelectedTests" onclick="assignSelectedEmployees()">
                    <i class="fas fa-tasks"></i> Assign Tests to Selected
                </button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes()">
                        </th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Assigned Tests</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($employee = $employees->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="employee-checkbox" value="<?php echo $employee['id']; ?>">
                        </td>
                        <td><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($employee['email']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower(str_replace('_', '-', $employee['status'])); ?>">
                                <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $employee['status']))); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($employee['assigned_tests'] ?? 'None'); ?></td>
                        <td>
                            <a href="edit_employee.php?id=<?php echo $employee['id']; ?>" class="btn btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <?php if ($employee['status'] === 'pending_tests'): ?>
                            <a href="#" class="btn btn-assign" onclick="assignTestsToEmployee(<?php echo $employee['id']; ?>)">
                                <i class="fas fa-tasks"></i> Assign
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?tab=<?php echo $current_tab; ?>&page=<?php echo $i; ?>" 
                       class="<?php echo ($page === $i) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- Modal for Bulk Upload -->
    <div id="bulkUploadModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Bulk Upload Employees</h2>
            <div class="template-download">
                <p>Please download and use this template for bulk upload:</p>
                <a href="templates/employee_template.csv" download class="btn btn-secondary">
                    <i class="fas fa-download"></i> Download Template
                </a>
            </div>
            <form id="bulkUploadForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Upload CSV File</label>
                    <input type="file" name="employeeFile" accept=".csv" required>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-upload"></i> Upload Employees
                </button>
            </form>
        </div>
    </div>

    <!-- Add this modal for test selection -->
    <div id="assignTestsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Assign Tests</h2>
            <form id="assignTestsForm">
                <input type="hidden" id="selectedEmployees" name="employee_ids">
                <div class="form-group">
                    <label>Select Tests to Assign:</label>
                    <div class="test-checkboxes">
                        <?php
                        // Fetch available tests
                        $tests_query = $conn->query("SELECT id, name FROM tests WHERE status = 'active'");
                        while ($test = $tests_query->fetch_assoc()) {
                            echo '<div class="test-option">';
                            echo '<input type="checkbox" name="test_ids[]" value="' . $test['id'] . '" id="test_' . $test['id'] . '">';
                            echo '<label for="test_' . $test['id'] . '">' . htmlspecialchars($test['name']) . '</label>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Assign Selected Tests</button>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
    // Modal Functions
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function openBulkUpload() {
        openModal('bulkUploadModal');
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    // Close buttons
    document.querySelectorAll('.close').forEach(function(closeBtn) {
        closeBtn.onclick = function() {
            this.closest('.modal').style.display = 'none';
        }
    });

    // Handle Bulk Upload Form Submit
    document.getElementById('bulkUploadForm').onsubmit = function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const fileInput = this.querySelector('input[type="file"]');
        
        if (!fileInput.files.length) {
            alert('Please select a file to upload');
            return;
        }
        
        fetch('process_bulk_upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error uploading file. Please try again.');
        });
    };

    // Add these functions to your existing JavaScript
    function toggleAllCheckboxes() {
        const mainCheckbox = document.getElementById('selectAll');
        const checkboxes = document.getElementsByClassName('employee-checkbox');
        
        for (let checkbox of checkboxes) {
            checkbox.checked = mainCheckbox.checked;
        }
    }

    function assignSelectedEmployees() {
        const checkboxes = document.getElementsByClassName('employee-checkbox');
        const selectedEmployees = [];
        
        for (let checkbox of checkboxes) {
            if (checkbox.checked) {
                selectedEmployees.push(checkbox.value);
            }
        }
        
        if (selectedEmployees.length === 0) {
            alert('Please select at least one employee');
            return;
        }
        
        document.getElementById('selectedEmployees').value = JSON.stringify(selectedEmployees);
        openModal('assignTestsModal');
    }

    // Handle form submission
    document.getElementById('assignTestsForm').onsubmit = function(e) {
        e.preventDefault();
        
        const selectedTests = Array.from(this.querySelectorAll('input[name="test_ids[]"]:checked'))
            .map(checkbox => checkbox.value);
        
        if (selectedTests.length === 0) {
            alert('Please select at least one test');
            return;
        }
        
        const formData = new FormData();
        formData.append('employee_ids', document.getElementById('selectedEmployees').value);
        formData.append('test_ids', JSON.stringify(selectedTests));
        
        fetch('assign_tests.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error assigning tests. Please try again.');
        });
    };
    </script>
</body>
</html>
