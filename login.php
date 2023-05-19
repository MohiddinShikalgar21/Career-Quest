<?php
// Initialize the session https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location:main.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: main.php");
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="css/s.css"/>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;500;600&display=swap");
        *{
          font-family: 'Nunito', sans-serif;  
        }
        body{ 
          margin:auto; 
          margin-top: 120px;
          width:500px; 
          background: -webkit-linear-gradient(to right, #007bff,#6f42c1); 
          background: linear-gradient(to right, #008cffba, #8d42c1); 
          color:white;        
        }
        .wrapper{  
          border-style: solid;
          border-color: white;
          border-radius: 30px;
          padding: 20px;  
          background-color: rgba(0, 0, 0, 0.1);
          color:white;
	      margin: auto auto;
	      padding: 40px;
	      border-radius: 10px;
	      box-shadow: 0 0 20px #000;
	      position: absolute;
	      width: 500px;
        }
        .form-control{
            border-style:solid;
            border-color:white;
            border-radius:20px;
            margin: 10px 0px;
            background-color:whitesmoke;	
        }
        a{
            color:white;
            margin-left:10px;
        }
        .btn-primary{
            box-shadow: 0 0 3px #000;
            background-color:#007bff87;
            margin-top:10px;
        }
        .btn-primary:hover{
            background-color:#e83e8ce3;
        }
    </style>
</head>
<body>
    <!--Header-->
    <header id="header" class="transparent-nav" style="position: fixed;background-color: rgb(120, 70, 167); top: 0;">
			<div class="container">

				<div class="navbar-header">
					<!-- Logo -->
					<div class="navbar-brand">
						<a class="logo" href="main.php" style="padding-bottom: 10px;">CareerQuest</a>
					</div>
					<!-- /Logo -->

				</div>
			</div>
		</header>
		<!-- /Header -->

    <div class="wrapper" >
        <h2 style="color:white;margin-left:30%;">User Login</h2><br>
        <p style="font-size:16px;">Please fill in your User Credentials to login.</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group" style="color:white;">
                <input type="text" name="username" placeholder="Enter Your Username." style="background-color:white;" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <input type="password" name="password" placeholder="Enter Your Password." style="background-color:white;" class="form-control<?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login" style="width:70%;margin-left:15%;border:white;">
            </div><br>
            <div style="align-items:centre;justify-content:centre;">
            <p style="font-size: 16px;margin-left:15%;">Don't have an account?   <a href="register.php">Sign Up Now</a></p>
            <p style="font-size: 16px;margin-left:22%;">Forgot Password?   <a href="reset.php">Click Here</a></p>
            </div>
        </form>
    </div>
</body>
</html>

