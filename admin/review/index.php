<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserAdmin()) {
    header("Location:/admin/login.php");
}

$reviews = Testimonial::getAll();

$template = $twig->load('admin/review/index.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'reviews' => $reviews]);
