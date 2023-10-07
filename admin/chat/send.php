<?php
require_once '../../config.php';


if (!Utilizator::checkUserAuth()) {
    throw new Exception("A User must be signed in");
}

$filePaths = [];
if (array_key_exists('file', $_FILES) && $_FILES['file']['tmp_name']) {
    foreach ($_FILES['file']['tmp_name'] as $index => $filename) {
        if (!empty($_FILES['file']['tmp_name'][$index])) {
            $filePath = Chat::uploadFile($_FILES['file']['name'][$index], $_FILES['file']['tmp_name'][$index]);
            if ($filePath) {
                $filePaths[] = $filePath;
            }
        }
    }
}
$message = Chat::createMessage($_SESSION['userId'], $_POST['conversation_id'], $_POST['content'], Utilizator::checkUserMedic(), json_encode($filePaths));
$template = $twig->load('admin/chat/outgoing-message.html.twig');
echo $template->render(['name' => $message['user']->nume, 'content' => $message['continut'], 'file' => $message['fisier']]);
