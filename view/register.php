<?php

$content = '';
$success = false;
$error_login = '';
$error_password = '';
$error_email = '';
$error_birthday = '';
$error_name = '';
$error_surname = '';
$error_city = '';
$error_patronymic = '';
$error_role = '';
$error_db = '';


if (!empty($_POST)) {
    if (
        !empty($_POST['login']) &&
        !empty($_POST['password']) &&
        !empty($_POST['surname']) &&
        !empty($_POST['name']) &&
        !empty($_POST['email']) &&
        !empty($_POST['birthday']) &&
        !empty($_POST['confirm']) &&
        !empty($_POST['city']) &&
        !empty($_POST['patronymic']) &&
        !empty($_POST['role'])
    ) {

        $login = trim($_POST['login']);
        $password = ($_POST['password']);
        $email = trim($_POST['email']);
        $city = trim($_POST['city']);
        $birthday = trim($_POST['birthday']);
        $name = trim($_POST['name']);
        $surname = trim($_POST['surname']);
        $patronymic = trim($_POST['patronymic']);
        $role = trim($_POST['role']);

        if (
            $login !== '' &&
            $password !== '' &&
            $surname !== '' &&
            $name !== '' &&
            $email !== '' &&
            $birthday !== '' &&
            $_POST['confirm'] !== '' &&
            $city !== '' &&
            $patronymic !== '' &&
            $role !== ''
        ) {



            if (in_array($role, ['Репетитор', 'Клиент'])) {
                if (4 <= strlen($login) and strlen($login) <= 10) {
                    if (preg_match('#^[a-zA-Z0-9]+$#', $login)) {
                        if (6 <= strlen($password) and strlen($password) <= 12) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $birthday)) {
                                    $birthday_check = explode('-', $birthday);
                                    $date_correct = checkdate(
                                        $birthday_check[1],
                                        $birthday_check[2],
                                        $birthday_check[0]
                                    );
                                    if ($date_correct) {
                                        if ($birthday <= date('Y-m-d')) {

                                        if ($_POST['password'] == $_POST['confirm']) {
                                            $stmt_check = mysqli_prepare($link, "SELECT * FROM users WHERE login = ?");
                                            mysqli_stmt_bind_param($stmt_check, "s", $login);
                                            mysqli_stmt_execute($stmt_check);
                                            $result_check = mysqli_stmt_get_result($stmt_check);
                                            $user = mysqli_fetch_assoc($result_check);
                                            mysqli_stmt_close($stmt_check);

                                            if (empty($user)) {
                                                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                                                $stmt_insert = mysqli_prepare($link, "INSERT INTO users SET login = ?, password = ?, email = ?, birthday = ?, name = ?, surname = ?, city = ?, patronymic = ?, role = ?");
                                                mysqli_stmt_bind_param($stmt_insert, "sssssssss", $login, $password, $email, $birthday, $name, $surname, $city, $patronymic, $role);
                                                $success = mysqli_stmt_execute($stmt_insert);
                                                mysqli_stmt_close($stmt_insert);

                                                if ($success) {
                                                    
                                                    $_SESSION['flash'] = 'Регистрация прошла успешно!';
                                                    $_SESSION['auth'] = true;
                                                    $_SESSION['login'] = $login;
                                                    header('Location: /');
                                                    die();
                                                } else {
                                                    $error_db = 'Не удалось сохранить данные, попробуйте еще раз';
                                                }
                                            } else {
                                                $error_login = 'Логин уже занят';
                                            }
                                        } else {
                                            $error_password = 'Пароли не совпадают';
                                        }
                                    } else {
                                        $error_birthday = 'Проверьте дату рождения';
                                    }
                                } else {
                                    $error_birthday = 'Такой даты не существует';
                                   }
                                } else {
                                    $error_birthday = 'Дата введена некорректно';
                                }
                            } else {
                                $error_email = 'Некорректная электронная почта';
                            }
                        } else {
                            $error_password = 'Пароль должен быть от 6 до 12 символов';
                        }
                    } else {
                        $error_login = 'Логин может содержать только латинские буквы и цифры';
                    }
                } else {
                    $error_login = 'Логин должен быть от 4 до 10 символов';
                }
            } else {
                $error_role = 'Некорректная роль';
            }
        } else {
            if (empty($_POST['name'])) {
                $error_name = 'Вы не ввели свое имя';
            }
            if (empty($_POST['surname'])) {
                $error_surname = 'Вы не ввели свою фамилию';
            }
            if (empty($_POST['city'])) {
                $error_city = 'Укажите город';
            }
            if (empty($_POST['patronymic'])) {
                $error_patronymic = 'Вы не ввели свое отчество';
            }
            if (empty($_POST['login'])) {
                $error_login = 'Вы не ввели логин';
            }
            if (empty($_POST['password'])) {
                $error_password = 'Вы не ввели пароль';
            }
            if (empty($_POST['email'])) {
                $error_email = 'Вы не ввели электронную почту';
            }
            if (empty($_POST['birthday'])) {
                $error_birthday = 'Вы не указали свою дату рождения';
            }
        }
    }
}

if (!$success) {
    $content .= '<form action="" method="POST">';
    if ($error_db) {
        $content .= '<p>' . $error_db . '</p>';
    }

    if ($error_login) {
        $content .= '<p>' . $error_login . '</p>';
    }
    $content .= '<input name="login" placeholder="Введите ваш логин"><br>';
    if ($error_password) {
        $content .= '<p>' . $error_password . '</p>';
    }
    $content .= '<input type="password" name="password" placeholder="Введтие ваш пароль"><br>';
    $content .= '<input type="password" name="confirm" placeholder="Подтвердите ваш пароль"><br>';
    if ($error_birthday) {
        $content .= '<p>' . $error_birthday . '</p>';
    }
    $content .= '<input name="birthday" type="date" placeholder="Введите дату рождения"><br>';
    if ($error_email) {
        $content .= '<p>' . $error_email . '</p>';
    }
    $content .= '<input name="email" placeholder="Введите ваш электронный адрес"><br>';
    if ($error_name) {
        $content .= '<p>' . $error_name . '</p>';
    }
    $content .= '<input name="name" placeholder="Введите ваше имя"><br>';
    if ($error_surname) {
        $content .= '<p>' . $error_surname . '</p>';
    }
    $content .= ' <input name="surname" placeholder="Введите вашу фамилию"><br>';
    if ($error_patronymic) {
        $content .= '<p>' . $error_patronymic . '</p>';
    }
    $content .= '<input name="patronymic" placeholder="Введите ваше отчество"><br>';
    if ($error_city) {
        $content .= '<p>' . $error_city . '</p>';
    }
    $content .= '<input name="city" placeholder="Введите город вашего проживания"><br>';
}

if ($error_role) {
    $content .= '<p>' . $error_role . '</p>';
}
$content .= '<select name="role"><option>Репетитор</option><option>Клиент</option></select><br>';
$content .= '<input type="submit"><br>';
$content .= '</form> ';

return [
    'title' => 'Регистрация',
    'content' => $content,

];
