<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}
try {

    $pacients = Pacient::getAll($_SESSION['userId']);
    $conversations = Chat::getAllConverations($_SESSION['userId']);
} catch (Exception $e) {
    // log or otherwise register the error
    header('HTTP/1.1 500 Internal Server Error');
}

$template = $twig->load('admin/chat/index.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'pacients' => $pacients, 'conversations' => $conversations]);
