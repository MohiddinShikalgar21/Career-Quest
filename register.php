<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
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
        .btn-secondary{
            box-shadow: 0 0 3px #000;
            background-color:#e0714a80;
            margin-top:10px;
        }
        .btn-secondary:hover{
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

    <div class="wrapper">
        <h3 style="color:white;margin-left:10%;">New User Registration</h3>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="Enter Your Username." style="background-color:white;" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <input type="password" name="password" placeholder="Enter Your Password." style="background-color:white;" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Confirm Your Password." style="background-color:white;" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div><br>
            <div class="form-group">
                <input type="submit" style="width:40%;margin-left:6%;" class="btn btn-primary" value="Submit">
                <input type="reset" style="width:40%;margin-left:20%;" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p style="font-size: 16px;">Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>