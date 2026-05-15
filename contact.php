<?php
/**
 * Motorfix Injection Services - Contact Page
 *
 * Features:
 * - SMTP contact form using PHPMailer.
 * - Sends notification to admin.
 * - Sends confirmation email to visitor (if email is provided).
 * - Saves submissions to the 'contact_submissions' table.
 * - Displays all branch locations.
 *
 * @version 2.1
 * @date Wednesday, October 22, 2025
 */

// --- 1. SETUP & DEPENDENCIES ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Load Composer's autoloader
require_once 'vendor/autoload.php';
// Load database connection
require_once 'db.php'; 
// Load header
require_once 'head.php'; 

// Load environment variables (db.php should handle this)
$feedback_message = '';
$feedback_type = ''; // 'success' or 'error'

// --- 2. FORM PROCESSING ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL); 
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if (empty($name) || empty($subject) || empty($message)) {
        $feedback_message = 'Please fill out all required fields (Name, Inquiry Type, Message).';
        $feedback_type = 'error';
    } else {
        $mail_admin = new PHPMailer(true);

        try {
            // --- 3. SMTP & EMAIL SENDING (TO ADMIN) ---
            
            // Server settings
            // $mail_admin->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
            $mail_admin->isSMTP();
            $mail_admin->Host       = $_ENV['SMTP_HOST'];
            $mail_admin->SMTPAuth   = true;
            $mail_admin->Username   = $_ENV['SMTP_USER'];
            $mail_admin->Password   = $_ENV['SMTP_PASS'];
            $mail_admin->SMTPSecure = $_ENV['SMTP_SECURE'];
            $mail_admin->Port       = $_ENV['SMTP_PORT'];

            // Recipients
            $mail_admin->setFrom($_ENV['SMTP_USER'], 'Motorfix Website Inquiry'); 
            $mail_admin->addAddress('info@motorfix.co.ke', 'Motorfix Admin');
            if ($email) {
                 $mail_admin->addReplyTo($email, $name); 
            }

            // Content
            $mail_admin->isHTML(true);
            $mail_admin->Subject = 'New Contact Form Inquiry: ' . htmlspecialchars($subject);
            
            // Admin Email Body
            $mail_admin->Body    = "
                <html lang='en'>
                <head><style>body { font-family: Arial, sans-serif; line-height: 1.6; } .container { width: 90%; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; } h2 { color: #0A4A2A; } .field { margin-bottom: 10px; } .label { font-weight: bold; } </style></head>
                <body>
                    <div class='container'>
                        <h2>New Website Inquiry</h2>
                        <p>You have received a new message from your website contact form.</p>
                        <hr>
                        <div class='field'><span class='label'>Name:</span> " . htmlspecialchars($name) . "</div>
                        <div class='field'><span class='label'>Email:</span> " . htmlspecialchars($email ?: 'Not provided') . "</div>
                        <div class='field'><span class='label'>Phone:</span> " . htmlspecialchars($phone ?: 'Not provided') . "</div>
                        <div class='field'><span class='label'>Inquiry Type:</span> " . htmlspecialchars($subject) . "</div>
                        <div class='field'><span class='label'>Message:</span><br>" . nl2br(htmlspecialchars($message)) . "</div>
                    </div>
                </body>
                </html>";
            
            $mail_admin->AltBody = "New Inquiry:\nName: " . $name . "\nEmail: " . ($email ?: 'Not provided') . "\nPhone: " . ($phone ?: 'Not provided') . "\nSubject: " . $subject . "\nMessage: " . $message;

            $mail_admin->send();

            // --- 4. SEND CONFIRMATION EMAIL (TO VISITOR) ---
            if ($email) {
                try {
                    $mail_visitor = new PHPMailer(true);
                    // SMTP Settings (must be repeated for new instance)
                    $mail_visitor->isSMTP();
                    $mail_visitor->Host       = $_ENV['SMTP_HOST'];
                    $mail_visitor->SMTPAuth   = true;
                    $mail_visitor->Username   = $_ENV['SMTP_USER'];
                    $mail_visitor->Password   = $_ENV['SMTP_PASS'];
                    $mail_visitor->SMTPSecure = $_ENV['SMTP_SECURE'];
                    $mail_visitor->Port       = $_ENV['SMTP_PORT'];

                    // Recipients
                    $mail_visitor->setFrom($_ENV['SMTP_USER'], 'Motorfix Support');
                    $mail_visitor->addAddress($email, $name); // Send TO the visitor

                    // Content
                    $mail_visitor->isHTML(true);
                    $mail_visitor->Subject = 'We have received your inquiry | Motorfix';
                    
                    // Beautiful HTML Email for Visitor
                    $mail_visitor->Body = "
                        <html lang='en'>
                        <head>
                            <meta charset='UTF-8'>
                            <style>
                                body { font-family: 'Inter', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                                .wrapper { width: 100%; background-color: #f8f9fa; padding: 40px 0; }
                                .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e5e5e5; border-radius: 12px; overflow: hidden; }
                                .header { background: #0A4A2A; padding: 30px 40px; text-align: center; }
                                .header img { height: 50px; width: auto; }
                                .content { padding: 40px; }
                                .content h1 { color: #0A4A2A; font-size: 24px; margin-top: 0; }
                                .content p { font-size: 16px; margin-bottom: 20px; }
                                .message-box { background-color: #f8f9fa; border: 1px solid #e5e5e5; border-radius: 8px; padding: 20px; margin-top: 25px; }
                                .message-box p { font-size: 15px; }
                                .footer { background-color: #f8f9fa; padding: 30px 40px; text-align: center; color: #6c757d; font-size: 13px; border-top: 1px solid #e5e5e5; }
                            </style>
                        </head>
                        <body>
                            <div class='wrapper'>
                                <div class='container'>
                                    <div class='header'>
                                        <img src='https://www.motorfix.co.ke/media/slogo.png' alt='Motorfix Logo'>
                                    </div>
                                    <div class='content'>
                                        <h1>Thank you, " . htmlspecialchars($name) . "!</h1>
                                        <p>We have successfully received your message. Our team will review your inquiry and get back to you as soon as possible.</p>
                                        <p>For your records, here is a copy of your message:</p>
                                        
                                        <div class='message-box'>
                                            <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
                                            <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
                                        </div>
                                    </div>
                                    <div class='footer'>
                                        &copy; " . date('Y') . " Motorfix Injection Services. All rights reserved.<br>
                                        Jekima Plaza, Jogoo Road, Nairobi
                                    </div>
                                </div>
                            </div>
                        </body>
                        </html>";

                    $mail_visitor->send();
                } catch (Exception $e) {
                    // Log if visitor email fails, but don't stop the success
                    error_log("Failed to send visitor confirmation email: " . $mail_visitor->ErrorInfo);
                }
            }
            
            // --- 5. SAVE TO DATABASE ---
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO contact_submissions (name, email, phone, subject, message, is_read) 
                    VALUES (:name, :email, :phone, :subject, :message, 0)
                ");
                $stmt->execute([
                    ':name' => $name,
                    ':email' => $email ?: 'not.provided@motorfix.co.ke', // Use a placeholder if empty
                    ':phone' => $phone,
                    ':subject' => $subject,
                    ':message' => $message
                ]);
                
                $feedback_message = 'Thank you! Your message has been sent successfully. We will get back to you soon.';
                $feedback_type = 'success';
                
            } catch (PDOException $e) {
                // Email sent, but DB save failed. Log this critical error.
                error_log("CRITICAL: Email sent but DB submission failed: " . $e->getMessage());
                $feedback_message = 'Your message was sent, but a database error occurred. Please contact us directly.';
                $feedback_type = 'error';
            }

        } catch (Exception $e) {
            $feedback_message = "Message could not be sent. Please try again later. Mailer Error: {$mail_admin->ErrorInfo}";
            $feedback_type = 'error';
            error_log("PHPMailer Error (Admin): " . $mail_admin->ErrorInfo);
        }
    }
}
?>

<style>
    /* Motorfix Contact Page Styles - mfx-contact- */
    :root {
        --mfx-dark-green: #0A4A2A;
        --mfx-green: #28a745;
        --mfx-black: #1a1a1a;
        --mfx-grey: #6c757d;
        --mfx-light-grey: #f8f9fa;
        --mfx-white: #ffffff;
        --mfx-border: #e5e5e5;
        --mfx-shadow: rgba(0, 0, 0, 0.08);
    }
    
    .mfx-contact-wrapper *, .mfx-contact-wrapper *::before, .mfx-contact-wrapper *::after {
        box-sizing: border-box;
    }

    .mfx-contact-wrapper {
        max-width: 1200px;
        margin: 30px auto;
        padding: 0 20px 60px;
        font-family: 'Inter', sans-serif;
    }

    .mfx-contact-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .mfx-contact-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--mfx-dark-green);
        margin-bottom: 10px;
    }

    .mfx-contact-header p {
        font-size: 1.1rem;
        color: var(--mfx-grey);
        max-width: 600px;
        margin: 0 auto;
    }

    .mfx-contact-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 40px;
    }

    /* Contact Form Styles */
    .mfx-contact-form-col {
        background: var(--mfx-white);
        border: 1px solid var(--mfx-border);
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 15px var(--mfx-shadow);
    }

    .mfx-contact-form-col h2 {
        font-size: 1.8rem;
        font-weight: 600;
        color: var(--mfx-black);
        margin-top: 0;
        margin-bottom: 25px;
    }
    
    .mfx-form-feedback {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 500;
    }
    .mfx-form-feedback.success {
        background: #e8f5e9;
        color: var(--mfx-dark-green);
    }
    .mfx-form-feedback.error {
        background: #fbe9e7;
        color: #c9302c;
    }

    .mfx-form-group {
        margin-bottom: 20px;
    }
    
    .mfx-form-group.required label::after {
        content: ' *';
        color: #c9302c;
    }

    .mfx-form-group label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--mfx-black);
        margin-bottom: 8px;
    }

    .mfx-form-input,
    .mfx-form-select,
    .mfx-form-textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--mfx-border);
        border-radius: 8px;
        font-size: 1rem;
        font-family: 'Inter', sans-serif;
        background: var(--mfx-light-grey);
        color: var(--mfx-black);
        transition: all 0.3s ease;
        box-sizing: border-box; 
    }

    .mfx-form-input:focus,
    .mfx-form-select:focus,
    .mfx-form-textarea:focus {
        outline: none;
        border-color: var(--mfx-dark-green);
        background: var(--mfx-white);
        box-shadow: 0 0 0 3px rgba(10, 74, 42, 0.1);
    }

    .mfx-form-textarea {
        resize: vertical;
        min-height: 140px;
    }

    .mfx-btn-submit {
        width: 100%;
        padding: 15px 30px;
        background: var(--mfx-dark-green);
        color: var(--mfx-white);
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .mfx-btn-submit:hover {
        background: var(--mfx-green);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    /* Info Column Styles */
    .mfx-contact-info-col {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .mfx-contact-info-card {
        background: var(--mfx-light-grey);
        border: 1px solid var(--mfx-border);
        border-radius: 12px;
        padding: 25px;
    }
    
    .mfx-contact-info-card h3 {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--mfx-dark-green);
        margin-top: 0;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .mfx-contact-info-card h3 i {
        font-size: 1.1rem;
    }
    
    .mfx-contact-info-card ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .mfx-contact-info-card li {
        /* MODIFIED: Reduced margin */
        margin-bottom: 5px; /* MODIFIED: Reduced spacing */
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .mfx-contact-info-card li i {
        color: var(--mfx-grey);
        width: 20px;
        text-align: center;
    }
    
    .mfx-contact-info-card p,
    .mfx-contact-info-card ul a { /* MODIFIED: Made selector more specific */
        font-size: 1rem;
        color: var(--mfx-black);
        text-decoration: none;
        line-height: 1.6;
    }

    /* ADDED: Rule to compact branch list */
    .mfx-contact-info-card ul li p {
        line-height: 1.4; 
        margin: 0;
    }
    
    .mfx-contact-info-card a:hover {
        color: var(--mfx-dark-green);
        text-decoration: underline;
    }

    .mfx-get-directions-btn {
        display: inline-block;
        padding: 10px 18px;
        background: var(--mfx-dark-green);
        /* MODIFIED: Added white color */
        color: var(--mfx-white); 
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        margin-top: 15px;
        transition: all 0.3s ease;
    }
    .mfx-get-directions-btn:hover {
        background: var(--mfx-green);
        transform: translateY(-2px);
    }


    /* Map Section */
    .mfx-contact-map-wrapper {
        margin-top: 50px;
        text-align: center;
    }
    
    .mfx-contact-map-wrapper h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--mfx-dark-green);
        margin-bottom: 25px;
    }
    
    .mfx-contact-map-iframe {
        width: 100%;
        max-width: 800px;
        height: 450px;
        border: 1px solid var(--mfx-border);
        border-radius: 12px;
        overflow: hidden;
        margin: 0 auto; 
    }
    
    .mfx-contact-map-iframe iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }
    
    /* Responsive Design */
    @media (max-width: 991px) {
        .mfx-contact-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .mfx-contact-info-col {
            order: -1; 
        }
    }
    
    @media (max-width: 480px) {
        .mfx-contact-header h1 {
            font-size: 2rem;
        }
        .mfx-contact-header p {
            font-size: 1rem;
        }
        .mfx-contact-form-col {
            padding: 20px;
        }
        .mfx-contact-info-card {
            padding: 20px;
        }
        .mfx-contact-map-iframe {
            height: 350px; 
        }
    }

</style>

<div class="mfix-body-content-spacer">
    <div class="mfx-contact-wrapper">

        <header class="mfx-contact-header">
            <h1>Contact Us</h1>
            <p>We're here to help. Whether you have a question about our products, need support, or want to discuss a partnership, reach out to us!</p>
        </header>

        <div class="mfx-contact-grid">

            <!-- Contact Form Column -->
            <div class="mfx-contact-form-col">
                <h2>Send us a Message</h2>
                
                <?php if ($feedback_message): ?>
                    <div class="mfx-form-feedback <?php echo $feedback_type; ?>">
                        <?php echo htmlspecialchars($feedback_message); ?>
                    </div>
                <?php endif; ?>

                <form action="contact" method="POST" id="contactForm">
                    <div class="mfx-form-group required">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="mfx-form-input" required>
                    </div>
                    
                    <div class="mfx-form-group"> 
                        <label for="email">Email Address (Optional)</label>
                        <input type="email" id="email" name="email" class="mfx-form-input">
                    </div>
                    
                    <div class="mfx-form-group">
                        <label for="phone">Phone Number (Optional)</label>
                        <input type="tel" id="phone" name="phone" class="mfx-form-input">
                    </div>
                    
                    <div class="mfx-form-group required">
                        <label for="subject">Inquiry Type</label>
                        <select id="subject" name="subject" class="mfx-form-select" required>
                            <option value="" disabled selected>-- Select a subject --</option>
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Product Question">Product Question</option>
                            <option value="Business Partnership">Business Partnership</option>
                            <option value="Organization Partnership">Organization Partnership</option>
                            <option value="Support Request">Support Request</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mfx-form-group required">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" class="mfx-form-textarea" required></textarea>
                    </div>
                    
                    <button type="submit" class="mfx-btn-submit">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Info Column -->
            <div class="mfx-contact-info-col">
                
                <!-- Get in Touch Card -->
                <div class="mfx-contact-info-card">
                    <h3><i class="fas fa-headset"></i> Get in Touch</h3>
                    <ul>
                        <li><i class="fas fa-phone"></i><a href="tel:+254725044914">+254 725 044 914</a></li>
                        <li><i class="fas fa-phone"></i><a href="tel:+254716959975">+254 716 959 975</a></li>
                        <li><i class="fas fa-phone"></i><a href="tel:+254727437207">+254 727 437 207</a></li>
                        <li><i class="fas fa-phone"></i><a href="tel:+254705346201">+254 705 346 201</a></li>
                        <li><i class="fas fa-envelope"></i><a href="mailto:info@motorfix.co.ke">info@motorfix.co.ke</a></li>
                    </ul>
                </div>
                
                <!-- Main Branch Card -->
                <div class="mfx-contact-info-card">
                    <h3><i class="fas fa-map-marker-alt"></i> Our Main Branch</h3>
                    <p>
                        <strong>Jekima Plaza, Jogoo Road</strong><br>
                        Nairobi, Kenya
                    </p>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=-1.2944312,36.8547174" target="_blank" rel="noopener" class="mfx-get-directions-btn">
                        <i class="fas fa-directions"></i> Get Directions
                    </a>
                </div>

                <!-- Other Branches Card -->
                <div class="mfx-contact-info-card">
                    <h3><i class="fas fa-store-alt"></i> Other Branches</h3>
                    <ul>
                        <li><i class="fas fa-map-pin"></i><p><strong>Mlolongo:</strong> Next to Petrol Station</p></li>
                        <li><i class="fas fa-map-pin"></i><p><strong>Nakuru:</strong> Victor Plaza, Biashara Street</p></li>
                        <li><i class="fas fa-map-pin"></i><p><strong>Kitale:</strong> Kipso House</p></li>
                        <li><i class="fas fa-map-pin"></i><p><strong>Eldoret:</strong> City Plaza, Nandi Road</p></li>
                    </ul>
                </div>
                
            </div>
        </div>

        <!-- Google Map Section -->
        <div class="mfx-contact-map-wrapper">
            <h2>Our Location</h2>
            <div class="mfx-contact-map-iframe">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3111.056219553596!2d36.854693999999995!3d-1.2945560000000003!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f110050a30909%3A0xa07f0a96f7821dc8!2sMOTORFIX%20INJECTION%20SERVICES%20Main%20Branch!5e1!3m2!1sen!2ske!4v1761846859906!5m2!1sen!2ske" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

    </div>
</div>

<?php require_once 'foot.php'; ?>


