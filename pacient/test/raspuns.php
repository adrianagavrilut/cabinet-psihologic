<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserPacient()) {
    header("Location:/admin/login.php");
}
$pacient = Pacient::getForUserId($_SESSION['userId']);

if ($raspuns = Raspuns::checkAnswerExist($pacient->id, $_POST['id_intrebare'])) {
    Raspuns::update($raspuns->id, $_POST['continut']);
} else {
    Raspuns::create($pacient->id, $_POST['id_intrebare'], $_POST['continut']);
}
