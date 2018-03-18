<?php

/*
 * Author: Radoslaw Wrobel
 * radoslaw.wrobel@gmx.com
 * License: MIT
 */

require_once 'vendor/autoload.php';
session_start();

if (!isset($_SESSION['loginPassed'])) {
    header('Location: index.php');
    exit();
}

$loader = new Twig_Loader_Filesystem('twig/en');
$twig = new Twig_Environment($loader);
$context = array(
    'title' => '',
    'header' => ''
);

if (isset($_SESSION['loginPassed'])) {
    $context['loginPassed'] = $_SESSION['loginPassed'];
}
if (isset($_SESSION['serverError'])) {
    $context['serverError'] = $_SESSION['serverError'];
    unset($_SESSION['serverError']);
}
echo $twig->render('main.html', $context);
