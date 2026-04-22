<?php
namespace Core;

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);

        // Start capturing output
        ob_start();

        // Include the view file
        include __DIR__ . '/../Views/' . $view . '.php';

        // Get the output and stop capturing
        $content = ob_get_clean();

        // Return or echo the content
        echo $content;
    }

    protected function jsonResponse($success, $message, $data = [])
    {
        header('Content-Type: application/json');
        $response = [
            'success' => $success,
            'message' => $message
        ];

        // Merge additional data if provided for show validation errors
        if (!empty($data)) {
            $response= array_merge($response, $data);
        }

        echo json_encode($response);
        return;
    }
}