<?php

namespace  App;

//ini_set('display_errors', true);

require_once 'app/getFulfillableOrders.php';

$class = new getFulfillableOrders($argc, $argv);
$class->displayOrdersOnConsole();
