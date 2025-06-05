<?php
$page_title = "Header";
// Admin panel link - will only be visible if the user is an admin
$admin_panel_link = isset($_SESSION['account_role']) && $_SESSION['account_role'] == 'Admin' ? '<a href="admin/index.php" target="_blank">Admin</a>' : '';
// Get the current file name (eg. home.php, profile.php)
$current_file_name = basename($_SERVER['PHP_SELF']);
// Indenting the below code may cause HTML validation errors
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1">
    <title><?=$page_title?></title>
    <link href="style.css" rel="stylesheet" type="text/css">

    <!-- Default favicon (for browsers that don't support media queries in link tags) -->
    <link rel="icon" href="images/favicon_light_theme.ico" type="image/x-icon">

    <!-- Light mode favicon -->
    <link rel="icon" href="images/favicon_light_theme.ico" type="image/x-icon" media="(prefers-color-scheme: light)">

    <!-- Dark mode favicon -->
    <link rel="icon" href="images/favicon_dark_theme.ico" type="image/x-icon" media="(prefers-color-scheme: dark)">
</head>
<body>

<header class="header <?php echo ($page === 'No Header') ? 'display-none' : ''; ?>">

    <div class="wrapper">

        <h1>User Login and Register Script</h1>

        <!-- If you prefer to use a logo instead of text uncomment the below code and remove the above h1 tag and replace the src attribute with the path to your logo image
        <img src="https://via.placeholder.com/200x45" width="200" height="45" alt="Logo" class="logo">
        -->

        <!-- Responsive menu toggle icon -->
        <input type="checkbox" id="menu">
        <label for="menu"></label>

        <nav class="menu">
            <a href="home.php" class="' . ($current_file_name == 'home.php' ? 'active' : '') . '">Home</a>
            <a href="profile.php" class="' . ($current_file_name == 'profile.php' ? 'active' : '') . '">Profile</a>
            <?=$admin_panel_link?>
            <a href="logout.php" class="alt">
                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>
                Logout
            </a>
        </nav>

    </div>

</header>

<div class="content">