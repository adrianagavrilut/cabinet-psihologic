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
    $result = Medic::update($_GET['id'], $_POST['email'], $_POST['nume'], $_POST['specialitate'], $_POST['data_nasterii'], $_POST['telefon'], $_POST['adresa'], $imagePath);
    if ($result === true) {
        header('Location: /admin/medic');
        exit();
    }
    $template = $twig->load('admin/medic/edit.html.twig');

    echo $template->render(['userRole' => $_SESSION['rol'], 'doctor' => $result['doctor'], 'id' => $_GET['id'], 'errors' => $result['errors']]);
    exit();
}

$template = $twig->load('admin/medic/edit.html.twig');

$doctor = Medic::get($_GET['id']);

echo $template->render(['userRole' => $_SESSION['rol'], 'doctor' => $doctor, 'id' => $_GET['id']]);
