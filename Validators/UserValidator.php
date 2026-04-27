<?php

namespace Validators;

class UserValidator
{
    private $user;
    private $userModel;

    public function __construct($user, $userModel = null)
    {
        $this->user = $user;
        $this->userModel = $userModel;
    }

    public function validate()
    {
        $errors = [];

        if (empty($this->user['username'])) {
            $errors['username'] = 'Username is required';
        }

        if (empty($this->user['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($this->user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        if (empty($this->user['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (\strlen($this->user['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        if ($this->user['password'] !== $this->user['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        if ($this->userModel) {
            if ($this->userModel->usernameExists($this->user['username'])) {
                $errors['username'] = 'Username already exists';
            }

            if ($this->userModel->emailExists($this->user['email'])) {
                $errors['email'] = 'Email already exists';
            }
        }

        if (empty($this->user['role'])) {
            $errors['role'] = 'Role is required';
        }

        return $errors;
    }
}