/**
 * assets/js/main.js
 * 
 * PURPOSE:
 * Contains the public-facing logic for the portfolio.
 * Implements Feature A (Dark Mode Toggle), Feature B (Dynamic Project Rendering via Fetch),
 * and Feature C (Contact Form validation and AJAX submission).
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // Initialize Features
    initThemeToggle();
    fetchProjects();
    initContactForm();

    // Minor UI effect: blinking cursor
    const cursor = document.querySelector('.cursor');
    if (cursor) {
        setInterval(() => {
            cursor.style.opacity = cursor.style.opacity === '0' ? '1' : '0';
        }, 500);
    }
});

/**
 * FEATURE A: Dark/Light Mode Toggle
 * WHY localStorage? By saving the preference in localStorage, the browser remembers
 * the user's choice even if they close the tab and return later.
 */
function initThemeToggle() {
    const toggleBtn = document.getElementById('theme-toggle');
    const htmlTag = document.documentElement; // Targets the <html> element
    
    // Check if user has a saved preference, otherwise default to dark
    const savedTheme = localStorage.getItem('portfolio_theme') || 'dark';
    htmlTag.setAttribute('data-theme', savedTheme);
    
    toggleBtn.addEventListener('click', () => {
        // Read current theme
        const currentTheme = htmlTag.getAttribute('data-theme');
        // Determine new theme
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        // Apply new theme and save to localStorage
        htmlTag.setAttribute('data-theme', newTheme);
        localStorage.setItem('portfolio_theme', newTheme);
    });
}

/**
 * FEATURE B: Dynamic Project Rendering (AJAX)
 * 
 * MINI-TUTORIAL: The Fetch API
 * 1. fetch() makes a network request to our PHP endpoint (api/projects.php). It returns a Promise.
 * 2. .then(response => response.json()) intercepts the response. If the server responded properly,
 *    we parse the JSON body into a JavaScript object/array.
 * 3. .then(data => ...) gives us the actual data array. We iterate over it and use template literals
 *    to construct HTML strings for each project card.
 * 4. Finally, we inject the combined HTML string into the DOM using innerHTML. This happens seamlessly
 *    without reloading the webpage.
 */
function fetchProjects() {
    const grid = document.getElementById('project-grid');
    if (!grid) return;

    fetch('api/projects.php')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(projects => {
            if (projects.length === 0) {
                grid.innerHTML = '<p>No projects initialized in database.</p>';
                return;
            }

            let html = '';
            projects.forEach((project, index) => {
                // Parse tags if they exist
                let tagsHtml = '';
                if (project.tech_stack) {
                    const tags = project.tech_stack.split(',');
                    tagsHtml = '<div class="tech-tags">';
                    tags.forEach(tag => {
                        tagsHtml += `<span class="tech-tag">[ ${tag.trim()} ]</span>`;
                    });
                    tagsHtml += '</div>';
                }

                // Construct Card HTML
                html += `
                    <article class="project-card">
                        <div style="text-align: right; font-family: var(--font-mono); font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1rem;">
                            ${String(index + 1).padStart(2, '0')}//
                        </div>
                        <h3>${escapeHTML(project.title)}</h3>
                        <p>${escapeHTML(project.description)}</p>
                        ${tagsHtml}
                        <div class="project-links">
                            ${project.live_url ? `<a href="${project.live_url}" target="_blank">LIVE_PREVIEW</a>` : ''}
                            ${project.repo_url ? `<a href="${project.repo_url}" target="_blank">SOURCE_CODE</a>` : ''}
                        </div>
                    </article>
                `;
            });
            grid.innerHTML = html;
        })
        .catch(error => {
            console.error('Fetch error:', error);
            grid.innerHTML = '<p class="error">Failed to load projects. Ensure the database API is reachable.</p>';
        });
}

/**
 * FEATURE C: Contact Form Validation & AJAX Submission
 */
function initContactForm() {
    const form = document.getElementById('contact-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        // e.preventDefault() stops the browser from doing a traditional form POST,
        // which would cause a full page refresh. We want to handle it asynchronously.
        e.preventDefault();

        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const message = document.getElementById('message').value.trim();
        const msgContainer = document.getElementById('form-message');

        // Reset message container
        msgContainer.style.display = 'none';
        msgContainer.className = 'message';

        // Front-End Validation
        if (!name || !email || !message) {
            showFormMessage('ERROR: ALL FIELDS ARE REQUIRED.', 'error');
            return;
        }

        // Email Regex Validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showFormMessage('ERROR: INVALID EMAIL FORMAT.', 'error');
            return;
        }

        showFormMessage('PROCESSING...', '');

        // Construct FormData object to easily send input values via Fetch
        const formData = new FormData(this);

        fetch('api/contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showFormMessage(data.message, 'success');
                form.reset(); // Clear form on success
            } else {
                showFormMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showFormMessage('ERROR: SERVER COMMUNICATION FAILURE.', 'error');
        });
    });
}

/**
 * Helper utility to show form messages
 * @param {string} msg Text to display
 * @param {string} type CSS class type ('error' or 'success')
 */
function showFormMessage(msg, type) {
    const container = document.getElementById('form-message');
    container.textContent = msg;
    if (type) container.classList.add(type);
    container.style.display = 'block';
}

/**
 * Helper utility to escape HTML to prevent XSS attacks when injecting data into the DOM.
 * WHY? If a user puts "<script>alert('hack')</script>" into the database, injecting it raw
 * via innerHTML would execute the script. We convert < to &lt; so it renders safely as text.
 */
function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/[&<>'"]/g, 
        tag => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#39;',
            '"': '&quot;'
        }[tag] || tag)
    );
}
