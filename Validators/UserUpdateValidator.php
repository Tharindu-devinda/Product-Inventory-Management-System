<?php

namespace Validators;

class UserUpdateValidator
{
    private $user;
    private $userModel;

    public function __construct($user, $userModel = null)
    {
        $this->user = $user;
        $this->userModel = $userModel;
    }

    // Validate user input for updates,check for empty fields,return an array of errors if any
    public function validate()
    {
        $errors = [];

        // Username validation
        if (empty($this->user['username'])) {
            $errors['username'] = 'Username is required';
        } elseif (\strlen($this->user['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        }

        // Email validation
        if (empty($this->user['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($this->user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        // Role validation
        if (empty($this->user['role'])) {
            $errors['role'] = 'Role is required';
        }

        // NO password validation for updates!

        return $errors;
    }
}