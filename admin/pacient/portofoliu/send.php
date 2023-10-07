<?php
require_once '../../../config.php';

if (!Utilizator::checkUserAuth()) {
    throw new Exception("A User must be signed in");
}
$filePath = false;
if (array_key_exists('file', $_FILES) && $_FILES['file']['tmp_name']) {
    if (!empty($_FILES['file']['tmp_name'])) {
        $filePath = Document::uploadFile($_FILES['file']['name'], $_FILES['file']['tmp_name']);
    }
}
if ($filePath) {
    if (Utilizator::checkUserMedic()) {
        $pacient = Pacient::get($_SESSION['userId'], $_GET['id_pacient']);
        Document::insertFileForMedic($_SESSION['userId'], $pacient->id_utilizator, $filePath, $_FILES['file']['name']);
    } else if (Utilizator::checkUserPacient()) {
        Document::insertFileForPacient($_SESSION['userId'], $filePath, $_FILES['file']['name']);
    }
}
echo $filePath;
