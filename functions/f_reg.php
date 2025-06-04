<?php
include_once './functions/f_validate.php';
include_once './functions/f_profile.php';
/*
 * Register a new user in the database.
 *
 * @param mysqli $conn The database connection object.
 * @param string $username The username to register.
 * @param string $email The email address to register.
 * @param string $password The plaintext password to be hashed and stored.
 * @return bool True if the user was registered successfully, false otherwise.
 */
function registerUser(mysqli $conn, string $username, string $email, string $password): bool {
    if (empty($username) || empty($email) || empty($password)) { return false;}
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO Users (username, email, upassword) VALUES (?, ?, ?)");
    if (!$stmt) { return false; }
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    $result = $stmt->execute();

    if (!$result) {
        $stmt->close();
        return false; 
    }

    $stmt->close();
    return true; 
}

/**
 * Handle the registration request by validating user input and registering the user if valid.
 *
 * @param mysqli $conn The database connection object.
 * @param array $postData The POST data from the registration form.
 * @return array An array of error messages. Empty if the registration is successful.
 */
function handleRegisterRequest(mysqli $conn, array $postData): array {
    $errors = [];
    try{
        $errors = array_merge($errors, validateUsernameExists($conn, trim($postData['username'])));
        $errors = array_merge($errors, validateEmailExists($conn, trim($postData['email'])));
        $errors = array_merge($errors, validatePasswordConfirm(trim($postData['password']),trim($postData['cpassword'])));

        if (empty($errors)) {
            if (registerUser($conn, $_POST['username'], $_POST['email'], $_POST['password'])) {
                header('Location: login.php');
                exit();
            } else {
                $errors[] = "Error. Please try again.";
            }
        }
    }
    catch (Exception $e) {
        $errors[] = "An error occurred. Please try again.";
    }
    return $errors;
}
?>
