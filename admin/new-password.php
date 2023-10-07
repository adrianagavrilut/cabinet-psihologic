<?php
require_once '../config.php';

$template = $twig->load('admin/new-password.html.twig');

echo $template->render();
