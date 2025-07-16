<?php
require_once __DIR__ . '/../models/UsersModel.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../config/db.php';

function handleUpdateProfile() {
    global $pdo;

    $data = sanitizeInput(getRequestBody());

    $requiredFields = ['user_id', 'name', 'email'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            respond(400, "Missing required field: $field");
            return;
        }
    }

    $user_id = $data['user_id'];
    if (!is_numeric($user_id)) {
        respond(400, 'Invalid user ID');
        return;
    }

    $user = getUserById((int)$user_id);
    if (!$user) {
        respond(404, 'User not found');
        return;
    }

    $name  = trim($data['name']);
    $email = filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL);

    if (!$email) {
        respond(422, 'Invalid email');
        return;
    }

    if (emailExists($email, $user_id)) {
        respond(409, 'Email already in use');
        return;
    }

    $fields = [
        'name'  => $name,
        'email' => $email
    ];

    // Password change section
    $changingPassword = isset($data['current_password'], $data['new_password'], $data['confirm_new_password']);

    if ($changingPassword) {
        $currentPass = trim($data['current_password']);
        $newPass     = trim($data['new_password']);
        $confirmNew  = trim($data['confirm_new_password']);

        if (strlen($newPass) < 8) {
            respond(422, 'New password must be at least 8 characters');
            return;
        }

        if ($newPass !== $confirmNew) {
            respond(422, 'New password and confirmation do not match');
            return;
        }

        $stmt = $pdo->prepare("SELECT hashed_password FROM users WHERE user_id = :id");
        $stmt->execute([':id' => $user_id]);
        $currentHashed = $stmt->fetchColumn();

        if (!password_verify($currentPass, $currentHashed)) {
            respond(401, 'Current password is incorrect');
            return;
        }

        if (password_verify($newPass, $currentHashed)) {
            respond(422, 'New password must be different from current password');
            return;
        }

        $fields['hashed_password'] = password_hash($newPass, PASSWORD_DEFAULT);
    }

    if (!updateUser((int)$user_id, $fields)) {
        respond(500, 'Update failed');
        return;
    }

    $updated = getUserById((int)$user_id);
    respond(200, 'Profile updated', $updated);
}