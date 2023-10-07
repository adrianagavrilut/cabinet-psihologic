<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserAdmin()) {
    header("Location:/admin/login.php");
}

Testimonial::delete($_GET['id']);
header('Location: /admin/review');
exit();
