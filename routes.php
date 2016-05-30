<?php

$r->addRoute('GET', '/', 'scoreboard');
$r->addRoute('GET', '/check/{matchId}', 'check');
$r->addRoute('POST', '/start', 'start');
$r->addRoute('POST', '/events/{type}/{side}', 'event');


function scoreboard($container, $args)
{
    $results = $container->get('db')->query('SELECT * FROM users');

    $data['users'] = [];

    while ($row = $results->fetchArray()) {
        $data['users'][] = $row;
    }

    echo $container->get('twig')->render('scoreboard.html.twig', $data);
}

function events($container, $args)
{
    $post = json_decode(file_get_contents('php://input'));

    $tableName = $db->escapeString($post['table']);

    $db = $container->get('db');
    $type = $db->escapeString($args['type']);
    $side = $db->escapeString($args['side']);

    $tableId = $db->querySingle("SELECT id FROM fbtable where name = {$tableName}");
    $matchId = $db->querySingle("SELECT id FROM match WHERE table_id = {$tableId} ORDER BY id DESC");

    if ($type == 'goals') {
        $db->query("INSERT INTO goal (side, match_id) VALUES ('{$matchId}', '{$playerId}', 'home')");
    } else if ($type == 'undo') {

    }
}

function check($container, $args)
{
    $db = $container->get('db');

    $matchId = $db->escapeString($args['matchId']);

    $results = $db->query("SELECT * FROM goal where match_id = {$matchId}");

    $data['goal'] = [];

    while ($row = $results->fetchArray()) {
        $data['goal'][] = $row;
    }

    jsonResponse($data);
}

function start($container, $args)
{
    $post = json_decode(file_get_contents('php://input'));

    $db = $container->get('db');

    $tableName = $db->escapeString($post->tableName);
    $tableId = $db->querySingle("SELECT id FROM fbtable where name = '{$tableName}'");

    $db->query("INSERT INTO `match` (table_id) VALUES ('{$tableId}')");
    $matchId = $db->lastInsertRowID();

    foreach ($post->home as $playerId) {
        $playerId = $db->escapeString($playerId);
        $db->query("INSERT INTO match_player (match_id, player_id, side) VALUES ('{$matchId}', '{$playerId}', 'home')");
    }

    foreach ($post->visitors as $playerId) {
        $playerId = $db->escapeString($playerId);
        $db->query("INSERT INTO match_player (match_id, player_id, side) VALUES ('{$matchId}', '{$playerId}', 'visitors')");
    }


    jsonResponse(['id' => $matchId]);
}


function jsonResponse($data = [])
{
    header("Content-type: application/json");
    echo json_encode($data);
}
