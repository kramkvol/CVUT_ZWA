<?php
    include_once './parts/config.php';
    include_once  './functions/f_profile.php';
    include_once  './functions/f_database.php';

    $uploadDir = './images/';  
    
    if (!isset($_SESSION['userid'])) {
        header("Location: login.php");
        exit();
    }
    $userid = $_SESSION['userid'];

    $user = getUserProfile($conn, $userid);

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = array_merge($errors,  handleProfilePostRequest($conn, $_POST, $_SESSION['userid'], $user['avatar']));
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile</title>
        <link rel="stylesheet" href="styles.css">
        <script src="parts/scripts.js"></script> 
    </head>
<body>
    <?php include 'parts/header.php'; ?>

    <div class="profile-info">
        <section>
            <img src="<?php echo $uploadDir ?><?php echo ($user['avatar']); ?>" alt="Avatar">
        </section>
        <section>
            <p><b>Username:</b> <?php echo ($user['username']); ?></p>
            <p><b>Created:</b> <?php echo ($user['created']); ?></p>
            <p><b>Email:</b> <?php echo ($user['email']); ?></p>
            <p><b>Usertype:</b> <?php echo ($user['usertype']); ?></p>
        </section>
    </div>
    <div class="profile-forms">
        <label>Fields marked with <span>*</span> are required.</label>
        <label>Field username сan only contain letters and numbers.</label>
        <label>Fields max lenth - 255 chars.</label>    
        <?php include 'parts/errors.php'; ?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <section>
                <label for="new-username">New username: <span>*</span></label>
                <input type="text" 
                    id="new-username" 
                    name="new-username" 
                    pattern="^[A-Za-z0-9]*$" 
                    required 
                    title="Field username сan only contain letters and numbers."  
                    value="<?php echo isset($_POST['new-username']) ? htmlspecialchars($_POST['new-username']) : ''; ?>"
                >
                <span id="new-username-error"></span>
            </section>
            <section>
                <input type="submit" id="do_newusername" name="do_newusername" value="Update">
            </section>
        </form>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <section>
                <label for="new-email">New email: <span>*</span></label>
                <input type="email" 
                    id="new-email" 
                    name="new-email" 
                    value="<?php echo isset($_POST['new-email']) ? htmlspecialchars($_POST['new-email']) : ''; ?>"
                    required 
                >
                <span id="new-email-error"></span>
            </section>
            <section>
                <input type="submit" id="do_newemail" name="do_newemail" value="Update">
            </section>
        </form>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
            <section>
                <label for="avatar">New avatar (JPEG, PNG, GIF): <span>*</span></label>
                <input type="file" id="avatar" name="avatar" accept="image/*" required>
            </section>
            <section>
                <input type="submit" id="do_newavatar" name="do_newavatar" value="Upload">
            </section>
        </form>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <section>
                <label>Delete avatar:</label>
            </section>
            <section>
                <input type="submit" id="do_deleteavatar" name="do_deleteavatar" value="Delete ">
            </section>
        </form>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <section>
                <label for="password">Current password: <span>*</span></label>
                <input type="password" 
                    id="password" 
                    name="password"
                    required 
                >
            </section>
            <section>
                <label for="new-password">New: <span>*</span></label>
                <input type="password" 
                    id="new-password" 
                    name="new-password" 
                    required 
                >
            </section>
            <section>
                <label for="cpassword">Confirm: <span>*</span></label>
                <input type="password" 
                    id="cpassword" 
                    name="cpassword" 
                    required 
                >
            </section>
            <section>
                <input type="submit" id="do_newpassword" name="do_newpassword" value="Update">
            </section>
        </form>
    </div>
    <?php include 'parts/footer.php'; ?>
</body>
</html>
