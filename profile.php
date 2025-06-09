<?php
$page_title = 'Profile Page';
$page = '';
include 'main.php';
require_once 'header.php';
// Check logged-in
check_loggedin($pdo);
// Error message variable
$error_msg = '';
// Success message variable
$success_msg = '';
// Retrieve additional account info from the database because we don't have them stored in sessions
$stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
$stmt->execute([ $_SESSION['account_id'] ]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);
// Handle edit profile post data
if (isset($_POST['username'], $_POST['npassword'], $_POST['cpassword'], $_POST['email'])) {
	// Make sure the submitted registration values are not empty.
	if (empty($_POST['username']) || empty($_POST['email'])) {
		$error_msg = 'The input fields must not be empty!';
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$error_msg = 'Please provide a valid email address!';
	} else if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['username'])) {
	    $error_msg = 'Username must contain only letters and numbers!';
	} else if (!empty($_POST['npassword']) && (strlen($_POST['npassword']) > 20 || strlen($_POST['npassword']) < 5)) {
		$error_msg = 'Password must be between 5 and 20 characters long!';
	} else if ($_POST['cpassword'] != $_POST['npassword']) {
		$error_msg = 'Passwords do not match!';
	}
	// No validation errors... Process update
	if (empty($error_msg)) {
		// Check if new username or email already exists in the database
		$stmt = $pdo->prepare('SELECT COUNT(*) FROM accounts WHERE (username = ? OR email = ?) AND username != ? AND email != ?');
		$stmt->execute([ $_POST['username'], $_POST['email'], $account['username'], $account['email'] ]);
		// Account exists? Output error...
		if ($stmt->fetchColumn() > 0) {
			$error_msg = 'Account already exists with that username and/or email!';
		} else {
			// No errors occurred, update the account...
			// Hash the new password if it was posted and is not blank
			$password = !empty($_POST['npassword']) ? password_hash($_POST['npassword'], PASSWORD_DEFAULT) : $account['password'];
			// If email has changed, generate a new activation code
			$activation_code = account_activation && $account['email'] != $_POST['email'] ? hash('sha256', uniqid() . $_POST['email'] . secret_key) : $account['activation_code'];
			// Update the account
			$stmt = $pdo->prepare('UPDATE accounts SET first_name = ?, last_name = ?, username = ?, password = ?, email = ?, activation_code = ? WHERE id = ?');
			$stmt->execute([ $_POST['first_name'], $_POST['last_name'], $_POST['username'], $password, $_POST['email'], $activation_code, $_SESSION['account_id'] ]);
			// Update the session variables
			$_SESSION['account_name'] = $_POST['username'];
			// If email has changed, logout the user and send a new activation email
			if (account_activation && $account['email'] != $_POST['email']) {
				// Account activation required, send the user the activation email with the "send_activation_email" function from the "main.php" file
				send_activation_email($_POST['email'], $activation_code);
				// Logout the user
				unset($_SESSION['account_loggedin']);
				// Output success message
				$success_msg = 'You have changed your email address! You need to re-activate your account!';
			} else {
				// Profile updated successfully, redirect the user back to the profile page
				header('Location: profile.php');
				exit;
			}
		}
	}
}
?>


<?php if (!isset($_GET['action'])): ?>

<!-- View Profile Page -->

<div class="block">

	<!-- Tip: it's good practice to escape user variables using htmlspecialchars() to prevent XSS attacks. -->

    <div class="page-title">
        <div class="icon">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
        </div>
        <div class="wrap">
            <h1>PROFILE</h1>
        </div>
    </div>

    <div class="profile-detail">
        <strong>First Name</strong>
        <?= !empty($account['first_name']) ? htmlspecialchars($account['first_name'], ENT_QUOTES) : '<div class="padding"></div>' ?>
    </div>

    <div class="profile-detail">
        <strong>Last Name</strong>
        <?= !empty($account['last_name']) ? htmlspecialchars($account['last_name'], ENT_QUOTES) : '<div class="padding"></div>' ?>
    </div>

    <div class="profile-detail">
		<strong>Username</strong>
		<?=htmlspecialchars($account['username'], ENT_QUOTES)?>
	</div>

	<div class="profile-detail">
		<strong>Email</strong>
		<?=htmlspecialchars($account['email'], ENT_QUOTES)?>
	</div>

	<div class="profile-detail">
		<strong>Role</strong>
		<?=$account['role']?>
	</div>

	<div class="profile-detail">
		<strong>Registered</strong>
		<?=date('Y-m-d H:ia', strtotime($account['registered']))?>
	</div>

	<a class="btn blue mar-top-5 mar-bot-2" href="?action=edit">Edit Profile</a>

</div>

<?php elseif ($_GET['action'] == 'edit'): ?>

<!-- Edit Profile Page -->

<div class="page-title">
	<div class="icon">
		<svg width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H322.8c-3.1-8.8-3.7-18.4-1.4-27.8l15-60.1c2.8-11.3 8.6-21.5 16.8-29.7l40.3-40.3c-32.1-31-75.7-50.1-123.9-50.1H178.3zm435.5-68.3c-15.6-15.6-40.9-15.6-56.6 0l-29.4 29.4 71 71 29.4-29.4c15.6-15.6 15.6-40.9 0-56.6l-14.4-14.4zM375.9 417c-4.1 4.1-7 9.2-8.4 14.9l-15 60.1c-1.4 5.5 .2 11.2 4.2 15.2s9.7 5.6 15.2 4.2l60.1-15c5.6-1.4 10.8-4.3 14.9-8.4L576.1 358.7l-71-71L375.9 417z"/></svg>
	</div>	
	<div class="wrap">
		<h2>Edit Profile</h2>
		<p>View and edit your profile details below.</p>
	</div>
</div>

<div class="block">

	<form action="profile.php?action=edit" method="post" class="form form-small">

        <label class="form-label" for="first_name" style="padding-top:5px">First Name</label>
        <div class="form-group">
            <svg class="form-icon-left" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
            <input class="form-input" type="text" name="first_name" placeholder="First Name" id="first_name" value="<?=htmlspecialchars($account['first_name'], ENT_QUOTES)?>">
        </div>

        <label class="form-label" for="last_name" style="padding-top:5px">Last Name</label>
        <div class="form-group">
            <svg class="form-icon-left" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
            <input class="form-input" type="text" name="last_name" placeholder="Last Name" id="last_name" value="<?=htmlspecialchars($account['last_name'], ENT_QUOTES)?>">
        </div>

		<label class="form-label" for="username" style="padding-top:5px">Username</label>
		<div class="form-group">
			<svg class="form-icon-left" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
			<input class="form-input" type="text" name="username" placeholder="Username" id="username" value="<?=htmlspecialchars($account['username'], ENT_QUOTES)?>" required>
		</div>

		<label class="form-label" for="npassword">New Password</label>
		<div class="form-group">
			<svg class="form-icon-left" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"/></svg>
			<input class="form-input" type="password" name="npassword" placeholder="New Password" id="npassword" autocomplete="new-password">
		</div>

		<label class="form-label" for="cpassword">Confirm Password</label>
		<div class="form-group">
			<svg class="form-icon-left" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"/></svg>
			<input class="form-input" type="password" name="cpassword" placeholder="Confirm Password" id="cpassword" autocomplete="new-password">
		</div>

		<label class="form-label" for="email">Email</label>
		<div class="form-group mar-bot-5">
			<svg class="form-icon-left" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
			<input class="form-input" type="email" name="email" placeholder="Email" id="email" value="<?=htmlspecialchars($account['email'], ENT_QUOTES)?>" required>
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

		<div class="mar-bot-2">
			<button class="btn blue mar-top-1 mar-right-1" type="submit">Save Details</button>
			<a href="profile.php" class="btn alt mar-top-1">Back To Profile</a>
		</div>

	</form>

</div>

<?php endif; ?>

<?= require 'footer.php'; ?>
