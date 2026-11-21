
<?php

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);

        $file = __DIR__ . "/../views/" . $view . ".php";

        if (file_exists($file)) {
            require $file;
        } else {
            http_response_code(404);
            echo "View tidak ditemukan.";
        }
    }

    protected function redirect($url)
    {
        header("Location: " . $url);
        exit;
    }
    protected function validateCsrf()
    
    {
        if (
            empty($_SESSION['csrf_token']) ||
            empty($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            http_response_code(403);
            exit("403 - Token CSRF tidak valid.");
        }
    }
}
