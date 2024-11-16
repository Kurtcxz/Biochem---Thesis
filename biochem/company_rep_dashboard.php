<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_rep') {
    header("Location: login_script.php");
    exit;
}

// Get company_id from users table if not in session
if (!isset($_SESSION['company_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT company_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && isset($user['company_id'])) {
        $_SESSION['company_id'] = $user['company_id'];
    } else {
        header("Location: login_script.php?error=no_company");
        exit;
    }
}

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get total number of employees
$total_query = $conn->prepare("SELECT COUNT(*) as total FROM employees WHERE company_id = ?");
$total_query->bind_param("i", $_SESSION['company_id']);
$total_query->execute();
$total_result = $total_query->get_result()->fetch_assoc();
$total_employees = $total_result['total'];
$total_pages = ceil($total_employees / $per_page);

// Get employees with pagination
$employee_query = $conn->prepare("
    SELECT e.*, 
           GROUP_CONCAT(t.name) as assigned_tests,
           COUNT(DISTINCT et.test_id) as test_count
    FROM employees e
    LEFT JOIN employee_tests et ON e.id = et.employee_id
    LEFT JOIN tests t ON et.test_id = t.id
    WHERE e.company_id = ?
    GROUP BY e.id
    ORDER BY e.created_at DESC
    LIMIT ? OFFSET ?
");
$employee_query->bind_param("iii", $_SESSION['company_id'], $per_page, $offset);
$employee_query->execute();
$employees = $employee_query->get_result();

// Get dashboard counts
$counts_query = $conn->prepare("
    SELECT 
        COUNT(*) as total_employees,
        SUM(CASE WHEN status = 'pending_tests' THEN 1 ELSE 0 END) as pending_tests,
        SUM(CASE WHEN status = 'tests_assigned' THEN 1 ELSE 0 END) as tests_assigned,
        SUM(CASE WHEN status = 'sent_to_marketing' THEN 1 ELSE 0 END) as sent_to_marketing
    FROM employees 
    WHERE company_id = ?
");
$counts_query->bind_param("i", $_SESSION['company_id']);
$counts_query->execute();
$counts = $counts_query->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Representative Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Dashboard Styles */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            margin: 0;
            color: #4e73df;
        }
        .stat-card p {
            font-size: 24px;
            margin: 10px 0;
        }
        
        /* Table Styles */
        .employee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .employee-table th, 
        .employee-table td {
            padding: 12px;
            border: 1px solid #e3e6f0;
        }
        .employee-table th {
            background: #f8f9fc;
            font-weight: bold;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
            border-radius: 8px;
        }
        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        /* Button Styles */
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: #4e73df;
            color: white;
        }
        .btn-secondary {
            background: #858796;
            color: white;
        }
        .btn-success {
            background: #1cc88a;
            color: white;
        }
        
        /* Status Badge Styles */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
        }
        .status-pending_tests { background: #ffeeba; }
        .status-tests_assigned { background: #c3e6cb; }
        .status-sent_to_marketing { background: #b8daff; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Dashboard Stats -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Employees</h3>
                <p><?php echo $counts['total_employees']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Pending Tests</h3>
                <p><?php echo $counts['pending_tests']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Tests Assigned</h3>
                <p><?php echo $counts['tests_assigned']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Sent to Marketing</h3>
                <p><?php echo $counts['sent_to_marketing']; ?></p>
            </div>
        </div>

        <!-- Employee Management Section -->
        <div class="card">
            <div class="card-header">
                <h2>Employee Management</h2>
                <div class="header-actions">
                    <button class="btn btn-primary" id="addBulkEmployees">Add Multiple Employees</button>
                    <button class="btn btn-secondary" id="addSingleEmployee">Add Single Employee</button>
                    <button class="btn btn-success" id="assignTestsBtn" disabled>
                        Assign Tests (<span id="selectedCount">0</span>)
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="employee-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Name</th>
                            <th>Contact Info</th>
                            <th>Status</th>
                            <th>Assigned Tests</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($employee = $employees->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="employee_ids[]" 
                                       value="<?php echo $employee['id']; ?>"
                                       <?php echo ($employee['status'] !== 'pending_tests') ? 'disabled' : ''; ?>>
                            </td>
                            <td><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></td>
                            <td>
                                Email: <?php echo htmlspecialchars($employee['email']); ?><br>
                                Phone: <?php echo htmlspecialchars($employee['contact_number']); ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $employee['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $employee['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($employee['test_count'] > 0): ?>
                                    <?php echo htmlspecialchars($employee['assigned_tests']); ?>
                                <?php else: ?>
                                    <em>No tests assigned</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($employee['status'] === 'tests_assigned'): ?>
                                    <button class="btn-action send-to-marketing" data-id="<?php echo $employee['id']; ?>">
                                        <i class="fas fa-paper-plane"></i> Send to Marketing
                                    </button>
                                <?php endif; ?>
                                <button class="btn-action edit-employee" data-id="<?php echo $employee['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action delete-employee" data-id="<?php echo $employee['id']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if($page > 1): ?>
                        <a href="?page=1">&laquo; First</a>
                        <a href="?page=<?php echo $page-1; ?>">Previous</a>
                    <?php endif; ?>

                    <?php for($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                        <?php if($i == $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if($page < $total_pages): ?>
                        <a href="?page=<?php echo $page+1; ?>">Next</a>
                        <a href="?page=<?php echo $total_pages; ?>">Last &raquo;</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Single Employee Modal -->
    <div id="singleEmployeeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Single Employee</h2>
                <span class="close-single">&times;</span>
            </div>
            <div class="modal-body">
                <form id="singleEmployeeForm">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" required>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Employee</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Employee Modal -->
    <div id="bulkEmployeeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Multiple Employees</h2>
                <span class="close-bulk">&times;</span>
            </div>
            <div class="modal-body">
                <form id="bulkEmployeeForm">
                    <div id="employeeRows">
                        <div class="employee-row">
                            <input type="text" name="employees[0][first_name]" placeholder="First Name" required>
                            <input type="text" name="employees[0][last_name]" placeholder="Last Name" required>
                            <input type="email" name="employees[0][email]" placeholder="Email" required>
                            <input type="text" name="employees[0][contact_number]" placeholder="Contact" required>
                            <textarea name="employees[0][address]" placeholder="Address" required></textarea>
                            <button type="button" class="remove-row" style="display:none;">&times;</button>
                        </div>
                    </div>
                    <button type="button" id="addRow" class="btn btn-secondary">Add Another Employee</button>
                    <button type="submit" class="btn btn-primary">Submit All</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Test Assignment Modal -->
    <div id="testAssignmentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Assign Tests</h2>
                <span class="close-test">&times;</span>
            </div>
            <div class="modal-body">
                <div class="selected-employees-summary">
                    <h3>Selected Employees (<span id="testSelectedCount">0</span>)</h3>
                    <div id="selectedEmployeesList"></div>
                </div>
                <form id="assignTestsForm">
                    <?php
                    $test_query = "SELECT id, name, category, price FROM tests ORDER BY category, name";
                    $tests = $conn->query($test_query);
                    $current_category = '';
                    
                    while($test = $tests->fetch_assoc()):
                        if($test['category'] !== $current_category):
                            if($current_category !== '') echo '</div>';
                            $current_category = $test['category'];
                            echo '<div class="test-category"><h4>' . htmlspecialchars($current_category) . '</h4>';
                        endif;
                    ?>
                        <div class="test-item">
                            <label>
                                <input type="checkbox" name="test_ids[]" 
                                       value="<?php echo $test['id']; ?>"
                                       data-price="<?php echo $test['price']; ?>">
                                <?php echo htmlspecialchars($test['name']); ?>
                                (₱<?php echo number_format($test['price'], 2); ?>)
                            </label>
                        </div>
                    <?php 
                    endwhile;
                    if($current_category !== '') echo '</div>';
                    ?>
                    <div class="test-summary">
                        <h4>Summary</h4>
                        <p>Selected Tests: <span id="testCount">0</span></p>
                        <p>Total Amount: ₱<span id="totalAmount">0.00</span></p>
                    </div>
                    <button type="submit" class="btn btn-primary">Assign Selected Tests</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Single Employee Modal
        const singleModal = document.getElementById('singleEmployeeModal');
        const addSingleBtn = document.getElementById('addSingleEmployee');
        const closeSingleBtn = document.querySelector('.close-single');
        const singleForm = document.getElementById('singleEmployeeForm');

        addSingleBtn.onclick = function() {
            singleModal.style.display = "block";
        }

        closeSingleBtn.onclick = function() {
            singleModal.style.display = "none";
        }

        singleForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('add_single_employee.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Employee added successfully!');
                    singleModal.style.display = "none";
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        // Bulk Employee Modal
        const bulkModal = document.getElementById('bulkEmployeeModal');
        const addBulkBtn = document.getElementById('addBulkEmployees');
        const closeBulkBtn = document.querySelector('.close-bulk');
        const bulkForm = document.getElementById('bulkEmployeeForm');
        const addRowBtn = document.getElementById('addRow');
        let rowCount = 1;

        addBulkBtn.onclick = function() {
            bulkModal.style.display = "block";
        }

        closeBulkBtn.onclick = function() {
            bulkModal.style.display = "none";
        }

        addRowBtn.onclick = function() {
            const template = document.querySelector('.employee-row').cloneNode(true);
            template.querySelectorAll('input, textarea').forEach(input => {
                input.name = input.name.replace('[0]', `[${rowCount}]`);
                input.value = '';
            });
            template.querySelector('.remove-row').style.display = 'inline';
            document.getElementById('employeeRows').appendChild(template);
            rowCount++;
            updateRemoveButtons();
        }

        document.getElementById('employeeRows').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('.employee-row').remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.employee-row');
            rows.forEach(row => {
                row.querySelector('.remove-row').style.display = rows.length > 1 ? 'inline' : 'none';
            });
        }

        bulkForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('add_bulk_employees.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Employees added successfully!');
                    bulkModal.style.display = "none";
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        // Test Assignment Modal
        const testModal = document.getElementById('testAssignmentModal');
        const assignTestsBtn = document.getElementById('assignTestsBtn');
        const closeTestBtn = document.querySelector('.close-test');
        const assignTestsForm = document.getElementById('assignTestsForm');
        const selectAll = document.getElementById('selectAll');

        assignTestsBtn.onclick = function() {
            const selectedEmployees = document.querySelectorAll('input[name="employee_ids[]"]:checked');
            if (selectedEmployees.length === 0) {
                alert('Please select at least one employee');
                return;
            }
            
            document.getElementById('testSelectedCount').textContent = selectedEmployees.length;
            const selectedList = document.getElementById('selectedEmployeesList');
            selectedList.innerHTML = '';
            
            selectedEmployees.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const name = row.querySelector('td:nth-child(2)').textContent;
                const div = document.createElement('div');
                div.className = 'selected-employee';
                div.textContent = name;
                selectedList.appendChild(div);
            });
            
            testModal.style.display = "block";
        }

        closeTestBtn.onclick = function() {
            testModal.style.display = "none";
        }

        // Handle checkbox selection
        document.querySelectorAll('input[name="employee_ids[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectedCount();
                updateTestSummary();
            });
        });

        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="employee_ids[]"]:not([disabled])');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            updateSelectedCount();
        });

        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('input[name="employee_ids[]"]:checked').length;
            document.getElementById('selectedCount').textContent = selectedCount;
            assignTestsBtn.disabled = selectedCount === 0;
        }

        // Handle test selection and price calculation
        document.querySelectorAll('input[name="test_ids[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updateTestSummary);
        });

        function updateTestSummary() {
            const selectedTests = document.querySelectorAll('input[name="test_ids[]"]:checked');
            const selectedEmployees = document.querySelectorAll('input[name="employee_ids[]"]:checked').length;
            let totalAmount = 0;
            
            selectedTests.forEach(test => {
                totalAmount += parseFloat(test.dataset.price);
            });

            // Multiply by number of selected employees
            totalAmount = totalAmount * selectedEmployees;

            document.getElementById('testCount').textContent = selectedTests.length;
            document.getElementById('totalAmount').textContent = totalAmount.toFixed(2);
        }

        // Handle test assignment form submission
        assignTestsForm.onsubmit = function(e) {
            e.preventDefault();
            
            const selectedEmployees = Array.from(document.querySelectorAll('input[name="employee_ids[]"]:checked'))
                .map(cb => cb.value);
            const selectedTests = Array.from(document.querySelectorAll('input[name="test_ids[]"]:checked'))
                .map(cb => cb.value);

            if (selectedTests.length === 0) {
                alert('Please select at least one test');
                return;
            }

            const formData = new FormData();
            formData.append('employee_ids', JSON.stringify(selectedEmployees));
            formData.append('test_ids', JSON.stringify(selectedTests));

            fetch('assign_tests.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Tests assigned successfully!');
                    testModal.style.display = "none";
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        // Handle Send to Marketing buttons
        document.querySelectorAll('.send-to-marketing').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to send this employee to marketing?')) {
                    const employeeId = this.dataset.id;
                    
                    fetch('send_to_marketing.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'employee_id=' + employeeId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Employee sent to marketing successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
                }
            });
        });

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target == singleModal) {
                singleModal.style.display = "none";
            }
            if (event.target == bulkModal) {
                bulkModal.style.display = "none";
            }
            if (event.target == testModal) {
                testModal.style.display = "none";
            }
        }
    });
    </script>
</body>
</html>
