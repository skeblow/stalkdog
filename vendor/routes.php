<?php
/**
 * Created by PhpStorm.
 * User: skeblow
 * Date: 24.5.17
 * Time: 20:01
 */
$app->group('/v1', function () use ($app) {

    $app->group('/users', function() use ($app) {
        $app->get('[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
            $return = [];
            /** @var Slim\PDO\Database $db */
            $db = $this->db;

            $stmt = $db->select(['osoby.*', 'MIN(Odchod) = \'0000-00-00 00:00:00\' as online'])
                ->from('osoby')
                ->join('Dochazka','Dochazka.OSID', '=', 'osoby.ID')
                ->groupBy('osoby.ID')
                ->execute()
            ;

            while ($user = $stmt->fetchObject()) {
                $return[] = $user;
            }

            return $response->withJson($return);
        });
    });

    $app->group('/online', function() use ($app) {
        $app->get('[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
            $return = [];
            /** @var Slim\PDO\Database $db */
            $db = $this->db;

            $stmt = $db->select(['osoby.*'])
                ->from('osoby')
                ->join('Dochazka','Dochazka.OSID', '=', 'osoby.ID')
                ->groupBy('osoby.ID')
                ->where('Odchod', '=', '0000-00-00 00:00:00')
                ->execute()
            ;


            while ($user = $stmt->fetchObject()) {
                $return[] = $user;
            }

            return $response->withJson($return);
        });
    });


    $app->options('/{params:.*}', function ($request, $response) {
        return $response;
    });
})->add($cors);