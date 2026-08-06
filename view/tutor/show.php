<?php

$id = $params['id'];
$stmt = mysqli_prepare($link, "SELECT tutors.*, users.name, users.city FROM tutors
LEFT JOIN users ON tutors.user_id = users.id WHERE tutors.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tutor = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
$content = '';

 if (!empty($tutor)) {
        $content .= htmlspecialchars($tutor['name'], ENT_QUOTES) . '<br>';
        $content .= htmlspecialchars($tutor['city'], ENT_QUOTES) . '<br>';
        $content .= htmlspecialchars($tutor['subject'], ENT_QUOTES) . '<br>';
        $content .= htmlspecialchars($tutor['experience'], ENT_QUOTES) . '<br>';
        $content .= htmlspecialchars($tutor['price'], ENT_QUOTES) . '<br>';
        $content .= htmlspecialchars($tutor['duration'], ENT_QUOTES) . '<br>';
        $content .= htmlspecialchars($tutor['education'], ENT_QUOTES) . '<br>';
        $content .= htmlspecialchars($tutor['about'], ENT_QUOTES) . '<br>';
        $content .= htmlspecialchars($tutor['phone'], ENT_QUOTES) . '<br>';
        $content .= htmlspecialchars($tutor['location'], ENT_QUOTES) . '<br>';
    } else {
        $content .= 'Пользователь не найден';
    }


return ['title' => 'Репетитор', 'content' => $content];
?>