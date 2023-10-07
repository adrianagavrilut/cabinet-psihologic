<?php

require_once '../../config.php';

if (!Utilizator::checkUserAuth()) {
    throw new Exception("A User must be signed in");
}

$messages = Chat::getUnseenMessages($_GET['conversation_id'], $_SESSION['userId'], Utilizator::checkUserMedic());
$template = $twig->load('admin/chat/conversation.html.twig');
echo $template->render(['messages' => $messages]);
