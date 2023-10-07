<?php

require_once '../../config.php';

$conversation = Chat::getConversation($_GET['id']);
$messages = Chat::getAllMessages($_GET['id'], $_SESSION['userId'], true);
$template = $twig->load('admin/chat/conversation.html.twig');
echo $template->render(['messages' => $messages]);
