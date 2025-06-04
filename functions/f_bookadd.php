<?php
/**
 * Adds a new book to the database.
 *
 * @param mysqli $conn       The database connection object.
 * @param string $bookname   The name of the book to add.
 * @param string $bookdesc   The description of the book to add.
 * @param int $userid        The ID of the user adding the book.
 * @return int|bool          The ID of the newly added book if successful, or false if the operation fails.
 */
function addBook(mysqli $conn, string $bookname, string $bookdesc, int $userid): int|bool {
    $query = "INSERT INTO Book (bookName, bookDesc, addedbyuserid) VALUES (?, ?, ?)";
    $stmt = $conn->prepare(query: $query);
    $stmt->bind_param("ssi", $bookname, $bookdesc, $userid);
    $stmt->execute();
    $stmt->close();
    return bookAndDescExists($conn, $bookname, $bookdesc);
}

/**
 * Checks if a book with the given name and description already exists in the database.
 *
 * @param mysqli $conn       The database connection object.
 * @param string $bookname   The name of the book to check.
 * @param string $bookdesc   The description of the book to check.
 * @return int|bool          The ID of the book if it exists, or false if it does not exist.
 */
function bookAndDescExists(mysqli $conn, string $bookname, string $bookdesc): int|bool {
    $query = "SELECT bookID FROM Book WHERE bookName = ? AND bookDesc = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $bookname, $bookdesc);
    $stmt->execute();

    $stmt->bind_result($bookID);
    if ($stmt->fetch()) {
        $stmt->close();
        return $bookID;
    } else {
        $stmt->close();
        return false;
    }
}

/**
 * Handles the addition of a new book based on user input.
 *
 * @param mysqli $conn       The database connection object.
 * @param array $postData    The POST data containing book details.
 * @param int $userid        The ID of the user making the request.
 * @return array             An array of error messages, or an empty array if the operation is successful.
 */
function addBookRequest(mysqli $conn, array $postData, int $userid): array{
    $errors = [];
    $bookname = trim($postData['bookname']);
    $bookdesc = trim($postData['bookdesc']);

    if(empty($bookname)){
        $errors[] = "Book name is required.";
    }

    if(strlen($bookname) > 255){
        $errors[] = "Book name max lenth is 255 chars.";
    }

    if(strlen($bookdesc) > 255){
        $errors[] = "Book descriptoin max lenth is 255 chars.";
    }

    if (bookAndDescExists($conn, $bookname, $bookdesc) != false) {
        $errors[] = "A book with that name and discription already exists. Check the library. If it doesn't exist, add a description that will distinguish this book from other books with the same name.";
    }

    if (empty($errors)) {
        $bookid = addBook($conn, $bookname, $bookdesc, $userid);
    
        if (is_int($bookid) && $bookid > 0) { 
            header("Location: bookeditpage.php?bookid=$bookid");
            exit();
        } else {
            $errors[] = "Failed to add the book.";
        }
    }
    return $errors;
}

?>
