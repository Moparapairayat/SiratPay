<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly if they exist
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Route to index.php with the page query parameter set
$_GET['page'] = ltrim($uri, '/');
require_once __DIR__ . '/index.php';
