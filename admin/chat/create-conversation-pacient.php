<?php

require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserPacient()) {
    throw new Exception("A 'Pacient' must be signed in");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pacient = Pacient::getForUserId($_SESSION['userId']);
    $medic = Medic::get($pacient->id_medic);
    $conversation = Chat::createConversation($medic->id_utilizator, $pacient->id);
    echo $conversation['id'];
}
