<?php
namespace Core;

class Controller
{
    // Render a view with optional data, and return the content
    protected function view($view, $data = [])
    {
        extract($data);

        // Capture view content
        ob_start();
        include __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();

        // Pass content to layout
        // ob_start();
        include __DIR__ . "/../Views/layout.php";
        // $html = ob_get_clean();

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

    protected function normalizeInput($input)
    {
        return trim($input ?? '');
    }
}