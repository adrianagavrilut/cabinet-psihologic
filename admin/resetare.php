<?php
require_once '../config.php';

$user = Utilizator::getUserByHashResetare($_GET['hash'], true);
if (!$user) {
    throw new Exception('Wrong hash');
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] ==  $_POST['second_password']) {
        Utilizator::resetPassword($_GET['hash'], $_POST['password']);
        Utilizator::authenticateUser($user->email, $user->rol, $user->id);
        if (Utilizator::checkUserPacient()) {
            header('Location: /pacient');
        } else {
            header('Location: /admin');
        }
    } else {
        throw new Exception('Parolele trebuie sa fie identice');
    }
}

$template = $twig->load('admin/resetare.html.twig');

echo $template->render(['hash' => $_GET['hash']]);
