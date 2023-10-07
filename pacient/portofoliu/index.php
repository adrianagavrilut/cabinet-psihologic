<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserPacient()) {
    header("Location:/admin/login.php");
}

try {
    $documents = Document::getDocumentsForPacient($_SESSION['userId']);
    $template = $twig->load('pacient/portofoliu/index.html.twig');
    echo $template->render(['documents' => $documents]);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
}
