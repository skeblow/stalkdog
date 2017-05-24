<?php
/**
 * Created by PhpStorm.
 * User: skeblow
 * Date: 24.5.17
 * Time: 20:06
 */
require __DIR__ . '/../vendor/autoload.php';
$settings = require 'config.php';
$app = new \Slim\App($settings);
require 'di.php';
require 'middleware.php';
require 'routes.php';
$app->run();