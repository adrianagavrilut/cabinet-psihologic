<?php
require_once '../config.php';

if (!Utilizator::checkUserAuth()) {
    header("Location: /admin/login.php");
}
Utilizator::deauthenticateUser();
header("Location: /");
