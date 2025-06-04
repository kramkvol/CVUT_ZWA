<?php
    include_once './functions/f_bookcomments.php';
    include_once './functions/f_pagination.php';
    include_once './parts/config.php';


    $comments = getAllComments($conn, $bookid); 
    $book = getBookDetails($conn, $bookid); 
    $curentusertype = getUserType($conn); 

    $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = commentsPostRequest($conn, $_SESSION['userid'], $_POST, $book['bookID']);
    if(empty($errors)){
        header("Location: bookpage.php?bookid=$bookid");
        exit();
    }
}
?>

<div class="book-comments">
    <?php if (isset($_SESSION['userid'])): ?>
        <?php if ($curentusertype === "admin" || $book['addedbyuserid'] === $_SESSION['userid']): ?>
            <h3><a href="bookeditpage.php?bookid=<?= $bookid ?>">Edit Book</a></h3>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['userid'])): ?>
        <div class="add-comment-form">
            <label>Fields marked with <span>*</span> are required.</label>
            <label>Fields max lenth - 255 chars.</label>    
            <?php include 'parts/errors.php'; ?>  
            <br>
            <form action="bookpage.php?bookid=<?php echo $bookid; ?>" method="post">
                <label for="comment_text">Add a comment: <span>*</span></label>
                <textarea
                    id="comment_text" 
                    name="comment_text" rows="4" 
                    required 
                    value = "<?php echo isset(($_POST['comment_text'])) ? htmlspecialchars($_POST['comment_text']) : ''; ?>"
                ></textarea>
                <br>
                <input type="submit" value="Post Comment">
            </form>
        </div>
    <?php else: ?>
        <h3>You must be logged in to add comments. <a href="login.php">Log in</a> or <a href="register.php">register</a>.</h3>
    <?php endif; ?>

    <?php if (empty($comments)): ?>
        <h3>No comments yet. Be the first to comment!</h3>
    <?php endif; ?>

    <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <img src="<?php echo $uploadDir . $comment['avatar']; ?>" alt="avatar">
                <div class="comment-body">
                    <b><?php echo htmlspecialchars($comment['username']); ?>:</b>
                    <p><?php echo htmlspecialchars($comment['commentText']); ?></p>
                    <small><?php echo htmlspecialchars($comment['createdAt']); ?></small>

                    <div class="comment-actions">
                        <?php if (isset($_SESSION['userid']) && $_SESSION['userid'] === $comment['userID']): ?>
                            <form action="bookpage.php?bookid=<?php echo $bookid; ?>" method="post">
                                <input type="hidden" name="edit_comment_id" value="<?php echo $comment['commentID']; ?>">
                                <input type="text" 
                                    name="edit_comment_text" 
                                    value="<?php 
                                        echo isset($_POST['edit_comment_id']) && $_POST['edit_comment_id'] == $comment['commentID'] 
                                        ? ($_POST['edit_comment_text']) 
                                        : htmlspecialchars($comment['commentText']); 
                                    ?>"
                                    required 
                                >
                                <input type="submit" value="Edit">
                            </form>
                        <?php endif; ?>


                        <?php if (isset($_SESSION['userid']) && ($_SESSION['userid'] === $comment['userID'] || $curentusertype === "admin")): ?>
                            <form action="bookpage.php?bookid=<?php echo $bookid; ?>" method="post">
                                <input type="hidden" name="delete_comment_id" value="<?php echo $comment['commentID']; ?>">
                                <input type="submit" value="Delete">
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
