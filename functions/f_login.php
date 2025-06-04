<?php
include_once './functions/f_validate.php';
/**
 * Validates if a username and password combination is correct and returns the user ID.
 *
 * @param mysqli $conn Database connection object.
 * @param string $username Username to check.
 * @param string $password Plaintext password to validate.
 * @return int|false The user ID if the combination is valid, otherwise false.
 */
function usernameAndPasswordValid(mysqli $conn, string $username, string $password) {
    $sql = "SELECT id, upassword FROM Users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($userid, $hashedPassword);

    if ($stmt->fetch()) {
        if (password_verify($password, $hashedPassword)) {
            return $userid; 
        }
    }
    return false; 
}

/**
 * Handle a login request by validating input and authenticating the user.
 *
 * @param mysqli $conn The database connection object.
 * @param array $postData The POST data from the login form.
 * @return array An array of error messages, empty if the login is successful.
 */
function handleLoginRequest(mysqli $conn, array $postData): array {
    $errors = [];
    try{
        $errors = array_merge($errors, validateUsername(trim($postData['username'])));
        $errors = array_merge($errors, validatePassword(trim($postData['password'])));

        $userid = usernameAndPasswordValid($conn, $postData['username'], $postData['password']);

        if (is_int($userid) && $userid > 0) { 
            $_SESSION['userid'] = $userid; 
            header("Location: profile.php");
            exit();
        } else {
            $errors[] = "Invalid username or password.";
        }
    } catch (Exception $e) {
        $errors[] = "An error occurred. Please try again.";
    }
    return $errors;
}

?>