<?php
require_once './config.php';

$doctors = Medic::getAll();
$articole = Articol::getAll();

$articoleArray = array_map(function ($articol) {
    return $articol->toArray();
}, $articole);
usort($articoleArray, function ($a, $b) {
    $dateA = new DateTime($a['publicare']);
    $dateB = new DateTime($b['publicare']);
    return $dateB <=> $dateA;
});

$testimoniale = Testimonial::getAll();

$template = $twig->load('index.html.twig');

echo $template->render(['doctors' => $doctors, 'articole' => $articoleArray, 'testimoniale' => $testimoniale]);
