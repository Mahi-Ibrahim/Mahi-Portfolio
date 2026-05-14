/**
 * assets/js/admin.js
 * 
 * PURPOSE:
 * Contains the client-side logic specifically for the admin panel.
 * Handles Login AJAX requests, fetching the project list for the dashboard,
 * adding new projects, deleting projects, and handling logout.
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // Check which page we are on by looking for specific elements
    const loginForm = document.getElementById('admin-login-form');
    if (loginForm) {
        initLogin(loginForm);
    }

    const projectForm = document.getElementById('add-project-form');
    if (projectForm) {
        // If the project form exists, we are on the dashboard
        initAdminDashboard(projectForm);
    }

    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        initLogout(logoutBtn);
    }
});

/**
 * Handle Admin Login via AJAX
 */
function initLogin(form) {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const msgContainer = document.getElementById('login-message');
        msgContainer.style.display = 'none';
        msgContainer.className = 'message';
        
        const formData = new FormData(form);

        fetch('../api/auth.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Feature D: If auth is successful, redirect to dashboard
                window.location.href = data.redirect;
            } else {
                msgContainer.textContent = data.message;
                msgContainer.classList.add('error');
                msgContainer.style.display = 'block';
            }
        })
        .catch(err => {
            console.error(err);
            msgContainer.textContent = 'SYSTEM ERROR. CHECK CONSOLE.';
            msgContainer.classList.add('error');
            msgContainer.style.display = 'block';
        });
    });
}

/**
 * Initialize Dashboard features: Adding and Fetching projects
 */
function initAdminDashboard(form) {
    // 1. Fetch existing projects immediately on load
    fetchAdminProjects();

    // 2. Handle adding new projects via AJAX
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const msgContainer = document.getElementById('project-form-message');
        msgContainer.style.display = 'none';
        msgContainer.className = 'message';

        const formData = new FormData(form);

        fetch('../api/projects.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                msgContainer.textContent = data.message;
                msgContainer.classList.add('success');
                msgContainer.style.display = 'block';
                form.reset();
                // Refresh the project list dynamically!
                fetchAdminProjects();
            } else {
                msgContainer.textContent = data.message;
                msgContainer.classList.add('error');
                msgContainer.style.display = 'block';
            }
        })
        .catch(err => console.error(err));
    });
}

/**
 * Fetch projects and populate the table dynamically.
 * Attach event listeners to the generated "Delete" buttons.
 */
function fetchAdminProjects() {
    const tbody = document.getElementById('admin-projects-list');
    
    fetch('../api/projects.php')
        .then(response => response.json())
        .then(projects => {
            let html = '';
            
            if(projects.length === 0) {
                html = '<tr><td colspan="3">No projects found.</td></tr>';
            } else {
                projects.forEach(project => {
                    html += `
                        <tr>
                            <td>${project.id}</td>
                            <td>${project.title}</td>
                            <td>
                                <button class="action-btn delete-btn" data-id="${project.id}">[ DELETE ]</button>
                            </td>
                        </tr>
                    `;
                });
            }
            tbody.innerHTML = html;

            // Attach event listeners to newly created delete buttons
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    if (confirm('WARNING: THIS ACTION IS IRREVERSIBLE. PROCEED?')) {
                        deleteProject(id);
                    }
                });
            });
        });
}

/**
 * Handle deleting a project via AJAX (DELETE method)
 */
function deleteProject(id) {
    // We append the ID as a query parameter for the DELETE request
    fetch(`../api/projects.php?id=${id}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Re-fetch the list to update the UI
            fetchAdminProjects();
        } else {
            alert('ERROR: ' + data.message);
        }
    })
    .catch(err => console.error(err));
}

/**
 * Handle Logout via AJAX POST request
 */
function initLogout(btn) {
    btn.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('action', 'logout');

        fetch('../api/auth.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = data.redirect;
            }
        });
    });
}
