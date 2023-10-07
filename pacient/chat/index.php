<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserPacient()) {
    header("Location:/admin/login.php");
}

try {
    $conversation = Chat::getConversationForPacient($_SESSION['userId']);
    $messages = [];
    $hasConversation = false;
    if ($conversation) {
        $hasConversation = true;
        $messages = Chat::getAllMessages($conversation['id'], $_SESSION['userId'], false);
    }
    $template = $twig->load('pacient/chat/index.html.twig');
    echo $template->render(['userRole' => $_SESSION['rol'], 'hasConversation' => $hasConversation, 'conversation' => $conversation, 'messages' => $messages]);
} catch (Exception $e) {
    // log or otherwise register the error
    header('HTTP/1.1 500 Internal Server Error');
}
