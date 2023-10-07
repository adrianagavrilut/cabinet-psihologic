<?php
require_once '../../config.php';

$filteredArticles = array();
$searchQuery = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $searchQuery = $_GET['search'];
    $filteredArticles = Articol::searchByTitle($searchQuery);
}
$template = $twig->load('blog/search/index.html.twig');
echo $template->render(['articole' => $filteredArticles, 'searchQuery' => $searchQuery]);
