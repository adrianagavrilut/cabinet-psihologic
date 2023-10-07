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
    $result = Articol::create($_POST['titlu'], $_POST['continut'], $imagePath, $_POST['categorie']);
    if ($result === true) {
        header('Location: /admin/blog');
        exit();
    }
    $template = $twig->load('admin/blog/new.html.twig');

    echo $template->render(['userRole' => $_SESSION['rol'], 'articol' => $result['articol'], 'errors' => $result['errors']]);
    exit();
}

$template = $twig->load('admin/blog/new.html.twig');
echo $template->render(['userRole' => $_SESSION['rol']]);
