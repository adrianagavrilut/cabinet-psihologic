<?php
require_once '../config.php';

try {
    $articole = Articol::getAll();
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
}

$template = $twig->load('blog/index.html.twig');

echo $template->render(['articole' => $articole]);
