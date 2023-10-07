<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}

try {
    $teste = Test::getAll($_SESSION['userId']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $result = Pacient::update($_SESSION['userId'], $_GET['id'], $_POST['email'], $_POST['nume'], $_POST['data_nasterii'], $_POST['gen'], $_POST['telefon'], $_POST['adresa'], $_POST['teste']);
        if ($result === true) {
            header('Location: /admin/pacient');
            exit();
        }
        $testePacient = PacientTest::getAllTestIdsForPacient($_GET['id']);
        $template = $twig->load('admin/pacient/edit.html.twig');

        echo $template->render(['userRole' => $_SESSION['rol'], 'pacient' => $result['pacient'], 'id' => $_GET['id'], 'errors' => $result['errors'], 'teste' => $teste, 'testePacient' => $testePacient]);
        exit();
    }

    $template = $twig->load('admin/pacient/edit.html.twig');

    $pacient = Pacient::get($_SESSION['userId'], $_GET['id']);
    $testePacient = PacientTest::getAllTestIdsForPacient($_GET['id']);
    echo $template->render(['userRole' => $_SESSION['rol'], 'pacient' => $pacient, 'id' => $_GET['id'], 'teste' => $teste, 'testePacient' => $testePacient]);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
}
