<?php

$content = '';
$success = false;
$error = '';



if (!empty($_POST)) {
    $login = trim($_POST['login']);
    $stmt_check = mysqli_prepare($link, "SELECT * FROM users WHERE login = ?");
    mysqli_stmt_bind_param($stmt_check, "s", $login);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $user = mysqli_fetch_assoc($result_check);
    mysqli_stmt_close($stmt_check);

    if (!empty($user)) {
        $pass = $user['password'];
        if (password_verify($_POST['password'], $pass)) {
         
        $_SESSION['auth'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['login'] = $login;
        $_SESSION['role'] = $user['role'];
         $_SESSION['flash']  = 'Вы успешно авторизовались'; 
        header('Location: /');
        die();
        
        } else {
               $error = 'Неверный логин или пароль';
            }
    } else {
        $error = 'Неверный логин или пароль';
    }
}

$content .= '<form action="" method="POST">';
if ($error) {
    $content .= '<p>' . $error . '</p>';
}
$content .= '<input name="login" placeholder="Введите ваш логин"><br>';

$content .= '<input type="password" name="password" placeholder="Введите ваш пароль"><br>';

$content .= '<input type="submit"><br>';
$content .= '</form> ';

return ['title' => 'Вход', 'content' => $content];


?>