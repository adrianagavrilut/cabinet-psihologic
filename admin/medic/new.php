<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserAdmin()) {
    header("Location:/admin/login.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = '';
    if (!empty($_FILES['imagine']['tmp_name'])) {
        $imagePath = Medic::uploadImage($_FILES['imagine']);
    }
    $result = Medic::create($_POST['email'], $_POST['nume'], $_POST['specialitate'], $_POST['data_nasterii'], $_POST['telefon'], $_POST['adresa'], $imagePath);
    if ($result === true) {
        header('Location: /admin/medic');
        exit();
    }
    $template = $twig->load('admin/medic/new.html.twig');

    echo $template->render(['userRole' => $_SESSION['rol'], 'doctor' => $result['doctor'], 'errors' => $result['errors']]);
    exit();
}

$template = $twig->load('admin/medic/new.html.twig');

echo $template->render(['userRole' => $_SESSION['rol']]);
