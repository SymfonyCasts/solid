<?php

require __DIR__.'/../vendor/autoload.php';

use sasquatch\Controllers\PictureController;

$controller = new PictureController();

$uri = $_SERVER['REQUEST_URI'];

$matches = [];

preg_match('|/upload|', $uri, $matches );

if ( count($matches) > 0 ) {
    echo $controller->upload();
} else {
    echo $controller->index();
}
