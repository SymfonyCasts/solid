<?php

require __DIR__.'/../vendor/autoload.php';

use Sasquatch\Controllers\PictureController;

$controller = new PictureController();

$uri = $_SERVER['REQUEST_URI'];

$matches = [];

preg_match('|/([^/\?]+)|', $uri, $matches);

if (count($matches)) {
    switch ($matches[1]) {
        case 'upload':
            echo $controller->upload();
            break;
        case 'show':
            echo $controller->show($_GET['file']);
            break;
    }
} else {
    echo $controller->index();
}
