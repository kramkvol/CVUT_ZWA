<?php
include_once './functions/f_validate.php';
/**
 * Check if the provided password matches the current password stored in the database.
 *
 * This function verifies whether the given plaintext password corresponds to the hashed
 * password stored in the database for the specified user.
 *
 * @param mysqli $conn The database connection object.
 * @param int $userId The ID of the user whose password needs to be checked.
 * @param string $newpassword The plaintext password to verify.
 *
 * @return bool True if the password matches the stored hash, false otherwise.
 */
function currentPasswordExists(mysqli $conn, int $userId, string $newpassword): bool {
    $sql = "SELECT upassword FROM Users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false; 
    }
    
    $stmt->bind_param('i', $userId); 
    $stmt->execute();
    $stmt->bind_result($hashedPassword); 

    if ($stmt->fetch()) { 
        return password_verify($newpassword, $hashedPassword); 
    }
    
    return false; 
}

/**
 * Checks if an email exists in the database.
 *
 * @param mysqli $conn Database connection object.
 * @param string $email Email to check.
 * @return bool True if the email exists, otherwise false.
 */
function emailExists(mysqli $conn, string $email): bool {
    $sql = "SELECT 1 FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

/**
 * Checks if a username exists in the database.
 *
 * @param mysqli $conn Database connection object.
 * @param string $username Username to check.
 * @return bool True if the username exists, otherwise false.
 */
function usernameExists(mysqli $conn, string $username): bool {
    $sql = "SELECT 1 FROM Users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

/**
 * Check if the user is logged in by checking the session.
 *
 * @return bool True if the user is logged in, false otherwise.
 */
function isUserLoggedIn(): bool {
    return isset($_SESSION['userid']);
}

/**
 * Retrieve the profile information of a user from the database.
 *
 * @param mysqli $conn The database connection object.
 * @param int $userid The ID of the user.
 * @return array|false An associative array containing the user's profile data, or false if not found.
 */
function getUserProfile(mysqli $conn, $userid) {
    $sql = "SELECT username, email, DATE_FORMAT(created, '%d %M %Y %H:%i') AS created, usertype, udescription, avatar FROM Users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    return false;
}

/**
 * Update the username of a user.
 *
 * @param mysqli $conn The database connection object.
 * @param int $userid The ID of the user.
 * @param string $newUsername The new username.
 * @return bool True on success, false on failure.
 */
function handleUsernameUpdate($conn, $userid, $newUsername): bool {
    $sql = "UPDATE Users SET username = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false; 
    }
    $stmt->bind_param('si', $newUsername, $userid);
    return $stmt->execute(); 
}

/**
 * Update the email address of a user.
 *
 * @param mysqli $conn The database connection object.
 * @param int $userid The ID of the user.
 * @param string $newEmail The new email address.
 * @return bool True on success, false on failure.
 */
function handleEmailUpdate(mysqli $conn, int $userid, string $newEmail): bool {
    $sql = "UPDATE Users SET email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false; 
    }
    $stmt->bind_param('si', $newEmail, $userid);
    return $stmt->execute(); 
}

/**
 * Handle the upload and update of a user's avatar.
 *
 * @param mysqli $conn The database connection object.
 * @param int $userid The ID of the user.
 * @param string $currentAvatar The current avatar filename.
 * @return bool True on success, false on failure.
 */
function handleAvatarUpload(mysqli $conn, int $userid, string $currentAvatar): bool {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
            return false; 
        }

        $uploadDir = './images/';
        $fileExtension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $fileName = 'user' . $userid . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;

        if (!empty($currentAvatar) && $currentAvatar !== 'avatar.jpg') {
            $oldFilePath = $uploadDir . $currentAvatar;
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        if (reduceFileSize($_FILES['avatar']['tmp_name'], $filePath, $_FILES['avatar']['type'], 75) && move_uploaded_file($_FILES['avatar']['tmp_name'], $filePath) ) {
            $sql = "UPDATE Users SET avatar = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('si', $fileName, $userid);
                return $stmt->execute(); 
            }
        }
    }
    return false; 
}

/**
 * Reduces the file size of an image by compressing it based on its MIME type.
 *
 * @param string $source The path to the source image file.
 * @param string $destination The path to save the compressed image.
 * @param string $mimeType The MIME type of the image (e.g., 'image/jpeg', 'image/png', 'image/gif').
 * @param int $quality Compression quality (0-100). Higher values mean better quality and larger file size.
 *                     For PNG, this is converted to a compression level (0-9).
 *
 * @return bool Returns true if the image was successfully compressed and saved, false otherwise.
 *
 * Supported MIME Types:
 * - image/jpeg: Compresses the image using JPEG quality.
 * - image/png: Compresses the image using PNG compression levels.
 * - image/gif: Saves the image without compression (GIF does not support quality adjustment).
 */
function reduceFileSize(string $source, string $destination, string $mimeType, int $quality): bool {
    switch ($mimeType) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            if ($image) {
                return imagejpeg($image, $destination, $quality);
            }
            break;

        case 'image/png':
            $image = imagecreatefrompng($source);
            if ($image) {
                $compressionLevel = min(9, round((100 - $quality) / 10)); 
                return imagepng($image, $destination, $compressionLevel);
            }
            break;

        case 'image/gif':
            $image = imagecreatefromgif($source);
            if ($image) {
                return imagegif($image, $destination);
            }
            break;

        default:
            return false;
    }

    return false;
}

/**
 * Handle the deletion of a user's avatar.
 *
 * @param mysqli $conn The database connection object.
 * @param int $userid The ID of the user.
 * @return bool True on success, false on failure.
 */
function handleAvatarDeletion(mysqli $conn, int $userid): bool {
    $defaultAvatar = 'avatar.jpg';
    $uploadDir = './images/';

    $sql = "SELECT avatar FROM Users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $stmt->bind_result($currentAvatar);
        if ($stmt->fetch() && !empty($currentAvatar) && $currentAvatar !== $defaultAvatar) {
            $stmt->close();

            $fileBaseName = pathinfo($currentAvatar, PATHINFO_FILENAME);

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
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

    $sql = "UPDATE Users SET avatar = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('si', $defaultAvatar, $userid);
        return $stmt->execute();
    }

    return false;
}

/**
 * Update the password of a user.
 *
 * @param mysqli $conn The database connection object.
 * @param int $userid The ID of the user.
 * @param string $newPassword The new plaintext password.
 * @return bool True on success, false on failure.
 */
function handlePasswordUpdate(mysqli $conn, int $userid, string $newPassword): bool {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "UPDATE Users SET upassword = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false; 
    }
    $stmt->bind_param('si', $hashedPassword, $userid);
    return $stmt->execute(); 
}

/**
 * Handle profile update requests, such as username, email, avatar, and password.
 *
 * @param mysqli $conn The database connection object.
 * @param array $postData The POST data containing the updates.
 * @param int $userid The ID of the user.
 * @param string $useravater The current avatar of the user.
 * @return array An array of error messages, empty if successful.
 */
function handleProfilePostRequest($conn, $postData, $userid, $useravater) {
    $errors = [];

    $actions = [
        'do_newusername' => [
            'validator' => function($data) use ($conn) {
                return validateUsernameExists($conn, trim($data['new-username']));
            },
            'handler' => function($data) use ($conn, $userid) {
                return handleUsernameUpdate($conn, $userid, $data['new-username']);
            }
        ],
        'do_newemail' => [
            'validator' => function($data) use ($conn) {
                return validateEmailExists($conn, trim($data['new-email']));
            },
            'handler' => function($data) use ($conn, $userid) {
                return handleEmailUpdate($conn, $userid, $data['new-email']);
            }
        ],
        'do_newavatar' => [
            'handler' => function() use ($conn, $userid, $useravater) {
                return handleAvatarUpload($conn, $userid, $useravater); 
            }
        ],
        'do_deleteavatar' => [
            'handler' => function() use ($conn, $userid) {
                return handleAvatarDeletion($conn, $userid);
            }
        ],
        'do_newpassword' => [
            'validator' => function($data) use ($conn, $userid) {
                $errors = [];
                if (!currentPasswordExists($conn, $userid, trim($data['password']))) {
                    $errors[] = 'Current password is incorrect.';
                }
                $errors = array_merge($errors, validatePasswordConfirm(trim($data['new-password']), trim($data['cpassword'])));
                return $errors;
            },
            'handler' => function($data) use ($conn, $userid) {
                return handlePasswordUpdate(
                $conn,
                 $userid,
                trim($data['new-password']));
            }
        ]

    ];

    
    foreach ($actions as $key => $action) {
        $errors = [];
        if (isset($postData[$key])) {
            try {
                if (isset($action['validator'])) {
                    $errors = array_merge($errors, $action['validator']($postData));
                }

                if (empty($errors) && $action['handler']($postData)) {
                    header('Location: profile.php');
                    exit();
                } 
            } catch (Exception $e) {
                $errors[] = "An error occurred. Please try again.";
            }
            return $errors;
        }
    }
    return $errors;
}
?>
