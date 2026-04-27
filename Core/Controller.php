<?php
namespace Core;

class Controller
{
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