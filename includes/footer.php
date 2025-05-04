    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-container">
                <div class="footer-item">
                    <h3>About Us</h3>
                    <p>Green Cafe is a cozy spot offering organic, fresh food and beverages in a relaxing environment. We're committed to sustainability and supporting local producers.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <div class="footer-item">
                    <h3>Opening Hours</h3>
                    <p><strong>Monday - Friday:</strong> 8:00 AM - 8:00 PM</p>
                    <p><strong>Saturday:</strong> 9:00 AM - 7:00 PM</p>
                    <p><strong>Sunday:</strong> 10:00 AM - 6:00 PM</p>
                </div>
                
                <div class="footer-item">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="<?php echo $currentPage === 'index.php' ? '' : '../'; ?>index.php">Home</a></li>
                        <li><a href="<?php echo $currentPage === 'index.php' ? 'pages/' : ''; ?>menu.php">Menu</a></li>
                        <li><a href="<?php echo $currentPage === 'index.php' ? 'pages/' : ''; ?>contact.php">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-item">
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Cafe Street, City, Country</p>
                    <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                    <p><i class="fas fa-envelope"></i> info@greencafe.com</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Green Cafe. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?php echo $currentPage === 'index.php' ? '' : '../'; ?>assets/js/main.js"></script>
</body>
</html> 