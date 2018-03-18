<?php

/*
 * Author: Radoslaw Wrobel
 * radoslaw.wrobel@gmx.com
 * License: MIT
 */

require_once 'vendor/autoload.php';
require_once 'src/Registration.php';
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_SESSION['loginPassed'])) {
    header('Location: main.php');
    exit();
}

$loader = new Twig_Loader_Filesystem('twig/en');
$twig = new Twig_Environment($loader);
$context = array(
    'title' => '',
    'header' => ''
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUser = new Registration($_POST['login'], $_POST['pass1'], $_POST['pass2'], isset($_POST['statute']));
    if ($newUser->getRegSuccess()) {
        $_SESSION['loginPassed'] = true;
        header('Location: main.php');
        exit;
    }
    if ($newUser->getServerStatus()) {
        $_SESSION['serverError'] = true;
    }
    if ($newUser->getLoginAccessStatus()) {
        $context['e_nick'] = true;
        $context['e_register'] = true;
    }
    if ($newUser->getLoginSyntaxStatus()) {
        $context['e_login_stx'] = true;
        $context['e_register'] = true;
    }
    if ($newUser->getLoginLengthStatus()) {
        $context['e_login_len'] = true;
        $context['e_register'] = true;
    }    
    if ($newUser->getPassLengthStatus()) {
        $context['e_pass_length'] = true;
        $context['e_register'] = true;
    }
    if ($newUser->getPassEqualityStatus()) {
        $context['e_pass_eq'] = true;
        $context['e_register'] = true;
    }
    if ($newUser->getStatuteStatus()) {
        $context['e_statute'] = true;
        $context['e_register'] = true;
    }
}
echo $twig->render('registration.html', $context);