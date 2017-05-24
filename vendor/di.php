<?php
/**
 * Created by PhpStorm.
 * User: skeblow
 * Date: 24.5.17
 * Time: 20:02
 */

use Slim\Container;
$container = $app->getContainer();
$container['db'] = function(Container $c) {
    $settings = $c->get('settings');

    $dsn = "mysql:host={$settings['host']};dbname={$settings['dbname']};charset=utf8";
    $usr = $settings['user'];
    $pwd =  $settings['password'];

    $slimpdo = new Slim\PDO\Database($dsn, $usr, $pwd);

    return $slimpdo;
};