<?php

$content = '';
$error = '';
$success = false;
$error_price = '';
$existing_tutor = null;
$error_subject = '';
$error_experience = '';
$error_duration = '';
$error_education = '';
$error_about = '';
$error_phone = '';
$error_location = '';
$errors = [];

$error_select = '';
$error_update = '';
$error_insert = '';

if (empty($_SESSION['auth']) || $_SESSION['role'] !== 'Репетитор') {
    $content .= '<p>Вы не авторизованы. Пройдите авторизацию</p>';
} else {
    $user_id = $_SESSION['id'];
    $stmt_check = mysqli_prepare($link, "SELECT * FROM tutors WHERE user_id = ?");

    if ($stmt_check !== false) {

        mysqli_stmt_bind_param($stmt_check, "i", $user_id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $existing_tutor = mysqli_fetch_assoc($result_check);
        mysqli_stmt_close($stmt_check);
    } else {
        $error_select = 'Что-то пошло не так, попробуйте еще';
    }
    if (!empty($_POST) && empty($error_select)) {
        $subject = trim($_POST['subject'] ?? '');
        $experience = trim($_POST['experience'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $education = trim($_POST['education'] ?? '');
        $about = trim($_POST['about'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $location = trim($_POST['location'] ?? '');

        if (empty($price)) {
            $error_price = 'Вы не ввели стоимость услуги';
        } elseif (!is_numeric($price)) {
            $error_price = 'Стоимость должна быть числом';
        } elseif (str_contains($price, '.') || str_contains($price, ',')) {
            $error_price = 'Стоимость должна быть целым числом';
        } elseif ($price <= 0) {
            $error_price = 'Стоимость должна быть положительным числом';
        } 

        if (empty($subject)) {
            $error_subject = 'Вы не указали предмет';
        } elseif (mb_strlen($subject) < 2) {
            $error_subject = 'Название предмета слишком короткое';
        } elseif (mb_strlen($subject) > 100) {
            $error_subject = 'Название предмета слишком длинное';
        }

        if (empty($experience)) {
            $error_experience = 'Вы не указали свой опыт';
        } elseif (!is_numeric($experience)) {
            $error_experience = 'Опыт должен быть указан числом';
        } elseif (str_contains($experience, '.') || str_contains($experience, ',')) {
            $error_experience = 'Значение должно быть целым числом';
        } elseif ($experience < 0) {
            $error_experience = 'Опыт не может быть меньше нуля';
        } elseif ($experience > 60) {
            $error_experience = 'Опыт не может быть больше 60 лет';
        }


        if (empty($duration)) {
            $error_duration = 'Вы не указали длительность занятия';
        } elseif (!is_numeric($duration)) {
            $error_duration = 'Значение должно быть числом';
        } elseif (str_contains($duration, '.') || str_contains($duration, ',')) {
            $error_duration = 'Значение должно быть целым числом';
        } elseif ($duration <= 0) {
            $error_duration = 'Значение должно быть положительным числом';
        } elseif ($duration < 15) {
            $error_duration = 'Минимальная длительность занятий - 15 минут';
        } elseif ($duration > 240) {
            $error_duration = 'Максимальная длительность занятий - 240 минут';
        }

        if (empty($education)) {
            $error_education = 'Вы не указали образование';
        } elseif (mb_strlen($education) < 3) {
            $error_education = 'Слишком короткое значение';
        } elseif (mb_strlen($education) > 100) {
            $error_education = 'Слишком длинное значение';
        }
        if (!empty($about) && mb_strlen($about) < 20) {
            $error_about = 'Укажите больше информации о себе';
        } elseif (mb_strlen($about) > 500) {
            $error_about = 'Слишком много символов';
        }

        if (empty($phone)) {
            $error_phone = 'Вы не указали номер телефона';
        } elseif (!str_starts_with($phone, '+7')) {
            $error_phone = 'Номер телефона должен начинаться с +7';
        } else {
            $phone_num = substr($phone, 2);
            if (!ctype_digit($phone_num)) {
                $error_phone = 'Номер телефона должен содержать только цифры';
            } elseif (strlen($phone_num) !== 10) {
                $error_phone = 'Номер должен содержать 10 цифр';
            }
        }

        if (empty($location)) {
            $error_location = 'Вы не указали свое местоположение';
        } elseif (mb_strlen($location) <= 2) {
            $error_location = 'Значение поля должно быть длиннее';
        } elseif (mb_strlen($location) > 30) {
            $error_location = 'Значение поля слишком длинное';
        }

        $errors = [
            $error_about,
            $error_duration,
            $error_education,
            $error_experience,
            $error_location,
            $error_phone,
            $error_price,
            $error_subject
        ];

        $filtered_error = array_filter($errors);


        if (empty($filtered_error)) {

            if ($existing_tutor) {
                $stmt_insert = mysqli_prepare($link, "UPDATE tutors SET subject = ?, experience = ?, price = ?, duration = ?, education = ?, about = ?, phone = ?, location = ? WHERE user_id = ?");
                if ($stmt_insert !== false) {
                    mysqli_stmt_bind_param($stmt_insert, "siiissssi", $subject, $experience, $price, $duration, $education, $about, $phone, $location, $user_id);
                    $success = mysqli_stmt_execute($stmt_insert);
                    mysqli_stmt_close($stmt_insert);
                } else {
                    $error_update = 'Что-то пошло не так, попробуйте еще раз';
                }
            } else {
                $stmt_insert = mysqli_prepare($link, "INSERT INTO tutors (subject, experience, price, duration, education, about, phone, location, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt_insert !== false) {
                    mysqli_stmt_bind_param($stmt_insert, "siiissssi", $subject, $experience, $price, $duration, $education, $about, $phone, $location, $user_id);
                    $success = mysqli_stmt_execute($stmt_insert);
                    mysqli_stmt_close($stmt_insert);
                } else {
                    $error_insert = 'Что-то пошло не так, попробуйте еще раз';
                }
            }

            if ($success) {
                $_SESSION['flash'] = 'Анкета успешно сохранена!';
                header('Location: /tutor-profile');
                die();
            } elseif (empty($error_update) && empty($error_insert)) {
                $error = 'Попробуйте внести изменения еще раз';
            }
        }
    }
}
if (!$success && !(empty($_SESSION['auth']) || $_SESSION['role'] !== 'Репетитор')) {
    $content .= '<form action="" method="POST">';
    if ($error) {
        $content .= '<p>' . $error . '</p>';
    }

    if ($error_select) {
        $content .= '<p>' . $error_select . '</p>';
    }

    if ($error_update) {
        $content .= '<p>' . $error_update . '</p>';
    }

    if ($error_insert) {
        $content .= '<p>' . $error_insert . '</p>';
    }


    foreach ($errors as $error_message) {
        if (!empty($error_message)) {
            $content .= '<p>' . $error_message . '</p>';
        }
    }

    if (!empty($_POST) && empty($error_select)) {
        $price_value = htmlspecialchars($price, ENT_QUOTES);
        $subject_value = htmlspecialchars($subject, ENT_QUOTES);
        $experience_value = htmlspecialchars($experience, ENT_QUOTES);
        $duration_value = htmlspecialchars($duration, ENT_QUOTES);
        $education_value = htmlspecialchars($education, ENT_QUOTES);
        $about_value = htmlspecialchars($about, ENT_QUOTES);
        $phone_value = htmlspecialchars($phone, ENT_QUOTES);
        $location_value = htmlspecialchars($location, ENT_QUOTES);
    } elseif ($existing_tutor) {
        $price_value = htmlspecialchars($existing_tutor['price'], ENT_QUOTES);
        $subject_value = htmlspecialchars($existing_tutor['subject'], ENT_QUOTES);
        $experience_value = htmlspecialchars($existing_tutor['experience'], ENT_QUOTES);
        $duration_value = htmlspecialchars($existing_tutor['duration'], ENT_QUOTES);
        $education_value = htmlspecialchars($existing_tutor['education'], ENT_QUOTES);
        $about_value = htmlspecialchars($existing_tutor['about'], ENT_QUOTES);
        $phone_value = htmlspecialchars($existing_tutor['phone'], ENT_QUOTES);
        $location_value = htmlspecialchars($existing_tutor['location'], ENT_QUOTES);
    } else {
        $price_value = '';
        $subject_value = '';
        $experience_value = '';
        $duration_value = '';
        $education_value = '';
        $about_value = '';
        $phone_value = '';
        $location_value = '';
    }



    $content .= '<input name="subject" placeholder="Предмет" value="' . $subject_value . '"><br>';
    $content .= '<input name="experience" placeholder="Опыт работы (год)" value="' . $experience_value . '"><br>';
    $content .= '<input name="price" placeholder="Стоимость" value="' . $price_value . '"><br>';
    $content .= '<input name="duration" placeholder="Длительность занятия (мин)" value="' . $duration_value . '"><br>';
    $content .= '<input name="education" placeholder="Образование" value="' . $education_value . '"><br>';
    $content .= '<textarea name="about" placeholder="О себе">' . $about_value . '</textarea><br>';
    $content .= '<input name="phone" placeholder="Номер телефона" value="' . $phone_value . '"><br>';
    $content .= '<input name="location" placeholder="Город" value="' . $location_value . '"><br>';

    $content .= '<input type="submit">';
    $content .= '</form>';
}


return ['title' => 'Анкета репетитора', 'content' => $content];
