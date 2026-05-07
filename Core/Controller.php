<?php
namespace Core;

class Controller
{
    // Render a view with optional data, return the rendered HTML content
    protected function view($view, $data = [])
    {
        extract($data);

        // Capture view content
        ob_start();
        include __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();

        if (isset($hideNav) && $hideNav === true) {
            return $content;
        }
        include __DIR__ . "/../Views/layout.php";

        return $content;
    }

    // Return a JSON response with success status, message, and optional data
    protected function jsonResponse($success, $message, $data = [])
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        // Merge additional data if provided for show validation errors
        if (!empty($data)) {
            $response = array_merge($response, $data);
        }

        return json_encode($response);
    }

    // Normalize a single input value, trim whitespace, return normalized value
    protected function normalizeInput($input)
    {
        return trim($input ?? '');
    }

    /**
     * Get current logged-in user ID from session
     *
     * @return int|null User ID or null if not logged in
     */
    protected function getCurrentUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }
}