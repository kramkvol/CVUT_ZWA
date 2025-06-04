<?php
/**
 * Database utility functions for checking the existence of records in the database.
 */

/**
 * Retrieve detailed information about a specific book.
 *
 * This function fetches details about a book, including its name, description, cover image, 
 * and a list of authors associated with the book.
 *
 * @param mysqli $conn The database connection object.
 * @param int $bookid The ID of the book to retrieve details for.
 * @return array|null An associative array containing the book details, or null if not found. Includes:
 *                    - bookID: The ID of the book.
 *                    - bookName: The name of the book.
 *                    - bookDesc: The description of the book.
 *                    - cover: The cover image file name.
 *                    - addedbyuserid: Id of added user.
 *                    - authors: A concatenated string of author names, separated by commas.
 */
function getBookDetails($conn, $bookid) {
    $stmt = $conn->prepare("
        SELECT 
            b.bookID,
            b.bookName, 
            b.bookDesc,
            b.cover, 
            b.addedbyuserid,
            GROUP_CONCAT(CONCAT(a.authorName, ' ', a.authorSurname) ORDER BY a.authorName ASC SEPARATOR ', ') AS authors
        FROM 
            Book b
        LEFT JOIN 
            BookAndAuthor ba ON b.bookID = ba.bookID
        LEFT JOIN 
            Author a ON ba.authorID = a.authorID
        WHERE 
            b.bookID = ?
        GROUP BY 
            b.bookID, b.bookName, b.bookDesc   
    ");
    $stmt->bind_param("i", $bookid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>
