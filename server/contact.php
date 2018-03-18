<?php

/*
 * Author: Radoslaw Wrobel
 * radoslaw.wrobel@gmx.com
 * License: MIT
 */

require_once 'vendor/autoload.php';
session_start();

$loader = new Twig_Loader_Filesystem('twig/en');
$twig = new Twig_Environment($loader);
$context = array(
    'title' => '',
    'header' => ''
);

echo $twig->render('contact.html', $context);
