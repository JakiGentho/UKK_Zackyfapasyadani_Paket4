<?php
function validateRequired($value, $field_name) {
    if (empty($value)) {
        return "$field_name harus diisi";
    }
    return true;
}

function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Format email tidak valid";
    }
    return true;
}

function validateMinLength($value, $min, $field_name) {
    if (strlen($value) < $min) {
        return "$field_name minimal $min karakter";
    }
    return true;
}

function validateMaxLength($value, $max, $field_name) {
    if (strlen($value) > $max) {
        return "$field_name maksimal $max karakter";
    }
    return true;
}

function validateNumeric($value, $field_name) {
    if (!is_numeric($value)) {
        return "$field_name harus berupa angka";
    }
    return true;
}

function validatePasswordMatch($password, $confirm_password) {
    if ($password !== $confirm_password) {
        return "Password tidak cocok";
    }
    return true;
}

function validateUsername($username) {
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        return "Username hanya boleh mengandung huruf, angka, dan underscore (3-20 karakter)";
    }
    return true;
}
?>
