<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserPacient()) {
    header("Location:/admin/login.php");
}

try {
    $pacient = Pacient::getForUserId($_SESSION['userId']);
    $test = Test::getForPacient($pacient->id, $_GET['id_test']);
    $raspunsuri = Raspuns::getAllForTest($pacient->id, $test->id);

    $result = [];
    $intrebari = Intrebare::getAllForTest($_GET['id_test']);
    foreach ($intrebari as $intrebare) {
        $result[$intrebare->id]['intrebare'] = $intrebare;
        $result[$intrebare->id]['raspuns'] = [];
    }

    foreach ($raspunsuri as $raspuns) {
        $result[$raspuns->id_intrebare]['raspuns'] = $raspuns;
    }
    $template = $twig->load('pacient/test/index.html.twig');

    echo $template->render(['userRole' => $_SESSION['rol'], 'result' => $result, 'test' => $test]);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
}
