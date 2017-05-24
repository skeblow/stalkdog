<?php
/**
 * Created by PhpStorm.
 * User: skeblow
 * Date: 24.5.17
 * Time: 20:10
 */

$cors = function ($request, $response, $next) {
    $response = $next($request, $response, $next);

    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
};