<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

session_start();

require 'connect.php';

$url = $_SERVER['REQUEST_URI'];

if (preg_match('#^/$#', $url, $params)) {
    $page = include 'view/home.php';
} elseif (preg_match('#^/tutor/(?<id>\d+)$#', $url, $params)) {
    $page = include 'view/tutor/show.php';
} elseif (preg_match('#^/register$#', $url, $params)) {
    $page = include 'view/register.php';
} elseif (preg_match('#^/login$#', $url, $params)) {
    $page = include 'view/login.php';
} elseif (preg_match('#^/tutor-profile$#', $url, $params)) {
    $page = include 'view/tutor-profile.php';
} elseif (preg_match('#^/logout$#', $url, $params)) {
    $page = include 'view/logout.php';
} elseif (preg_match('#^/home$#', $url, $params)) {
    $page = include 'view/home.php';
}

$layout = file_get_contents('layout.php');
$layout = str_replace('{{ title }}', $page['title'], $layout);
$layout = str_replace('{{ content }}', $page['content'], $layout);

echo $layout;
?>