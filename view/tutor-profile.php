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
            } elseif (str_contains($price, '.')) {
                    $error_price = 'Стоимость должна быть целым числом';
               }
        
        if (empty($error_price)) {

        if ($existing_tutor) {
            $stmt_insert = mysqli_prepare($link, "UPDATE tutors SET subject = ?, experience = ?, price = ?, duration = ?, education = ?, about = ?, phone = ?, location = ? WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt_insert, "ssssssssi", $subject, $experience, $price, $duration, $education, $about, $phone, $location, $user_id);
            $success = mysqli_stmt_execute($stmt_insert);
            mysqli_stmt_close($stmt_insert);
        }else {
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

                        

            
    }  if (!$success) {
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



    $content .= '<input name="subject" placeholder="Предмет" value="' . ($existing_tutor ? htmlspecialchars($existing_tutor['subject'], ENT_QUOTES) : '') . '"><br>';
    $content .= '<input name="experience" placeholder="Опыт работы" value="' . ($existing_tutor ? htmlspecialchars($existing_tutor['experience'], ENT_QUOTES) : '') . '"><br>';
    $content .= '<input name="price" placeholder="Стоимость" value="' . $price_value . '"><br>';
    $content .= '<input name="duration" placeholder="Длительность занятия" value="' . ($existing_tutor ? htmlspecialchars($existing_tutor['duration'], ENT_QUOTES) : '') . '"><br>';
    $content .= '<input name="education" placeholder="Образование" value="' . ($existing_tutor ? htmlspecialchars($existing_tutor['education'], ENT_QUOTES) : '') . '"><br>';
    $content .= '<textarea name="about" placeholder="О себе">' . ($existing_tutor ? htmlspecialchars($existing_tutor['about'], ENT_QUOTES) : '') . '</textarea><br>';
    $content .= '<input name="phone" placeholder="Номер телефона" value="' . ($existing_tutor ? htmlspecialchars($existing_tutor['phone'], ENT_QUOTES) : '') . '"><br>';
    $content .= '<input name="location" placeholder="Город" value="' . ($existing_tutor ? htmlspecialchars($existing_tutor['location'], ENT_QUOTES) : '') . '"><br>';

    $content .= '<input type="submit">';
    $content .= '</form>';
}


return ['title' => 'Анкета репетитора', 'content' => $content];
