<?php
    ob_start();
    include_once './parts/config.php';    
    $bookid = $_GET['bookid'];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Book Info</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <?php include 'parts/header.php'; ?>
        <?php include 'parts/bookhead.php'; ?>
        <?php include 'parts/bookcomments.php'; ?>
        <?php include 'parts/footer.php'; ?>
    </body>
</html>
<?php ob_end_flush();?>
