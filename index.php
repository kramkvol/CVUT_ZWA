<?php
include_once './parts/config.php';
include_once './functions/f_pagination.php';
include_once './functions/f_home.php';

$uploadDir = './images/';  

$recordsPerPage = 8; 
$paginationData = getPaginationData(
    $conn,
    'fetchBooks',     
    'getTotalBooks',  
    $recordsPerPage
);

$books = $paginationData['items'];
$totalPages = $paginationData['totalPages'];
$currentPage = $paginationData['currentPage'];
?>


<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>Book List</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <?php include 'parts/header.php'; ?>
        <div class="home">
                <?php if (!empty($books)): ?>
                    <?php foreach ($books as $book): ?>
                        <div class="book-card">
                            <img src="<?php echo $uploadDir . ($book['cover']); ?>" alt="Book Cover">
                            <div class="book-info">
                                <a 
                                    href ="bookpage.php?bookid=<?php echo isset($book['bookID']) ? htmlspecialchars($book['bookID']) : ''?>"
                                >
                                    <?php echo isset($book['bookName']) ? htmlspecialchars($book['bookName']) : ''; ?>
                                </a>
                                <p><b>Autors: </b><?php echo isset($book['authors']) ? htmlspecialchars($book['authors']) : ' Authors not assigned'; ?></p>
                                <p><b>Description: </b><?php echo isset($book['bookDesc']) ? htmlspecialchars($book['bookDesc']) : ' BookDesc not assigned'; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No books available.</p>
                <?php endif; ?>
        </div>
        <?php include 'parts/pagination.php'; ?>
        <?php include 'parts/footer.php'; ?>
    </body>
</html>
