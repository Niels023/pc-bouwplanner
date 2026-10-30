<?php

use App\Http\Application;
use App\Http\Request;

$config = require __DIR__ . '/../bootstrap.php';

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

(new Application($config))->handle(Request::fromGlobals())->send();
