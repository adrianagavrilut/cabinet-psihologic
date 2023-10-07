<?php
require_once __DIR__ . '/medic.php';
require_once __DIR__ . '/pacient.php';
require_once __DIR__ . '/utilizator.php';
require_once __DIR__ . '/test.php';
require_once __DIR__ . '/intrebare.php';
require_once __DIR__ . '/pacientTest.php';
require_once __DIR__ . '/raspuns.php';
require_once __DIR__ . '/chat.php';
require_once __DIR__ . '/articol.php';
require_once __DIR__ . '/testimonial.php';
require_once __DIR__ . '/document.php';

function createConnection()
{
    $mysqli = new mysqli("mysql.office.local", "office", "office", "office");
    if ($mysqli->connect_errno) {
        throw new Exception('Error connecting to database');
        exit();
    }
    return $mysqli;
}
