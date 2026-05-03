<?php

namespace Validators;

class UserUpdateValidator
{
    private $user;
    private $userModel;
    private $currentUser;

    public function __construct($user, $userModel = null, $currentUser = null)
    {
        $this->user = $user;
        $this->userModel = $userModel;
        $this->currentUser = $currentUser;
    }

    // Validate user input for updates, check for duplicates and empty fields, return array of errors
    public function validate()
    {
        $errors = [];

        // Username validation
        if (empty($this->user['username'])) {
            $errors['username'] = 'Username is required';
        } elseif (\strlen($this->user['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        } elseif ($this->userModel && $this->currentUser && $this->user['username'] !== $this->currentUser['username']) {
            // Check if NEW username already exists for another user (only if username was changed)
            if ($this->userModel->usernameExists($this->user['username'])) {
                $errors['username'] = 'Username already in use by another user';
            }
        }

        // Email validation
        if (empty($this->user['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($this->user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } elseif ($this->userModel && $this->currentUser && $this->user['email'] !== $this->currentUser['email']) {
            // Check if NEW email already exists for another user (only if email was changed)
            if ($this->userModel->emailExists($this->user['email'])) {
                $errors['email'] = 'Email already in use by another user';
            }
        }

        // Role validation
        if (empty($this->user['role'])) {
            $errors['role'] = 'Role is required';
        }

        return $errors;
    }
}