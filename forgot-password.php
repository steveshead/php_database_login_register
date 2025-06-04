<?php
$page_title = "Forgot Password";
$page = "No Header";
include 'main.php';
require_once 'header.php';
// Error message variable
$error_msg = '';
// Success message variable
$success_msg = '';
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if (isset($_POST['email'])) {
    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.
    $stmt = $pdo->prepare('SELECT username, email FROM accounts WHERE email = ?');
    $stmt->execute([ $_POST['email'] ]);
	$account = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the acc exists...
    if ($account) {
        // Email exist, so generate a strong unique reset code
    	$unique_reset_code = hash('sha256', uniqid() . $account['email'] . secret_key);
		// Update the reset code in the database
        $stmt = $pdo->prepare('UPDATE accounts SET reset_code = ? WHERE email = ?');
        $stmt->execute([ $unique_reset_code, $account['email'] ]);
		// Send email with reset link
		send_password_reset_email($account['email'], $account['username'], $unique_reset_code);
		// Output success message
        $success_msg = 'Reset password link has been sent to your email!';
    } else {
		// Output error message
        $error_msg = 'We do not have an account with that email!';
    }
}
?>

		<div class="login">

			<h1>Forgot Password</h1>

			<form action="forgot-password.php" method="post" class="form">

				<label class="form-label" for="email">Email</label>
				<div class="form-group mar-bot-5">
					<svg class="form-icon-left" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
					<input class="form-input" type="email" name="email" placeholder="Email" id="email" required>
				</div>
				
				<?php if ($error_msg): ?>
				<div class="msg error">
					<?=$error_msg?>
				</div>
				<?php elseif ($success_msg): ?>
				<div class="msg success">
					<?=$success_msg?>
				</div>
				<?php endif; ?>

				<button class="btn blue" type="submit">Submit</button>

			</form>

<?= require 'footer.php'; ?>