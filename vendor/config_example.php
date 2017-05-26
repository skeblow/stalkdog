<?php
/**
 * Created by PhpStorm.
 * User: skeblow
 * Date: 24.5.17
 * Time: 20:06
 */

return [
    'settings' => [
        'displayErrorDetails'               => true, // set to false in production
        'addContentLengthHeader'            => false, // Allow the web server to send the content-length header
        'determineRouteBeforeAppMiddleware' => true, // required for middleware for options mw

        'host' => 'localhost',
        'dbname' => '****',
        'user' => '****',
        'password' => '****',
    ],
];