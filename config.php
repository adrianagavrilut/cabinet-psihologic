<?php
const BASE_URL = 'http://office.local';
require_once __DIR__ . '/model/config.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/services/mail.php';
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/views');
$twig = new \Twig\Environment($loader, [
    'debug' => true
]);

$twig->addExtension(new \Twig\Extension\DebugExtension());


function initSession()
{
    session_start();
}
initSession();
