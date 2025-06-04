<?php
include_once './functions/f_bookadd.php';

/**
 * Edit the name and description of a book.
 *
 * @param mysqli $conn Database connection.
 * @param array $bookdata Data containing book name and description.
 * @param int $bookid ID of the book to edit.
 * @return array List of errors, if any.
 */
function editBookNameAndDesc(mysqli $conn, array $bookdata, int $bookid): array {
    $errors = [];
    $bookname = trim($bookdata['bookname']);
    $bookdesc = trim($bookdata['bookdesc']);

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
        $bookid = editBook($conn, $bookname, $bookdesc, $bookid);
    
        if (is_int($bookid) && $bookid > 0) { 
            header("Location: bookeditpage.php?bookid=$bookid");
            exit();
        } else {
            $errors[] = "Failed to add the book.";
        }
    }
    return $errors;
}

/**
 * Updates the details of a book in the database and checks if the new book name
 * and description exist in the database after the update.
 *
 * @param mysqli $conn The database connection object.
 * @param string $bookname The new name of the book.
 * @param string $bookdesc The new description of the book.
 * @param int $bookid The ID of the book to update.
 * 
 * @return int|bool Returns the result of the `bookAndDescExists` function, which
 *                  indicates whether the updated book name and description exist
 *                  in the database. Returns `int` if the check passes (likely the
 *                  ID of the book or a similar identifier), or `bool` if the check fails.
 */
function editBook(mysqli $conn, string $bookname, string $bookdesc, int $bookid): int|bool {
    $query = "UPDATE Book SET bookName = ?, bookDesc = ? WHERE bookID = ?";
    $stmt = $conn->prepare(query: $query);
    $stmt->bind_param("ssi", $bookname, $bookdesc, $bookid);
    $stmt->execute();
    $stmt->close();
    return bookAndDescExists($conn, $bookname, $bookdesc);
}

/**
 * Delete the book's cover and set the default cover image.
 *
 * @param mysqli $conn Database connection.
 * @param int $bookid ID of the book.
 * @return array List of errors, if any.
 */
function editCoverDeletion(mysqli $conn, int $bookid): array {
    $errors = [];
    $defaultCover = 'cover.jpg';
    $uploadDir = './images/';

    $sql = "SELECT cover FROM Book WHERE bookID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $bookid);
        $stmt->execute();
        $stmt->bind_result($currentCover);
        if ($stmt->fetch() && !empty($currentCover) && $currentCover !== $defaultCover) {
            $stmt->close();

            $fileBaseName = pathinfo($currentCover, PATHINFO_FILENAME);

            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            foreach ($allowedExtensions as $extension) {
                $filePath = $uploadDir . $fileBaseName . '.' . $extension;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        } else {
            $stmt->close();
        }
    }

    $sql = "UPDATE Book SET cover = ? WHERE bookID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('si', $defaultCover, $bookid);
        $stmt->execute();
    }

    return $errors;
}

/**
 * Upload a new cover for the book.
 *
 * @param mysqli $conn Database connection.
 * @param int $bookid ID of the book.
 * @param string|null $currentAvatar Current cover of the book.
 * @return array List of errors, if any.
 */
function editCoverUpload(mysqli $conn, int $bookid, ?string $currentAvatar = null): array {
    $errors = [];

    // Fetch the current avatar if not provided
    if ($currentAvatar === null) {
        $stmt = $conn->prepare("SELECT cover FROM Book WHERE bookID = ?");
        if ($stmt) {
            $stmt->bind_param("i", $bookid);
            $stmt->execute();
            $stmt->bind_result($currentAvatar);
            $stmt->fetch();
            $stmt->close();
        } else {
            $errors[] = "Failed to fetch current cover: " . $conn->error;
            return $errors;
        }
    }

    if (isset($_FILES['cover']) && $_FILES['cover']['error'] == UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png'];
        $fileType = mime_content_type($_FILES['cover']['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Invalid type. JPEG and PNG only are allowed.";
            return $errors;
        }

        $uploadDir = './images/';
        $fileExtension = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $fileName = 'book' . $bookid . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;

        // Delete old cover if it exists and isn't the default
        if (!empty($currentAvatar) && $currentAvatar !== 'cover.jpg') {
            $oldFilePath = $uploadDir . $currentAvatar;
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        if (move_uploaded_file($_FILES['cover']['tmp_name'], $filePath)) {
            $stmt = $conn->prepare("UPDATE Book SET cover = ? WHERE bookID = ?");
            if ($stmt) {
                $stmt->bind_param('si', $fileName, $bookid);
                if (!$stmt->execute()) {
                    $errors[] = "Failed to update the database: " . $stmt->error;
                }
            } else {
                $errors[] = "Failed to prepare the database query: " . $conn->error;
            }
        } else {
            $errors[] = "Failed to save the uploaded file.";
        }
    } else {
        $errors[] = "No file uploaded or an error occurred during the upload.";
    }

    return $errors;
}

/**
 * Handle book edit requests, including name, description, and cover.
 *
 * @param mysqli $conn Database connection.
 * @param array $bookdata Data from the request.
 * @param int $bookid ID of the book.
 * @return array List of errors, if any.
 */
function handleEditBookRequest(mysqli $conn, array $bookdata, int $bookid): array {
    $errors = [];

    if (isset($bookdata['do_editbook'])) {
        $errors = array_merge($errors, editBookNameAndDesc($conn, $bookdata, $bookid));
    }

    if (isset($bookdata['do_newcover'])) {
        $errors = array_merge($errors, editCoverUpload($conn, $bookid, $bookdata['cover']));
    }

    if (isset($bookdata['do_deletecover'])) {
        $errors = array_merge($errors, editCoverDeletion($conn, $bookid));
    }
    return $errors;
}

/**
 * Fetch the current authors of a book.
 *
 * @param mysqli $conn Database connection.
 * @param int $bookID ID of the book.
 * @return array List of authors.
 */
function getCurrentAuthors(mysqli $conn, int $bookID): array {
    $query = "
        SELECT 
            a.authorID, 
            a.authorName, 
            a.authorSurname, 
            a.authorDesc 
        FROM 
            BookAndAuthor ba
        JOIN 
            Author a ON ba.authorID = a.authorID
        WHERE 
            ba.bookID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $bookID);
    $stmt->execute();
    $result = $stmt->get_result();

    $authors = [];
    while ($row = $result->fetch_assoc()) {
        $authors[] = $row;
    }

    return $authors; 
}

/**
 * Assign an author to a book.
 *
 * @param mysqli $conn Database connection.
 * @param int $bookID ID of the book.
 * @param int $authorID ID of the author.
 * @return bool Whether the operation was successful.
 */
function assignAuthorToBook(mysqli $conn, int $bookID, int $authorID): bool {
    $query = "
        INSERT INTO BookAndAuthor (bookID, authorID) 
        VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $bookID, $authorID);

    if ($stmt->execute()) {
        return true;
    } else {
        return false; 
    }
}

/**
 * Remove an author from a book.
 *
 * @param mysqli $conn Database connection.
 * @param int $bookID ID of the book.
 * @param int $authorID ID of the author.
 * @return bool Whether the operation was successful.
 */
function removeAuthorFromBook(mysqli $conn, int $bookID, int $authorID): bool {
    $query = "
        DELETE FROM BookAndAuthor 
        WHERE bookID = ? AND authorID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $bookID, $authorID);

    if ($stmt->execute()) {
        return true;
    } else {
        return false; 
    }
}

/**
 * Handles the editing of a book's details (name, description, etc.).
 *
 * @param mysqli $conn The database connection.
 * @param array $postData The POST data from the form.
 * @param int $bookID The ID of the book to edit.
 * @return array An array of errors, if any occur.
 */
function handleEditBookPost(mysqli $conn, array $postData, int $bookID): array {
    $errors = handleEditBookRequest($conn, $postData, $bookID);
    return $errors;
}

/**
 * Searches for authors based on their surname, excluding those already linked to the book.
 *
 * @param mysqli $conn The database connection.
 * @param int $bookID The ID of the book for which authors are being searched.
 * @param string $authorSurname The surname of the author to search for.
 * @return array An array of authors matching the search criteria.
 */
function handleAuthorSearch(mysqli $conn, int $bookID, string $authorSurname): array {
    $stmt = $conn->prepare("
        SELECT 
            a.authorID, 
            a.authorName, 
            a.authorSurname, 
            a.authorDesc 
        FROM 
            Author a
        LEFT JOIN 
            BookAndAuthor ba ON a.authorID = ba.authorID AND ba.bookID = ?
        WHERE 
            a.authorSurname LIKE ? AND ba.bookID IS NULL
    ");

    $searchParam = "%" . $authorSurname . "%";
    $stmt->bind_param("is", $bookID, $searchParam); 
    $stmt->execute();
    $foundAuthors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    return $foundAuthors;
}

/**
 * Adds an author to a book.
 *
 * @param mysqli $conn The database connection.
 * @param int $bookID The ID of the book.
 * @param int $authorID The ID of the author to add.
 * @return bool True if the author was successfully added, false otherwise.
 */
function handleAddAuthor(mysqli $conn, int $bookID, int $authorID): bool {
    if (assignAuthorToBook($conn, $bookID, $authorID)) {
        return true;
    }
    return false;
}

/**
 * Removes an author from a book.
 *
 * @param mysqli $conn The database connection.
 * @param int $bookID The ID of the book.
 * @param int $authorID The ID of the author to remove.
 * @return bool True if the author was successfully removed, false otherwise.
 */
function handleRemoveAuthor(mysqli $conn, int $bookID, int $authorID): bool {
    if (removeAuthorFromBook($conn, $bookID, $authorID)) {
        return true;
    }
    return false;
}
?>
