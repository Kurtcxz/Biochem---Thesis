    // Function to fetch user data and populate the form
    function loadUserData(userId) {
        // Simulating fetching data from a server (replace with fetch() or Ajax for real API)
        const mockUserData = {
            first_name: 'John',
            last_name: 'Doe',
            email: 'john.doe@example.com',
            role: 'admin'
        };

        // Fill in the form with the user data
        document.getElementById('first_name').value = mockUserData.first_name;
        document.getElementById('last_name').value = mockUserData.last_name;
        document.getElementById('email').value = mockUserData.email;
        document.getElementById('role').value = mockUserData.role;
    }

    // Function to handle form submission (update user)
    document.getElementById('edit-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form from submitting normally

        // Get form values
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        const email = document.getElementById('email').value;
        const role = document.getElementById('role').value;

        // Simple validation: Check if any field is empty
        if (!firstName || !lastName || !email || !role) {
            document.getElementById('error-message').innerText = 'All fields are required!';
            document.getElementById('error-message').style.display = 'block';
            return;
        }

        // Simulate sending updated data to a server (use fetch() or Ajax for real update)
        setTimeout(function() {
            // Simulate successful form submission
            document.getElementById('error-message').style.display = 'none';
            document.getElementById('success-message').innerText = 'User updated successfully!';
            document.getElementById('success-message').style.display = 'block';
        }, 1000);
    });

    // Simulate page load and load user data (use actual userId here)
    window.onload = function() {
        const userId = 123; // Replace this with the dynamic user ID from your URL or context
        loadUserData(userId);
    };

    function setDataTables() {
        if ($.fn.dataTable.isDataTable('#userTable')) {
            $('#userTable').DataTable().destroy(); // Destroy existing instance before reinitializing
        }
    
        var table = $('#userTable').DataTable({
            "order": [], // Disable initial sorting
            "autoWidth": false, // Disable automatic column width calculation
            "responsive": true, // Enable responsiveness
            "paging": true, // Enable pagination
            "searching": true, // Enable search functionality
            "ordering": true, // Enable column sorting
            "lengthChange": true, // Allow user to change rows per page
            "info": true, // Display table information like showing X of Y entries
            "columnDefs": [
                { "targets": 0, "width": "20%", "orderable": true },  // Name (first_name and last_name)
                { "targets": 1, "width": "25%", "orderable": true },  // Email
                { "targets": 2, "width": "15%", "orderable": true },  // Role
                { "targets": 3, "width": "15%", "orderable": true },  // Status
                { "targets": 4, "width": "25%", "orderable": false, "className": "text-center" } // Actions
            ]
        });
    
        // Adjust table layout on sidebar toggle or resize
        $(window).on('resize', function () {
            table.columns.adjust().draw(); // Redraw the DataTable to adjust the columns
        });
    }
