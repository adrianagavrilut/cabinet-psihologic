<?php
require_once '../../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}
try {
    $teste = Test::getAllForPacient($_GET['id_pacient']);
    $raspunsuri = Raspuns::getAll($_GET['id_pacient']);
    $pacient = Pacient::get($_SESSION['userId'], $_GET['id_pacient']);

    $result = [];
    foreach ($teste as $test) {
        $intrebari = Intrebare::getAll($_SESSION['userId'], $test->id);
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

$template = $twig->load('admin/pacient/test/index.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'id_pacient' => $_GET['id_pacient'], 'result' => $result, 'numePacient' => $pacient->nume]);
