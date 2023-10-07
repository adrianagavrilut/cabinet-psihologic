<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}

$teste = Test::getAll($_SESSION['userId']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = Pacient::create($_SESSION['userId'], $_POST['email'], $_POST['nume'], $_POST['data_nasterii'], $_POST['gen'], $_POST['telefon'], $_POST['adresa'], $_POST['teste']);
    if ($result === true) {
        header('Location: /admin/pacient');
        exit();
    }
    $template = $twig->load('admin/pacient/new.html.twig');

    echo $template->render(['userRole' => $_SESSION['rol'], 'teste' => $teste, 'pacient' => $result['pacient'], 'errors' => $result['errors']]);
    exit();
}

$template = $twig->load('admin/pacient/new.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'teste' => $teste]);
