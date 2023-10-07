<?php
require_once '../config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = Utilizator::resetPasswordRequest($_POST['email']);


    Mail::sendResetPasswordEmail($user->email, $user->nume, $user->hash_resetare);
    $template = $twig->load('admin/new-password.html.twig');

    echo $template->render(['email' => $_POST['email'], 'result' => 'success']);
}
