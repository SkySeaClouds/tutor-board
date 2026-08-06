<?php

$content = '';
$error = '';
$success = false;
$error_price = '';
$existing_tutor = null;

if (empty($_SESSION['auth']) || $_SESSION['role'] !== 'Репетитор') {
    $content .= '<p>Вы не авторизованы. Пройдите авторизацию</p>';
} else {
    $user_id = $_SESSION['id'];
    $stmt_check = mysqli_prepare($link, "SELECT * FROM tutors WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $user_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $existing_tutor = mysqli_fetch_assoc($result_check);
    mysqli_stmt_close($stmt_check);
    if (!empty($_POST)) {
        $subject = trim($_POST['subject']);
        $experience = trim($_POST['experience']);
        $price = trim($_POST['price']);
        $duration = trim($_POST['duration']);
        $education = trim($_POST['education']);
        $about = trim($_POST['about']);
        $phone = trim($_POST['phone']);
        $location = trim($_POST['location']);

        if (empty($price)) {
            $error_price = 'Вы не ввели стоимость услуги';
        } elseif (!is_numeric($price)) {
            $error_price = 'Стоимость должна быть числом';
        } elseif ($price <= 0) {
            $error_price = 'Стоимость должна быть положительным числом';
        } elseif (str_contains($price, '.') || str_contains($price, ',')) {
            $error_price = 'Стоимость должна быть целым числом';
        }

        if (empty($error_price)) {

            if ($existing_tutor) {
                $stmt_insert = mysqli_prepare($link, "UPDATE tutors SET subject = ?, experience = ?, price = ?, duration = ?, education = ?, about = ?, phone = ?, location = ? WHERE user_id = ?");
                mysqli_stmt_bind_param($stmt_insert, "ssssssssi", $subject, $experience, $price, $duration, $education, $about, $phone, $location, $user_id);
                $success = mysqli_stmt_execute($stmt_insert);
                mysqli_stmt_close($stmt_insert);
            } else {
                $stmt_insert = mysqli_prepare($link, "INSERT INTO tutors SET subject = ?, experience = ?, price = ?, duration = ?, education = ?, about = ?, phone = ?, location = ?, user_id = ?");
                mysqli_stmt_bind_param($stmt_insert, "ssssssssi", $subject, $experience, $price, $duration, $education, $about, $phone, $location, $user_id);
                $success = mysqli_stmt_execute($stmt_insert);
                mysqli_stmt_close($stmt_insert);
            }

            if ($success) {
                $_SESSION['flash'] = 'Анкета успешно сохранена!';
                header('Location: /tutor-profile');
                die();
            } else {
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
    if ($error_price) {
        $content .= '<p>' . $error_price . '</p>';
    }

    if (!empty($_POST)) {
        $price_value = htmlspecialchars($price, ENT_QUOTES);
    } elseif ($existing_tutor) {
        $price_value = htmlspecialchars($existing_tutor['price'], ENT_QUOTES);
    } else {
        $price_value = '';
    }

    if (!empty($_POST)) {
        $subject_value = htmlspecialchars($subject, ENT_QUOTES);
    } elseif ($existing_tutor) {
        $subject_value = htmlspecialchars($existing_tutor['subject'], ENT_QUOTES);
    } else {
        $subject_value = '';
    }

    if (!empty($_POST)) {
        $experience_value = htmlspecialchars($experience, ENT_QUOTES);
    } elseif ($existing_tutor) {
        $experience_value = htmlspecialchars($existing_tutor['experience'], ENT_QUOTES);
    } else {
        $experience_value = '';
    }

    if (!empty($_POST)) {
        $duration_value = htmlspecialchars($duration, ENT_QUOTES);
    } elseif ($existing_tutor) {
        $duration_value = htmlspecialchars($existing_tutor['duration'], ENT_QUOTES);
    } else {
        $duration_value = '';
    }

    if (!empty($_POST)) {
        $education_value = htmlspecialchars($education, ENT_QUOTES);
    } elseif ($existing_tutor) {
        $education_value = htmlspecialchars($existing_tutor['education'], ENT_QUOTES);
    } else {
        $education_value = '';
    }

    if (!empty($_POST)) {
        $about_value = htmlspecialchars($about, ENT_QUOTES);
    } elseif ($existing_tutor) {
        $about_value = htmlspecialchars($existing_tutor['about'], ENT_QUOTES);
    } else {
        $about_value = '';
    }

    if (!empty($_POST)) {
        $phone_value = htmlspecialchars($phone, ENT_QUOTES);
    } elseif ($existing_tutor) {
        $phone_value = htmlspecialchars($existing_tutor['phone'], ENT_QUOTES);
    } else {
        $phone_value = '';
    }

    if (!empty($_POST)) {
        $location_value = htmlspecialchars($location, ENT_QUOTES);
    } elseif ($existing_tutor) {
        $location_value = htmlspecialchars($existing_tutor['location'], ENT_QUOTES);
    } else {
        $location_value = '';
    }



    $content .= '<input name="subject" placeholder="Предмет" value="' . $subject_value . '"><br>';
    $content .= '<input name="experience" placeholder="Опыт работы" value="' . $experience_value . '"><br>';
    $content .= '<input name="price" placeholder="Стоимость" value="' . $price_value . '"><br>';
    $content .= '<input name="duration" placeholder="Длительность занятия" value="' . $duration_value . '"><br>';
    $content .= '<input name="education" placeholder="Образование" value="' . $education_value . '"><br>';
    $content .= '<textarea name="about" placeholder="О себе">' . $about_value . '</textarea><br>';
    $content .= '<input name="phone" placeholder="Номер телефона" value="' . $phone_value . '"><br>';
    $content .= '<input name="location" placeholder="Город" value="' . $location_value . '"><br>';

    $content .= '<input type="submit">';
    $content .= '</form>';
}


return ['title' => 'Анкета репетитора', 'content' => $content];
