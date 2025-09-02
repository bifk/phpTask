<?php

$route = $_SERVER['REQUEST_URI'];

switch ($route) {
    case '/':
    case '':
        require 'templates/index.php';
        break;
    case '/registration':
        require 'templates/registration.php';
        break;
    case '/auth':
        require 'templates/auth.php';
        break;
    default:
        if (file_exists('templates' . $route . '.php')) {
            require 'templates' . $route . '.php';
        } else {
            http_response_code(404);
        }
        break;
}
