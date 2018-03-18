<?php

/*
 * Author: Radoslaw Wrobel
 * radoslaw.wrobel@gmx.com
 * License: MIT
 */

require_once 'vendor/autoload.php';
require_once 'src/LoginUser.php';
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (($_SERVER['REQUEST_METHOD'] === 'POST')) {

    $login = $_POST['login'];
    $pass = $_POST['password'];
    $user = new LoginUser($login, $pass);
    if ($user->checkData()) {
        $_SESSION['loginPassed'] = true;
        header('Location: main.php');
        exit;
    }
    if ($user->getServerStatus()) {
        $_SESSION['serverError'] = true;
    }
    if ($user->getLoginStatus()) {
        $_SESSION['invalidLogin'] = true;
    }
    if ($user->getPassStatus()) {
        $_SESSION['invalidPass'] = true;
    }
    header('Location: login.php');
} else {
    $loader = new Twig_Loader_Filesystem('twig/en');
    $twig = new Twig_Environment($loader);
    $context = array(
        'title' => '',
        'header' => ''
    );

    if (isset($_SESSION['invalidLogin'])) {
        $context['invalidLogin'] = $_SESSION['invalidLogin'];
        $context['e_login'] = true;
        unset($_SESSION['invalidLogin']);
    }
    if (isset($_SESSION['invalidPass'])) {
        $context['invalidPass'] = $_SESSION['invalidPass'];
        $context['e_login'] = true;
        unset($_SESSION['invalidPass']);
    }
    if (isset($_SESSION['serverError'])) {
        $context['serverError'] = $_SESSION['serverError'];
        unset($_SESSION['serverError']);
    }
    echo $twig->render('login.html', $context);
}