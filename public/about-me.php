<?php
/**
 * About Me page for ShayNeeo's Portfolio
 */

// Include helper functions
require_once dirname(__DIR__) . '/private/includes/functions.php';

// Page-specific variables
$title = 'About Me';
$additional_css = 'about-me.css,modern-effects.css';
$additional_js = '';

// Include header
include_template('header.php', [
    'title' => $title,
    'additional_css' => $additional_css
]);
?>

<div class="about-container">
    <section class="about-header">
        <h1>About This Project</h1>
        <div class="profile-section">
            <div class="profile-image">
                <img src="<?php echo asset('images/blogger.jpg'); ?>" alt="Project Logo">
            </div>
            <div class="profile-intro">
                <h2>Open-Source Web Tools Platform</h2>
                <p>This project is a community-driven, open-source platform for building modern web portfolios and productivity tools. It is designed for developers, freelancers, agencies, and anyone who wants to showcase their work and leverage powerful web utilities.</p>
            </div>
        </div>
    </section>

    <section class="about-content">
        <div class="content-grid">
            <div class="about-text">
                <h2>Project Story</h2>
                <p>This platform was created to empower the public with a free, extensible, and production-ready web toolkit. It is maintained by contributors from around the world and welcomes new ideas and improvements from the community.</p>
                
                <p>We believe in the power of open-source collaboration, clean code, and user-centered design. Whether you're using this as a personal portfolio, a business toolkit, or an educational resource, you're part of a growing community.</p>
                
                <h2>What This Project Offers</h2>
                <p>It provides a full-stack web solution, including portfolio templates, QR code generation, URL shortening, file management, and more. The architecture is modular and easy to extend for your own needs.</p>
                
                <div class="specialties">
                    <div class="specialty">
                        <h3>🎨 Frontend</h3>
                        <p>Responsive, accessible, and modern UI/UX</p>
                    </div>
                    <div class="specialty">
                        <h3>⚙️ Backend</h3>
                        <p>Secure, scalable, and API-ready PHP backend</p>
                    </div>
                    <div class="specialty">
                        <h3>🚀 Community</h3>
                        <p>Open to contributions, feedback, and new features</p>
                    </div>
                </div>
            </div>
            
            <div class="skills-sidebar">
                <h2>Core Technologies</h2>
                <div class="skill-group">
                    <h3>Languages</h3>
                    <div class="skill-list">
                        <span class="skill-item">PHP</span>
                        <span class="skill-item">JavaScript</span>
                        <span class="skill-item">HTML5</span>
                        <span class="skill-item">CSS3</span>
                        <span class="skill-item">SQL</span>
                    </div>
                </div>
                
                <div class="skill-group">
                    <h3>Frameworks & Libraries</h3>
                    <div class="skill-list">
                        <span class="skill-item">Composer</span>
                        <span class="skill-item">chillerlan/php-qrcode</span>
                        <span class="skill-item">Bootstrap</span>
                    </div>
                </div>
                
                <div class="skill-group">
                    <h3>Tools & Technologies</h3>
                    <div class="skill-list">
                        <span class="skill-item">Docker</span>
                        <span class="skill-item">Nginx</span>
                        <span class="skill-item">Linux</span>
                        <span class="skill-item">Git</span>
                        <span class="skill-item">MySQL</span>
                    </div>
                </div>
                <div class="contact-info">
                    <h3>Get Involved</h3>
                    <p>This is a public, open-source project. Join us on GitHub or Discord to contribute, ask questions, or share your ideas!</p>
                    <div class="contact-links">
                        <a href="https://github.com/ShayNeeo/Portfolio-template" class="contact-link github-link" target="_blank" rel="noopener noreferrer">
                            🌐 GitHub
                        </a>
                        <a href="https://discord.gg/UbtHVJza" class="contact-link discord-link" target="_blank" rel="noopener noreferrer">
                            💬 Discord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
// Include footer
include_template('footer.php', [
    'additional_js' => $additional_js
]);
?>
