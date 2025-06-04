<?php
    include_once './parts/config.php';
    include_once './functions/f_database.php';
    $uploadDir = './images/';
    $book = getBookDetails($conn, $bookid); 

?>

<div class="book-container">
    <section>
        <img src="<?php echo $uploadDir . ($book['cover']); ?>" alt="Book Cover">
    </section>
    <section>
        <p><b>Name:</b>  <?php echo isset($book['bookName']) ? htmlspecialchars($book['bookName']) : ''; ?></p>
        <p><b>Autors: </b><?php echo isset($book['authors']) ? htmlspecialchars($book['authors']) : ' Authors not assigned'; ?></p>
        <p><b>Description: </b><?php echo isset($book['bookDesc']) ? htmlspecialchars($book['bookDesc']) : ' BookDesc not assigned'; ?></p>
    </section>
</div>
