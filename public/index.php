<?php

use App\Http\Application;
use App\Http\Request;

$config = require __DIR__ . '/../bootstrap.php';

(new Application($config))->handle(Request::fromGlobals())->send();
