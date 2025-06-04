<?php 
/**
 * Validate if the username is not empty.
 *
 * @param string $username The username to validate.
 * @return array An array of error messages, empty if valid.
 */
function validateUsername(string $username): array {
    $errors = [];
    if (empty($username)) {
        $errors[] = "Username is required.";
    } 
    return $errors;
}

/* Validate the username for existence, length, and uniqueness.
*
* @param mysqli $conn The database connection object.
* @param string $username The username to validate.
* @return array An array of error messages, empty if valid.
*/
function validateUsernameExists(mysqli $conn, string $username): array {
    $errors = [];
    if (empty($username)) {
        $errors[] = "Username is required.";
    } 
    else {
        if (strlen($username) < 5) {
            $errors[] = "Username cannot be shorter than 5 characters.";
        }
        if (usernameExists($conn, $username)) {
            $errors[] = "Username already exists.";
        }
    }
    return $errors;
}


/**
 * Validate the email for format and uniqueness.
 *
 * @param mysqli $conn The database connection object.
 * @param string $email The email to validate.
 * @return array An array of error messages, empty if valid.
 */
function validateEmailExists(mysqli $conn, string $email): array {
    $errors = [];
    if (empty($email)) {
        $errors[] = "Email is required.";
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (emailExists($conn, $email)) {
            $errors[] = "Email already exists.";
        }
    }
    return $errors;
}

/**
 * Validate if the password is not empty.
 *
 * @param string $password The password to validate.
 * @return array An array of error messages, empty if valid.
 */
function validatePassword(string $password): array {
    $errors = [];
    if (empty($password)) {
        $errors[] = "Password is required.";
    } 
    return $errors;
}

/**
 * Validate if the password and confirm password match.
 *
 * @param string $password The original password.
 * @param string $cpassword The confirmation password.
 * @return array An array of error messages, empty if valid.
 */
function validatePasswordConfirm(string $password, string $cpassword): array {
    $errors = [];
    if (empty($password) || empty($cpassword)) {
        $errors[] = "Password and confirm password are required.";
    } elseif ($password !== $cpassword) {
        $errors[] = "Passwords do not match.";
    }
    return $errors;
}
?>