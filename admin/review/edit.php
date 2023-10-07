<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserAdmin()) {
    header("Location:/admin/login.php");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = '';
    if (!empty($_FILES['imagine']['tmp_name'])) {
        $imagePath = Testimonial::uploadImage($_FILES['imagine']);
    }
    $result = Testimonial::update($_GET['id'], $_POST['titlu'], $_POST['continut'], $_POST['rating'], $_POST['nume'], $_POST['despre'], $imagePath);
    if ($result === true) {
        header('Location: /admin/review');
        exit();
    }
    $template = $twig->load('admin/review/edit.html.twig');

    echo $template->render(['userRole' => $_SESSION['rol'], 'review' => $result['testimonial'], 'id' => $_GET['id'], 'errors' => $result['errors']]);
    exit();
}

$template = $twig->load('admin/review/edit.html.twig');

$review = Testimonial::get($_GET['id']);

echo $template->render(['userRole' => $_SESSION['rol'], 'review' => $review, 'id' => $_GET['id']]);
