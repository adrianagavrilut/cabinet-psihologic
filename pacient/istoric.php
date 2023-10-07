<?php
require_once '../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserPacient()) {
    header("Location:/admin/login.php");
}
$pacient = Pacient::getForUserId($_SESSION['userId']);

Pacient::updateHistory($pacient->id, $_POST['coloana'], $_POST['valoare']);
