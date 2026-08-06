<?php


$_SESSION['flash'] = 'Вы вышли из своего аккаунта' . '<br>';
unset($_SESSION['auth']);
unset($_SESSION['id']);
unset($_SESSION['login']);
unset($_SESSION['role']);


header('Location: /');


die();
?>

