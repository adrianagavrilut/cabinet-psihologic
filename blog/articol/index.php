<?php
require_once '../../config.php';

try {
    $articol = Articol::get($_GET['id_articol']);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
}

$template = $twig->load('blog/articol/index.html.twig');

echo $template->render(['articol' => $articol]);
