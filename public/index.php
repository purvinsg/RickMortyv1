<?php

use App\Core\Renderer;
use App\Core\Router;

require_once '../vendor/autoload.php';
require_once '../app/Core/Router.php';
require_once '../app/Core/Renderer.php';
$response = Router::route();

$renderer = new Renderer();

echo $renderer->render($response);


///
///
///
/// uztaisit single character vieW!!!
/// UN MEKLETĀJU!