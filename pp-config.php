<?php
    $db_host   = getenv('DB_HOST')     ?: 'db.fr-pari1.bengt.wasmernet.com';
    $db_port   = getenv('DB_PORT')     ?: '10272';
    $db_user   = getenv('DB_USERNAME') ?: getenv('DB_USER') ?: 'user_2809cae2';
    $db_pass   = getenv('DB_PASSWORD') ?: getenv('DB_PASS') ?: 'pw_zkNMyT2l08HhGcJEa4faalj8rr3zTGuc';
    $db_name   = getenv('DB_NAME')     ?: 'Siratpay';
    $db_prefix = getenv('DB_PREFIX')   ?: 'pp_';
?>