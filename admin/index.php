<?php
require_once '../config.php';

if (!Utilizator::checkUserAuth()) {
    header("Location:/admin/login.php");
}
$numarMedici = Utilizator::getNrMedic();
$numarPacienti = Utilizator::getNrPacient();
$numarArticole = Articol::getNrArticole();
$numarPacientiForMedic = Pacient::getNrForMedic($_SESSION['userId']);
$numarTesteForMedic = Test::getNrForMedic($_SESSION['userId']);
$numarConvForMedic = Chat::getNrConvForMedic($_SESSION['userId']);
$numarReviews = Testimonial::getNrTestimoniale();


$template = $twig->load('admin/index.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'numarMedici' => $numarMedici, 'numarPacienti' => $numarPacienti, 'numarArticole' => $numarArticole, 'numarPacientiForMedic' => $numarPacientiForMedic, 'numarTesteForMedic' => $numarTesteForMedic, 'numarConvForMedic' => $numarConvForMedic, 'numarReviews' => $numarReviews]);
