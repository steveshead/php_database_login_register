<?php
$page_title = "Contact Us";
$page = '';
// Include the main.php file
include 'main.php';
require_once 'header.php';

// Check if the user is logged in, if not then redirect to login page
global $pdo;
check_loggedin($pdo);
// Template code below

// Process form submission
$msg = '';
if (isset($_POST['submit'])) {
    // Validate form inputs
    if (empty($_POST['name'])) {
        $msg = '<div class="msg error">Please enter your name!</div>';
    } else if (empty($_POST['email'])) {
        $msg = '<div class="msg error">Please enter your email!</div>';
    } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $msg = '<div class="msg error">Please enter a valid email!</div>';
    } else if (empty($_POST['subject'])) {
        $msg = '<div class="msg error">Please enter a subject!</div>';
    } else if (empty($_POST['message'])) {
        $msg = '<div class="msg error">Please enter your message!</div>';
    } else {
        // All inputs are valid, process the form
        // Send the email using the send_contact_email function from main.php
        $email_sent = send_contact_email($_POST['name'], $_POST['email'], $_POST['subject'], $_POST['message']);

        if ($email_sent) {
            $msg = '<div class="msg success">Your message has been sent successfully!</div>';
            // Clear form data after successful submission
            $_POST = array();
        } else {
            $msg = '<div class="msg error">There was a problem sending your message. Please try again later.</div>';
        }
    }
}
?>

<div class="block">
    <div class="row contact">
        <div class="column">
            <div class="page-title">
                <div class="icon">
                    <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
                </div>
                <div class="wrap">
                    <h1>CONTACT US</h1>
                </div>
            </div>

            <h3>Reach out and say hello!</h3>
            <p>Please fill out the form below to get in touch with us. We'll respond to your inquiry as soon as possible.</p>

            <form class="form" method="post" action="contact.php">
                <?=$msg?>

                <label for="name" class="form-label">Name</label>
                <div class="form-group">
                    <input type="text" name="name" id="name" class="form-input" placeholder="Your name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES) : '' ?>">
                </div>

                <label for="email" class="form-label">Email</label>
                <div class="form-group">
                    <input type="email" name="email" id="email" class="form-input" placeholder="Your email address" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES) : '' ?>">
                </div>

                <label for="subject" class="form-label">Subject</label>
                <div class="form-group">
                    <input type="text" name="subject" id="subject" class="form-input" placeholder="Subject of your message" value="<?= isset($_POST['subject']) ? htmlspecialchars($_POST['subject'], ENT_QUOTES) : '' ?>">
                </div>

                <label for="message" class="form-label">Message</label>
                <div class="form-group">
                    <textarea name="message" id="message" class="form-input" placeholder="Your message" style="height: 150px; resize: vertical;"><?= isset($_POST['message']) ? htmlspecialchars($_POST['message'], ENT_QUOTES) : '' ?></textarea>
                </div>

                <div class="form-group pad-top-2">
                    <input type="submit" name="submit" class="btn blue" value="Send Message">
                </div>
            </form>
        </div>
        <div class="column">
            <img src="images/hello.png" width="40%">
            <p></p>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
