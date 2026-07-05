<?php
    $db_host   = getenv('DB_HOST')     ?: '127.0.0.1';
    $db_port   = getenv('DB_PORT')     ?: '3306';
    $db_user   = getenv('DB_USERNAME') ?: getenv('DB_USER') ?: 'root';
    $db_pass   = getenv('DB_PASSWORD') ?: getenv('DB_PASS') ?: '';
    $db_name   = getenv('DB_NAME')     ?: 'siratpay';
    $db_prefix = getenv('DB_PREFIX')   ?: 'pp_';
?>