<?php
require_once '../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserPacient()) {
    header("Location:/admin/login.php");
}

try {

    $pacient = Pacient::getForUserId($_SESSION['userId']);
    $teste = Test::getAllForPacient($pacient->id);
    $raspunsuri = Raspuns::getAll($pacient->id);

    $result = [];
    foreach ($teste as $test) {
        $intrebari = Intrebare::getAllForTest($test->id);
        $result[$test->id] = [
            'test' => $test,
            'intrebari' => []
        ];
        foreach ($intrebari as $intrebare) {
            $result[$test->id]['intrebari'][$intrebare->id]['intrebare'] = $intrebare;
            $result[$test->id]['intrebari'][$intrebare->id]['raspuns'] = [];
        }
    }

    foreach ($raspunsuri as $raspuns) {
        $result[$raspuns->intrebare_test]['intrebari'][$raspuns->id_intrebare]['raspuns'] = $raspuns;
    }
} catch (Exception $e) {
    // log or otherwise register the error
    header('HTTP/1.1 500 Internal Server Error');
}

$template = $twig->load('pacient/index.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'result' => $result, 'pacient' => $pacient]);
