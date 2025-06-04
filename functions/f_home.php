<?php
/**
 * Get the total number of books in the database.
 *
 * @param mysqli $conn The database connection object.
 * @return int The total number of books. Returns 0 if no books are found.
 */
function getTotalBooks(mysqli $conn): mixed {
    $result = $conn->query("SELECT COUNT(*) AS total FROM Book");
    if (!$result) {
        die("SQL query failed: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

/**
 * Fetch a paginated list of books and their authors.
 *
 * @param mysqli $conn The database connection object.
 * @param int $offset The starting position for the records to fetch.
 * @param int $recordsPerPage The number of records to fetch per page.
 * @return array An array of books, each including book details and a concatenated list of authors.
 */
function fetchBooks(mysqli $conn, int $offset, int $recordsPerPage): array {
    $query = "
        SELECT 
            b.bookID, 
            b.bookName, 
            b.bookDesc,
            b.cover, 
            GROUP_CONCAT(CONCAT(a.authorName, ' ', a.authorSurname) ORDER BY a.authorName ASC SEPARATOR ', ') AS authors
        FROM 
            Book b
        LEFT JOIN 
            BookAndAuthor ba ON b.bookID = ba.bookID
        LEFT JOIN 
            Author a ON ba.authorID = a.authorID
        GROUP BY 
            b.bookID, b.bookName, b.bookDesc
        ORDER BY b.bookName ASC
        LIMIT ?, ? ";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("SQL prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $offset, $recordsPerPage);
    if (!$stmt->execute()) {
        die("SQL execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    $stmt->close();
    return $books;
}
?>
