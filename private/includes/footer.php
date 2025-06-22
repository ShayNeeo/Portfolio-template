<?php
/**
 * Common footer template
 */
?>    </main>
    <footer>
        <div class="social-links social-links-enhanced">
            <!-- Example social links for community demonstration. Replace or extend as needed. -->
            <a href="https://github.com/ShayNeeo/Portfolio-template" target="_blank" rel="noopener noreferrer" aria-label="GitHub" class="social-link github" data-platform="GitHub">
                <img src="<?php echo asset('images/github.jpg'); ?>" alt="GitHub" class="social-icon">
            </a>
            <a href="https://discord.gg/UbtHVJza" target="_blank" rel="noopener noreferrer" aria-label="Discord" class="social-link discord" data-platform="Discord">
                <img src="<?php echo asset('images/logo.jpg'); ?>" alt="Discord" class="social-icon">
            </a>
        </div>
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        <p class="footer-tagline">Open-source project. Built with ❤️ by the community.</p>
    </footer>
    <script src="<?php echo asset('js/scripts.js'); ?>"></script>
    <?php if (isset($additional_js) && !empty($additional_js)): ?>
        <script src="<?php echo asset('js/' . $additional_js); ?>"></script>
    <?php endif; ?>
</body>
</html>
