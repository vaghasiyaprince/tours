<?php 
include('config.php');

// Initialize variables
$username = $email = $password = $confirm_password = "";
$errors = array();

// Process registration form when submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate username
    if(empty(trim($_POST["username"]))){
        $errors[] = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
        // Check if username already exists
        $sql = "SELECT id FROM users WHERE username = ?";
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            if($stmt->execute()){
                $stmt->store_result();
                if($stmt->num_rows == 1){
                    $errors[] = "This username is already taken.";
                }
            }
            $stmt->close();
        }
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $errors[] = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors[] = "Invalid email format.";
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $errors[] = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $errors[] = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $errors[] = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if($password != $confirm_password){
            $errors[] = "Password did not match.";
        }
    }
    
    // Check for errors before inserting in database
    if(empty($errors)){
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("sss", $param_username, $param_email, $param_password);
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            if($stmt->execute()){
                header("location: login.php");
            } else {
                echo "Something went wrong. Please try again later.";
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
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="register_page.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
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
                <label>Email</label>
                <input type="text" name="email" value="<?php echo $email; ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password">
            </div>
            <div class="form-group">
                <input type="submit" value="Register">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>