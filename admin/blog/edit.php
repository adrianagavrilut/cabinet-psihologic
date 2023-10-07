<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserAdmin()) {
    header("Location:/admin/login.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = '';
    if (!empty($_FILES['imagine']['tmp_name'])) {
        $imagePath = Articol::uploadImage($_FILES['imagine']);
    }
    $result = Articol::update($_GET['id'], $_POST['titlu'], $_POST['continut'], $imagePath, $_POST['categorie']);
    if ($result === true) {
        header('Location: /admin/blog');
        exit();
    }
    $template = $twig->load('admin/blog/edit.html.twig');

    echo $template->render(['userRole' => $_SESSION['rol'], 'articol' => $result['articol'], 'id' => $_GET['id'], 'errors' => $result['errors']]);
    exit();
}

$template = $twig->load('admin/blog/edit.html.twig');

$articol = Articol::get($_GET['id']);

echo $template->render(['userRole' => $_SESSION['rol'], 'articol' => $articol, 'id' => $_GET['id']]);
