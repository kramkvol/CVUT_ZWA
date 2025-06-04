<?php
/**
 * Get the user type of the currently logged-in user.
 *
 * This function retrieves the user type from the database based on the user ID stored in the session.
 *
 * @param mysqli $conn The database connection object.
 * @return string|null The user type as a string, or null if the user is not logged in or not found.
 *
 */
function getUserType($conn) {
    if (!isset($_SESSION['userid'])) {
        return null;
    }

    $query = "SELECT usertype FROM Users WHERE id = ?";
    $stmt = $conn->prepare($query);

    $stmt->bind_param("i", $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user['usertype'] ?? null;
}

/**
 * Retrieve all comments for a specific book.
 *
 * This function fetches all comments associated with a specific book, including the comment's text,
 * the user who posted it, their username, and avatar.
 *
 * @param mysqli $conn The database connection object.
 * @param int $bookid The ID of the book for which to retrieve comments.
 * @return array An array of comments. Each comment includes:
 *               - commentID: The comment's ID.
 *               - userID: The ID of the user who posted the comment.
 *               - username: The username of the commenter.
 *               - avatar: The avatar of the commenter.
 *               - commentText: The text of the comment.
 *               - createdAt: The timestamp of when the comment was created.
 */
function getAllComments($conn, $bookid) {
    $query = "
        SELECT 
            c.commentID, 
            c.userID, 
            u.username, 
            u.avatar, 
            c.commentText, 
            DATE_FORMAT(c.createdAt, '%d %M %Y %H:%i') AS createdAt
        FROM 
            Comments c
        JOIN 
            Users u ON c.userID = u.id
        WHERE c.bookID = ?
        ORDER BY  
            c.createdAt DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $bookid);
    $stmt->execute();
    $result = $stmt->get_result();

    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }

    $stmt->close();
    return $comments;
}


/**
 * Handles comment-related actions (add, delete, edit) based on the POST request data.
 *
 * @param mysqli $conn       The database connection object.
 * @param int $userid        The ID of the user performing the action.
 * @param array $postData    The POST data containing comment-related inputs.
 * @param int $bookid        The ID of the book associated with the comments.
 * @return array             An array of error messages, or an empty array if successful.
 */
function commentsPostRequest(mysqli $conn, int $userid, array $postData, int $bookid,): array {
    $errors = [];
    if (isset($postData['comment_text'])) {
        $errors = addComment($conn, $userid, $postData['comment_text'], $bookid);
    }
    
    if (isset($postData['delete_comment_id'])) {
        $errors = deleteComment($conn,  $postData['delete_comment_id'], $bookid);
    }
    
    if (isset($postData['edit_comment_id'], $postData['edit_comment_text'])) {
        $errors = editComment($conn, $postData['edit_comment_id'], $postData['edit_comment_text'], $bookid);
    }
    return $errors;
}

/**
 * Adds a new comment to the database.
 *
 * @param mysqli $conn       The database connection object.
 * @param int $userid        The ID of the user adding the comment.
 * @param string $commentText The text of the comment to add.
 * @param int $bookid        The ID of the book the comment is associated with.
 * @return array             An array of error messages, or an empty array if successful.
 */
function addComment(mysqli $conn, int $userid, string $commentText, int $bookid,): array {
    $errors = [];
    $commentText = trim($commentText);
    if(!empty($commentText)){
        if(strlen($commentText) <= 255){
            $query = "INSERT INTO Comments (bookID, userid, commentText) VALUES (?, ?, ?)";
            $stmt = $conn->prepare(query: $query);
            $stmt->bind_param("iis", $bookid, $userid,  $commentText);
            $stmt->execute();
            $stmt->close();     
        }else{
            if(strlen($commentText) > 255){
                $errors[] = "Max comment's lenth is 255 chars";
            }
        }
    }else{
        $errors[] = "Comment is requred.";
    }   
    
    return $errors;
}

/**
 * Deletes a comment from the database.
 *
 * @param mysqli $conn          The database connection object.
 * @param int $deleteCommentID  The ID of the comment to delete.
 * @param int $bookid           The ID of the book associated with the comment.
 * @return array                An array of error messages, or an empty array if successful.
 */
function deleteComment(mysqli $conn, int $deleteCommentID, int $bookid):array {
    $errors = [];
    $query = "DELETE FROM Comments WHERE commentID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $deleteCommentID);
    $stmt->execute();
    $stmt->close();
    return $errors;
}

/**
 * Edits an existing comment in the database.
 *
 * @param mysqli $conn            The database connection object.
 * @param int $editCommentID      The ID of the comment to edit.
 * @param string $editCommentText The new text for the comment.
 * @param int $bookid             The ID of the book associated with the comment.
 * @return array                  An array of error messages, or an empty array if successful.
 */
function editComment(mysqli $conn, int $editCommentID, string $editCommentText, int $bookid):array {
    $errors = [];
    $editCommentID = (int)$editCommentID;
    $editCommentText = trim($editCommentText);
    
    if(!empty($editCommentText)){
        if(strlen($editCommentText) <= 255){
            $query = "UPDATE Comments SET commentText = ? WHERE commentID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $editCommentText, $editCommentID);
            $stmt->execute();
            $stmt->close();       
        }else{
            if(strlen($editCommentText) > 255){
                $errors[] = "Max comment's lenth is 255 chars";
            }
        }        
    }else{
        $errors[] = "Editeble comment is requred.";
    }  
    
    return $errors;
}


?>