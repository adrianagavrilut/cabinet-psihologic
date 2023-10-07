<?php
require_once '../../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}
try {
    $pacient = Pacient::get($_SESSION['userId'], $_GET['id_pacient']);
    $documents = Document::getDocuments($_SESSION['userId'], $pacient->id_utilizator);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
}

$template = $twig->load('admin/pacient/portofoliu/index.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'documents' => $documents, 'pacient' => $pacient]);
