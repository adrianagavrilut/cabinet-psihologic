<?php

require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    throw new Exception("A 'Medic' must be signed in");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //TODO: check if there is already a convo with the patient if yes then show message "there already is.." else procede with creating a new conversation
    $conversation = Chat::createConversation($_SESSION['userId'], $_POST['id_pacient']);
    $template = $twig->load('admin/chat/item-conversation.html.twig');
    echo $template->render(['conversation' => $conversation]);
} else {
    $pacients = Pacient::getAll($_SESSION['userId']);
    $template = $twig->load('admin/chat/new-conversation.html.twig');
    echo $template->render(['pacients' => $pacients]);
}
