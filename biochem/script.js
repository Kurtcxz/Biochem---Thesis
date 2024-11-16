// script.js

// Login Form Validation
function validateLogin() {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    let valid = true;

    // Clear previous errors
    document.getElementById('usernameError').style.display = 'none';
    document.getElementById('passwordError').style.display = 'none';

    // Validate username
    if (username === "") {
        const usernameError = document.getElementById('usernameError');
        usernameError.textContent = "Username is required.";
        usernameError.style.display = 'block';
        valid = false;
    }

    // Validate password
    if (password === "") {
        const passwordError = document.getElementById('passwordError');
        passwordError.textContent = "Password is required.";
        passwordError.style.display = 'block';
        valid = false;
    }

    return valid;
}

// Admin User Creation Form Validation
function validateUserForm() {
    const username = document.getElementById('username').value.trim();
    const firstName = document.getElementById('first_name').value.trim();
    const lastName = document.getElementById('last_name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const role = document.getElementById('role').value;
    let valid = true;

    // Clear previous errors
    document.getElementById('usernameError').style.display = 'none';
    document.getElementById('firstNameError').style.display = 'none';
    document.getElementById('lastNameError').style.display = 'none';
    document.getElementById('emailError').style.display = 'none';
    document.getElementById('passwordError').style.display = 'none';
    document.getElementById('roleError').style.display = 'none';

    // Validate username
    if (username === "") {
        const usernameError = document.getElementById('usernameError');
        usernameError.textContent = "Username is required.";
        usernameError.style.display = 'block';
        valid = false;
    }

    // Validate first name
    if (firstName === "") {
        const firstNameError = document.getElementById('firstNameError');
        firstNameError.textContent = "First name is required.";
        firstNameError.style.display = 'block';
        valid = false;
    }

    // Validate last name
    if (lastName === "") {
        const lastNameError = document.getElementById('lastNameError');
        lastNameError.textContent = "Last name is required.";
        lastNameError.style.display = 'block';
        valid = false;
    }

    // Validate email
    if (email === "") {
        const emailError = document.getElementById('emailError');
        emailError.textContent = "Email is required.";
        emailError.style.display = 'block';
        valid = false;
    } else {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            const emailError = document.getElementById('emailError');
            emailError.textContent = "Invalid email format.";
            emailError.style.display = 'block';
            valid = false;
        }
    }

    // Validate password
    if (password === "") {
        const passwordError = document.getElementById('passwordError');
        passwordError.textContent = "Password is required.";
        passwordError.style.display = 'block';
        valid = false;
    } else if (password.length < 6) {
        const passwordError = document.getElementById('passwordError');
        passwordError.textContent = "Password must be at least 6 characters.";
        passwordError.style.display = 'block';
        valid = false;
    }

    // Validate role
    if (role === "") {
        const roleError = document.getElementById('roleError');
        roleError.textContent = "Role is required.";
        roleError.style.display = 'block';
        valid = false;
    }

    return valid;
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    // Add validation logic here if needed
    // e.g., check if fields are filled
});

$(document).ready(function() {
    const addEmployeeModal = document.getElementById('addEmployeeModal');
    const uploadModal = document.getElementById('uploadModal');
    const addSingleEmployeeBtn = document.getElementById('addSingleEmployee');
    const uploadEmployeesBtn = document.getElementById('uploadEmployees');
    const closeBtns = document.getElementsByClassName('close');

    addSingleEmployeeBtn.onclick = function() {
        addEmployeeModal.style.display = 'block';
    }

    uploadEmployeesBtn.onclick = function() {
        uploadModal.style.display = 'block';
    }

    for (let closeBtn of closeBtns) {
        closeBtn.onclick = function() {
            addEmployeeModal.style.display = 'none';
            uploadModal.style.display = 'none';
        }
    }

    window.onclick = function(event) {
        if (event.target == addEmployeeModal) {
            addEmployeeModal.style.display = 'none';
        }
        if (event.target == uploadModal) {
            uploadModal.style.display = 'none';
        }
    }

    $('#singleEmployeeForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'add_employee.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Employee added successfully');
                    addEmployeeModal.style.display = 'none';
                    updateDashboardCounts();
                } else {
                    alert('Failed to add employee: ' + response.message);
                }
            }
        });
    });

    $('#bulkUploadForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'add_employee.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Bulk upload complete');
                    uploadModal.style.display = 'none';
                    updateDashboardCounts();
                } else {
                    alert('Upload failed: ' + response.message);
                }
            }
        });
    });

    function updateDashboardCounts() {
        $.ajax({
            url: 'get_employee_counts.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#registered-count').text(response.total);
                $('#marketing-count').text(response.marketing);
            }
        });
    }
});