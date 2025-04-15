<?php 
include('config.php');

// Initialize variables
$username = $password = "";
$errors = array();

// Process login form when submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $errors[] = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $errors[] = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($errors)){
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            
            if($stmt->execute()){
                $stmt->store_result();
                if($stmt->num_rows == 1){
                    $stmt->bind_result($id, $username, $hashed_password);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            
                            // Redirect to dashboard
                            header("location: dashboard.php");
                        } else {
                            $errors[] = "Invalid password.";
                        }
                    }
                } else {
                    $errors[] = "No account found with that username.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="register_page.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php 
        if(!empty($errors)){
            foreach($errors as $error){
                echo '<div class="error">' . $error . '</div>';
            }
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
            </div>
            <div class="form-group">
                <input type="submit" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php">Register now</a>.</p>
        </form>
    </div>
</body>
</html>