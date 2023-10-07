<?php
require_once '../config.php';

$user = Utilizator::getUserByHashActivare($_GET['hash'], true);
$error = '';
if (!$user) {
    $error = 'Link-ul pentru activare este invalid sau a expirat.'; //wrong hash
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] ==  $_POST['second_password']) {
        Utilizator::activateUser($_GET['hash'], $_POST['password']);
        Utilizator::authenticateUser($user->email, $user->rol, $user->id);
        if (Utilizator::checkUserPacient()) {
            header('Location: /pacient');
        } else {
            header('Location: /admin');
        }
        exit();
    } else {
        $error = 'Parolele trebuie sa fie identice!';
    }
}
$template = $twig->load('admin/activare.html.twig');
echo $template->render(['hash' => $_GET['hash'], 'error' => $error]);
