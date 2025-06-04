<?php
include_once 'config.php';

/**
 * Check if a username is available in the database.
 *
 * @param mysqli $conn Database connection.
 * @param string $username The username to check.
 * @return string A message indicating whether the username is taken or available.
 */
function checkUsernameAvailability($conn, $username) {
    $count = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0 ? "Username is already taken." : "Username is available.";
}

/**
 * Check if an email is available in the database.
 *
 * @param mysqli $conn Database connection.
 * @param string $email The email to check.
 * @return string A message indicating whether the email is taken or available.
 */
function checkEmailAvailability($conn, $email) {
    $count = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0 ? "Email is already taken." : "Email is available.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['email'])) {
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            echo json_encode(["status" => "error", "message" => "Email is required."]);
            exit;
        }

        $message = checkEmailAvailability($conn, $email);
        echo json_encode(["status" => $message === "Email is already taken." ? "taken" : "available", "message" => $message]);
    }

    if (isset($_POST['username'])) {
        $username = trim($_POST['username'] ?? '');

        if (empty($username)) {
            echo json_encode(["status" => "error", "message" => "Username is required."]);
            exit;
        }

        $message = checkUsernameAvailability($conn, $username);
        echo json_encode(["status" => $message === "Username is already taken." ? "taken" : "available", "message" => $message]);
    }
}

?>
