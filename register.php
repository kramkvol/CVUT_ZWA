<?php
include_once './parts/config.php';
include_once './functions/f_reg.php';
include_once './functions/f_validate.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_signup'])) {
    $errors = array_merge($errors, handleRegisterRequest($conn,$_POST));
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <?php include 'parts/header.php'; ?>
    <script src="parts/scripts.js"></script> 
    <div class="register">
            <label>Fields marked with <span>*</span> are required.</label>
            <label>Field username сan only contain letters and numbers.</label>
            <label>Fields max lenth - 255 chars.</label>
            <br>     
        <form action="<?php echo htmlspecialchars(string: $_SERVER['PHP_SELF']); ?>" method="post">
            <?php include 'parts/errors.php'; ?>                         
            <label for="username">Username: <span>*</span></label>
            <input type="text" 
                    id="username" 
                    name="username" 
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                    pattern="^[A-Za-z0-9]*$" 
                    required 
                    title="Field username сan only contain letters and numbers."  
            >
            <span id="username-error"></span>
            <br>
            <label for="email">Email: <span>*</span></label>
            <input type="email" 
                    id="email" 
                    name="email" 
                    required 
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
            >
            <span id="email-error"></span>
            <br>
            <label for="password">Password: <span>*</span></label>
            <input type="password" 
                id="password" 
                name="password" 
                required 
            >
            <span id="password-error"></span>
            <br>
            <label for="cpassword">Confirm Password: <span>*</span></label>
            <input type="password" 
                id="cpassword" 
                name="cpassword" 
                required 
            >
            <span id="cpassword-error"></span>
            <br>
            <input type="submit" id="do_signup" name="do_signup" value="Register">
        </form>
    </div>
    <?php include 'parts/footer.php'; ?>
    </body>
</html>
