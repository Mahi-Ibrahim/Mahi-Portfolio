# Full-Stack Web Portfolio

## Project Explanation
This project is a dynamic, full-stack web portfolio designed to showcase software development skills, projects, and contact information. It features a custom "terminal/developer" dark-mode aesthetic that seamlessly switches to a light theme. The application consists of a public-facing website for visitors and a secure System Console (admin dashboard) where the site owner can manage projects and view contact inquiries in real time.

## Technologies Used
The project was built from scratch without any external frameworks or libraries (zero dependencies), adhering strictly to core web technologies:
- **HTML5**: For semantic and accessible page structure.
- **CSS3 (Vanilla)**: For responsive design, Flexbox/Grid layouts, and CSS Custom Properties (Variables) for the Light/Dark mode toggling.
- **JavaScript (ES6+)**: For client-side interactivity, form validation, and communicating with the server asynchronously via the `fetch()` API.
- **PHP 8+**: For robust server-side API logic, routing, and secure session management.
- **MySQL**: As the relational database to persistently store projects, messages, and admin credentials.
- **PDO (PHP Data Objects)**: Used securely with prepared statements to connect PHP and MySQL, completely preventing SQL injection attacks.

## How It Was Built
The project was developed step-by-step using a strict "Separation of Concerns" architecture:

1. **Database Foundation**: We established a secure PDO connection to MySQL. A utility script (`setup.php`) was created to quickly initialize the database schema, generating tables for `projects`, `messages`, and `admins` and utilizing secure cryptographic hashing for passwords.
2. **Frontend Architecture**: We built the public interface using semantic HTML. The styling was modularized into CSS files utilizing variables for instantaneous theme switching. JavaScript was implemented to handle UI toggles and make AJAX requests, ensuring the webpage never reloads when fetching data or submitting forms.
3. **Backend APIs**: We created RESTful PHP endpoints (`api/projects.php`, `api/contact.php`, `api/auth.php`). These endpoints receive asynchronous requests from the JavaScript frontend, rigorously validate the data on the server side, and securely mutate the MySQL database.
4. **Secure Admin Dashboard**: We implemented a protected Content Management System (CMS). A login system verifies hashed passwords and initiates secure PHP sessions. Once authenticated, the admin can view incoming messages and seamlessly add or delete projects, which instantly updates the public homepage via dynamic DOM manipulation.

## Local Setup Instructions
1. Ensure you have XAMPP (or a similar environment) installed.
2. Move this project folder to your local server directory (e.g., `C:\xampp\htdocs\Mahi-Portfolio`).
3. Start Apache and MySQL in your XAMPP Control Panel.
4. Run `http://localhost/Mahi-Portfolio/setup.php` in your browser to initialize the database and create the default admin account.
5. Visit `http://localhost/Mahi-Portfolio/` to view the public site, and `http://localhost/Mahi-Portfolio/admin/login.php` to access the dashboard.