<!DOCTYPE html>
<html lang="en" data-theme="dark"> <!-- data-theme initialized to dark by default -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Portfolio</title>
    <!-- 
      Separation of Concerns: CSS is loaded from an external file in the assets directory.
      We do not use inline styles or <style> tags here.
    -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Main Navigation Header -->
    <header>
        <a href="index.php" class="logo">~/DEV_PORTFOLIO</a>
        <nav>
            <ul>
                <li><a href="#projects">Projects</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="admin/login.php">Login</a></li>
                <!-- Feature A: Dark Mode Toggle Button -->
                <li><button id="theme-toggle" class="btn" aria-label="Toggle Dark/Light Mode">Toggle Theme</button></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Hero / Introduction Section -->
        <section class="intro">
            <span class="context-path">~/home/intro</span>
            <div class="intro-content">
                <div class="intro-text">
                    <h1>Hi, I'm Mahi! I build robust, scalable <span class="accent">software solutions.</span><span class="cursor">|</span></h1>
                    <p>A software architect and full-stack engineer specializing in distributed systems and performance-critical applications. Focused on translating complex business requirements into elegant, maintainable code architectures.</p>
                    
                    <div class="action-buttons">
                        <a href="#projects" class="btn">VIEW_PROJECTS</a>
                        <a href="#contact" class="btn">SET_IN_TOUCH</a>
                    </div>
                </div>
                <div class="intro-image">
                    <img src="https://ui-avatars.com/api/?name=Mahi&background=1a1a1e&color=82aaff&size=200&rounded=true" alt="Mahi Profile Picture" class="profile-pic">
                </div>
            </div>
        </section>

        <!-- Feature B: Dynamic Project Rendering Container -->
        <section id="projects">
            <div class="section-header">
                <div>
                    <span class="context-path">~/PROJECTS/FEATURED</span>
                    <h2>Selected Architecture</h2>
                </div>
            </div>
            
            <!-- 
              This is the empty container. 
              main.js will fetch data from api/projects.php and populate this div.
              Why? To make the page load instantly (HTML first) and fetch data asynchronously without blocking the user interface.
            -->
            <div id="project-grid" class="projects-grid">
                <!-- Data will be injected here via Vanilla JS DOM manipulation -->
                <p>Loading architecture data...</p>
            </div>
        </section>

        <!-- Feature C: Contact Form Section -->
        <section id="contact" style="margin-top: 6rem;">
            <span class="context-path">~/root/communications/contact_form</span>
            <h1>Let's Connect<span class="accent">|</span></h1>
            <p>Have a technical inquiry or a collaboration proposal? Drop a message into the buffer and I'll get back to you shortly.</p>

            <!-- 
              The form uses semantic inputs. 
              Notice the absence of the 'action' and 'method' attributes. 
              Why? Because we are hijacking the submission process using JavaScript (e.preventDefault()) 
              in main.js to submit the data asynchronously via the Fetch API.
            -->
            <form id="contact-form">
                <div class="form-meta">03//</div>
                
                <!-- Container for displaying success/error messages dynamically -->
                <div id="form-message" class="message" style="display: none;"></div>

                <div class="form-group">
                    <label for="name">NAME_STRING</label>
                    <input type="text" id="name" name="name" placeholder="IDENTIFY YOURSELF" required>
                </div>

                <div class="form-group">
                    <label for="email">EMAIL_ADDRESS</label>
                    <!-- type="email" provides basic built-in browser validation, but we will also use Regex in JS -->
                    <input type="email" id="email" name="email" placeholder="ADDRESS_FOR_REPLY" required>
                </div>

                <div class="form-group">
                    <label for="message">MESSAGE_BUFFER</label>
                    <textarea id="message" name="message" rows="6" placeholder="TYPE YOUR MESSAGE HERE..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">EXECUTE_SEND >_</button>
            </form>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div>© <?php echo date("Y"); ?> MAHI_CORE // ALL RIGHTS RESERVED</div>
        <div class="footer-links">
            <a href="https://github.com/Mahi-Ibrahim" target="_blank" rel="noopener noreferrer">GITHUB</a>
            <a href="https://linkedin.com/in/mahi-ibrahim" target="_blank" rel="noopener noreferrer">LINKEDIN</a>
        </div>
    </footer>

    <!-- 
      Separation of Concerns: JavaScript is loaded from an external file at the end of the body.
      Why at the end? So the browser parses and renders all HTML first, making the page appear faster, 
      and ensuring the DOM elements exist when the script tries to attach event listeners to them.
    -->
    <script src="assets/js/main.js"></script>
</body>
</html>
