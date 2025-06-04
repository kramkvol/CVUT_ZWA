<?php
include_once './parts/config.php';
include_once './functions/f_bookedit.php';

$book = getBookDetails($conn, $_GET['bookid']);
$errors = [];
$foundAuthors = [];

$currentAuthors = getCurrentAuthors($conn, $book['bookID']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['do_editbook'])) {
        $errors = handleEditBookPost($conn, $_POST, $book['bookID']);
        if (empty($errors)) {
            header("Location: bookeditpage.php?bookid=" . $book['bookID']);
            exit();
        }
    }

    if (isset($_POST['do_searchauthor'])) {
        $authorSurname = $_POST['author_surname'];
        $foundAuthors = handleAuthorSearch($conn, $book['bookID'], $authorSurname);
    }

    if (isset($_POST['do_addauthor']) && isset($_POST['author_id'])) {
        $authorID = intval($_POST['author_id']);
        if (!handleAddAuthor($conn, $book['bookID'], $authorID)) {
            $errors[] = "Failed to add author to the book.";
        }
        if (empty($errors)) {
            header("Location: bookeditpage.php?bookid=" . $book['bookID']);
            exit();
        }
    }

    if (isset($_POST['do_deleteauthor']) && isset($_POST['author_id'])) {
        $authorID = intval($_POST['author_id']);
        if (!handleRemoveAuthor($conn, $book['bookID'], $authorID)) {
            $errors[] = "Failed to remove author from the book.";
        }
        if (empty($errors)) {
            header("Location: bookeditpage.php?bookid=" . $book['bookID']);
            exit();
        }
    }

    if (isset($_POST['do_newcover'])) {
        $errors = editCoverUpload($conn,  $book['bookID']);
        if (empty($errors)) {
            header("Location: bookeditpage.php?bookid=" . $book['bookID']);
            exit();
        }
    }

    if (isset($_POST['do_deletecover'])) {
        $errors = editCoverDeletion($conn, $book['bookID']);
        if (empty($errors)) {
            header("Location: bookeditpage.php?bookid=" . $book['bookID']);
            exit();
        }
    }
}

?>
<div class="bookedit">
    <label>Fields marked with <span>*</span> are required.</label>
    <label>Fields max lenth - 255 chars.</label>    
    <?php include 'parts/errors.php'; ?>  
    <form action="bookeditpage.php?bookid=<?php echo $bookid; ?>" method="post">
    <section>
            <label for="bookname">New name: <span>*</span></label>
            <input type="text" 
                id="bookname" 
                name="bookname" 
                value="<?php echo htmlspecialchars($book['bookName']); ?>" 
                required 
            >
            <label for="bookdesc">New description:</label>
            <input type="text" 
                id="bookdesc"
                name="bookdesc" 
                value="<?php echo htmlspecialchars($book['bookDesc']) ?? ''; ?>"
            >
    </section>
    <section>
        <input type="submit" name="do_editbook" value="Update Book">
    </section>
    </form>

    <form action="bookeditpage.php?bookid=<?php echo $bookid; ?>" method="post" enctype="multipart/form-data">
        <section>
            <label for="cover">New cover (JPEG, PNG): <span>*</span></label>
            <input type="file" id="cover" name="cover" accept="image/*" required>
        </section>
        <section>
            <input type="submit" id="do_newcover" name="do_newcover" value="Update cover">
        </section>
    </form>

    <form action="bookeditpage.php?bookid=<?php echo $bookid; ?>" method="post" enctype="multipart/form-data">
        <section>  
            <label>Delete cover:</label>
        </section>
        <section>
            <input type="submit" id="do_deletecover" name="do_deletecover" value="Delete Cover">
        </section>
    </form>

    <form action="bookeditpage.php?bookid=<?php echo $bookid; ?>" method="post">
    <section>  
        <label for="author_surname">Author's surname: <span>*</span></label>
        <input type="text" 
            id="author_surname" 
            name="author_surname" 
            value="<?php echo isset($_POST['author_surname']) ? ($_POST['author_surname']) : ''; ?>"  
            required
        >
    </section>
        <input type="submit" name="do_searchauthor" value="Search Author">
    </form>

    <div class="authors">
    <form class="form2" action="bookeditpage.php?bookid=<?php echo $bookid; ?>" method="post">
    <?php if (!empty($foundAuthors)): ?>
        <?php foreach ($foundAuthors as $author): ?>
            <label>
                <input type="radio" 
                    name="author_id" 
                    value="<?php echo $author['authorID']; ?>" 
                    required
                >
                <?php echo htmlspecialchars($author['authorName'] . ' ' . $author['authorSurname'] . ' (' . $author['authorDesc'] . ')'); ?>                    
            </label>
        <?php endforeach; ?>
            <input type="submit" name="do_addauthor" value="Add author">
    <?php else: ?>
        <p>No authors found.</p>
    <?php endif; ?>
    </form>

    <form class="form2" action="bookeditpage.php?bookid=<?php echo $bookid; ?>" method="post">
    <?php if (!empty($currentAuthors)): ?>
        <?php foreach ($currentAuthors as $author): ?>
            <label>
                <input type="radio" 
                    name="author_id" 
                    value="<?php echo $author['authorID']; ?>" 
                    required
                >
                <?php echo htmlspecialchars($author['authorName'] . ' ' . $author['authorSurname'] . ' (' . $author['authorDesc'] . ')'); ?>
            </label>
        <?php endforeach; ?>
        <input type="submit" name="do_deleteauthor" value="Remove Author">
    <?php else: ?>
        <p>No authors currently assigned to this book.</p>
    <?php endif; ?>
</form>
    </div>
</div>