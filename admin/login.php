<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = Utilizator::checkUser($_POST['email'], $_POST['password']);
    if (!$user) {
        $template = $twig->load('admin/login.html.twig');
        echo $template->render(['data' => $_POST, 'error' => '1']);
        exit();
    }

    Utilizator::authenticateUser($_POST['email'], $user->rol, $user->id);
    if (Utilizator::checkUserPacient()) {
        header('Location: /pacient');
    } else {
        header('Location: /admin');
    }
}
$template = $twig->load('admin/login.html.twig');

echo $template->render();
