<?php
// Start the session
include('config.php');

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="register_page.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>!</h1>
        <p>This is your secure dashboard.</p>
        <p>
            <a href="logout.php">Logout</a>
        </p>
    </div>
</body>
</html>