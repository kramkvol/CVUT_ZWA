<?php
    include_once './parts/config.php';
    include_once './functions/f_bookadd.php';

    if (!isset($_SESSION['userid'])) {
        header("Location: login.php");
        exit();
    }

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_addbook'])) {
        $errors = addBookRequest($conn, $_POST, $_SESSION['userid']);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Book</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <?php include 'parts/header.php'; ?>
        <div class="bookadd">
            <label>Fields marked with <span>*</span> are required.</label>
            <label>Fields max lenth - 255 chars.</label>    
            <br>
            <?php include 'parts/errors.php'; ?>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">                        
                <label for="bookname">Book Name:<span>*</span></label>
                <input type="text" 
                    id="bookname" 
                    name="bookname" 
                    value="<?php echo isset($_POST['bookname']) ? htmlspecialchars($_POST['bookname']) : ''; ?>" 
                    required 
                >
                <span id="bookname-error"></span>
                <br>
                <label for="bookdesc">Book Description:</label>
                <input type="text" 
                    id="bookdesc" 
                    name="bookdesc" 
                    value="<?php echo isset($_POST['bookdesc']) ? htmlspecialchars($_POST['bookdesc']) : ''; ?>"
                >
                <span id="bookdesc-error"></span>
                <br>
                <input type="submit" id="do_addbook" name="do_addbook" value="Add Book">
            </form>
        </div>
        <?php include 'parts/footer.php'; ?>
        </body>
</html>
