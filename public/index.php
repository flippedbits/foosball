<?php

require_once '../vendor/autoload.php';
require_once '../Container.php';

$config = include_once '../config.php';

$db = new SQLite3($config['db']['filename']);

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('../view/');
$twig = new Twig_Environment($loader, array(
    'debug' => true,
    //'cache' => '../cache/',
));
$twig->addExtension(new Twig_Extension_Debug());
$twig->addGlobal('global', $config['global']);

$container = new Container($config);
$container->add('twig', $twig);
$container->add('db', $db);

$sql = file_get_contents('../dbsetup.sql');
$db->exec($sql);

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    require_once '../routes.php';
});

$container->run($dispatcher);
