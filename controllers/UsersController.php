<?php
require_once __DIR__ . '/../models/UsersModel.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';

function handleUpdateProfile() {
    $data = sanitizeInput(getRequestBody());

    if (empty($data['user_id']) || !is_numeric($data['user_id'])) {
        respond(400, 'Invalid or missing user ID');
        return;
    }

    $user_id = (int)$data['user_id'];
    $user = getUserById($user_id);

    if (!$user) {
        respond(404, 'User not found');
        return;
    }

    $fields = [];
    if (isset($data['name'])) {
        $fields['name'] = trim($data['name']);
    }
    if (isset($data['email'])) {
        $email = filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL);
        if (!$email) {
            respond(422, 'Invalid email');
            return;
        }
        if (emailExists($email, $user_id)) {
            respond(409, 'Email already in use');
            return;
        }
        $fields['email'] = $email;
    }

    if (isset($data['current_password'], $data['new_password'], $data['confirm_new_password'])) {
        $currentPass = trim($data['current_password']);
        $newPass = trim($data['new_password']);
        $confirmNew = trim($data['confirm_new_password']);

        $passwordValidation = validatePasswordChange($user_id, $currentPass, $newPass, $confirmNew);
        if (!$passwordValidation['success']) {
            respond(422, $passwordValidation['message']);
            return;
        }

        $fields['hashed_password'] = $passwordValidation['hashed_password'];
    }

    if (!updateUser($user_id, $fields)) {
        respond(500, 'Update failed');
        return;
    }

    $updated = getUserById($user_id);
    respond(200, 'Profile updated', $updated);
}