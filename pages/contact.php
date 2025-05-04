<?php
// Include header
require_once '../includes/header.php';

// Initialize variables
$name = $email = $subject = $message = '';
$nameErr = $emailErr = $messageErr = '';
$successMsg = '';
$errorMsg = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate name
    if (empty($_POST['name'])) {
        $nameErr = 'Name is required';
    } else {
        $name = sanitizeInput($_POST['name']);
    }
    
    // Validate email
    if (empty($_POST['email'])) {
        $emailErr = 'Email is required';
    } else {
        $email = sanitizeInput($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = 'Invalid email format';
        }
    }
    
    // Validate message
    if (empty($_POST['message'])) {
        $messageErr = 'Message is required';
    } else {
        $message = sanitizeInput($_POST['message']);
    }
    
    // Get subject (optional)
    $subject = !empty($_POST['subject']) ? sanitizeInput($_POST['subject']) : '';
    
    // If no errors, submit the message
    if (empty($nameErr) && empty($emailErr) && empty($messageErr)) {
        if (submitContactMessage($name, $email, $subject, $message)) {
            $successMsg = 'Your message has been sent successfully!';
            $name = $email = $subject = $message = ''; // Clear the form
        } else {
            $errorMsg = 'There was an error sending your message. Please try again later.';
        }
    }
}
?>

<!-- Contact Banner -->
<section class="hero" style="height: 50vh; background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/images/contact-banner.jpg');">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Contact Us</h1>
            <p class="hero-subtitle">Get in touch with us or visit our cafe</p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="section">
    <div class="container">
        <div class="contact-container">
            <div class="contact-info">
                <h3>Find Us</h3>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-text">
                        <h4>Address</h4>
                        <p>123 Cafe Street, City, Country</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-text">
                        <h4>Phone</h4>
                        <p>+1 (555) 123-4567</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-text">
                        <h4>Email</h4>
                        <p>info@greencafe.com</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-text">
                        <h4>Opening Hours</h4>
                        <p><strong>Monday - Friday:</strong> 8:00 AM - 8:00 PM</p>
                        <p><strong>Saturday:</strong> 9:00 AM - 7:00 PM</p>
                        <p><strong>Sunday:</strong> 10:00 AM - 6:00 PM</p>
                    </div>
                </div>
                
                <!-- Google Map (replace with your actual location) -->
                <div style="margin-top: 30px; border-radius: 8px; overflow: hidden;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s123%20Street%2C%20New%20York%2C%20NY%2010001%2C%20USA!5e0!3m2!1sen!2sus!4v1590512734256!5m2!1sen!2sus" width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                </div>
            </div>
            
            <div class="contact-form">
                <h3>Send Us a Message</h3>
                
                <?php if (!empty($successMsg)): ?>
                    <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <?php echo $successMsg; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errorMsg)): ?>
                    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <?php echo $errorMsg; ?>
                    </div>
                <?php endif; ?>
                
                <form id="contactForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>">
                        <span id="nameError" class="error-message" style="color: #dc3545;"><?php echo $nameErr; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
                        <span id="emailError" class="error-message" style="color: #dc3545;"><?php echo $emailErr; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject (Optional)</label>
                        <input type="text" class="form-control" id="subject" name="subject" value="<?php echo $subject; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5"><?php echo $message; ?></textarea>
                        <span id="messageError" class="error-message" style="color: #dc3545;"><?php echo $messageErr; ?></span>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
require_once '../includes/footer.php';
?> 