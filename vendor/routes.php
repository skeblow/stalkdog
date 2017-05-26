<?php
/**
 * Created by PhpStorm.
 * User: skeblow
 * Date: 24.5.17
 * Time: 20:01
 */
$app->group('/v1', function () use ($app) {

    $app->group('/dogs', function() use ($app) {
        $app->get('[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
            $return = [];
            /** @var Slim\PDO\Database $db */
            $db = $this->db;
            $whoof = !empty($request->getQueryParam('whoof'));

            $stmt = $db->select(['osoby.*', 'MIN(Odchod) = \'0000-00-00 00:00:00\' as online'])
                ->from('osoby')
                ->join('Dochazka','Dochazka.OSID', '=', 'osoby.ID')
                ->groupBy('osoby.ID')
                ->execute()
            ;

            $now = (new DateTime())->format('Y-m-d H:i:s');

            while ($user = $stmt->fetchObject()) {

                $stmt2 = $db->select(['OSID'])
                    ->count('*', 'c')
                    ->from('Dochazka')
                    ->where('Prichod', '>', $now)
                    ->where('OSID','=', $user->ID)
                    ->execute()
                ;

                $obj = $stmt2->fetchObject();

                if ($obj->c) {
                    $user->status = 3;
                } else {
                    $user->status = $user->online + 1;
                }

                if ($whoof) {
                    if (in_array($user->ID, [15, 16, 17, 18, 22])) {
                        $return[] = $user;
                    }
                } else {
                    $return[] = $user;
                }
            }

            return $response->withJson($return);
        });
    });

    $app->group('/hounds', function() use ($app) {
        $app->post('/unleash', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
            $body = $request->getParsedBody();

            if (!is_array($body['dogs'])) {
                return $response->withStatus(400)
                    ->withJson(['error' => 'Body has no dogs.']);
            }

            /** @var Slim\PDO\Database $db */
            $db = $this->db;

            $now = (new DateTime())->format('Y-m-d H:i:s');

            foreach ($body['dogs'] as $dog) {
                $db->update(['Odchod' => $now])
                    ->table('Dochazka')
                    ->where('Odchod', '=' , '\'0000-00-00 00:00:00\'')
                    ->where('OSID', '=', $dog)
                    ->execute();
            }

            return $response->withStatus(204);
        });

        $app->post('/cage', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {

            $body = $request->getParsedBody();

            if (!is_array($body['dogs'])) {
                return $response->withStatus(400)
                    ->withJson(['error' => 'Body has no dogs.']);
            }

            /** @var Slim\PDO\Database $db */
            $db = $this->db;

            $now = (new DateTime())->format('Y-m-d H:i:s');

            try {
                $db->beginTransaction();

                foreach ($body['dogs'] as $dog) {
                    $stmt = $db->select()
                        ->count('*', 'c')
                        ->from('Dochazka')
                        ->where('Odchod', '=', '0000-00-00 00:00:00')
                        ->where('OSID', '=', $dog)
                        ->execute()
                    ;

                    $obj = $stmt->fetchObject();

                    if ($obj->c) {
                        throw new Exception();
                    }

                    $db->insert([
                        'OSID' => $dog,
                        'Prichod' => $now,
                        'Odchod' => '\'0000-00-00 00:00:00\'',
                    ])
                        ->into('Dochazka')
                        ->execute();
                }
                $db->commit();

                return $response->withStatus(204);
            } catch( Exception $ex ) {
                $db->rollBack();
                return $response->withStatus(400)
                    ->withJson(['error' => 'Some dog is in.']);
            }
        });

        $app->post('/natankuj', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
            $body = $request->getParsedBody();

            if (!is_array($body['dogs'])) {
                return $response->withStatus(400)
                    ->withJson(['error' => 'Body has no dogs.']);
            }

            /** @var Slim\PDO\Database $db */
            $db = $this->db;

            $now = (new DateTime())->format('Y-m-d H:i:s');
            $after = ((new DateTime())->add(new DateInterval('PT30M')))->format('Y-m-d H:i:s');

            try {

                foreach ($body['dogs'] as $dog) {

                    $stmt = $db->select()
                        ->count('*', 'c')
                        ->from('Dochazka')
                        ->where('Odchod', '=', '0000-00-00 00:00:00')
                        ->where('OSID', '=', $dog)
                        ->execute()
                    ;

                    $obj = $stmt->fetchObject();

                    if (!$obj->c) {
                        throw new Exception();
                    }


                    $db->update(['Odchod' => $now])
                        ->table('Dochazka')
                        ->where('Odchod', '=' , '\'0000-00-00 00:00:00\'')
                        ->where('OSID', '=', $dog)
                        ->execute();

                    $db->insert([
                        'OSID' => $dog,
                        'Prichod' => $after,
                        'Odchod' => '\'0000-00-00 00:00:00\'',
                    ])
                        ->into('Dochazka')
                        ->execute();

                }


                return $response->withStatus(204);
            } catch( Exception $ex ) {
                return $response->withStatus(400)
                    ->withJson(['error' => 'Some dog is in.']);
            }

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