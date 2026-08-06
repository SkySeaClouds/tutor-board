<?php

$flash_message = '';
if (isset($_SESSION['flash'])) {
    $flash_message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}




$query = "SELECT tutors.*, users.name FROM tutors 
LEFT JOIN users ON tutors.user_id = users.id";
$res = mysqli_query($link, $query);
if (!$res) {
    die('Ошибка SQL: ' . mysqli_error($link));
}
$content = '';

if (($flash_message)) {
    $content .= $flash_message;
}
while ($row = mysqli_fetch_assoc($res)) {
    $content .= '<a href="/tutor/' . $row['id'] . '">' . htmlspecialchars($row['name'], ENT_QUOTES) . '</a> - '  . htmlspecialchars($row['subject'], ENT_QUOTES) . '-' . htmlspecialchars($row['experience'], ENT_QUOTES) . '-' .
    htmlspecialchars($row['phone'], ENT_QUOTES) . '-' . htmlspecialchars($row['location'], ENT_QUOTES) . '-' . htmlspecialchars($row['price'], ENT_QUOTES) . 'руб' . '-' .
    htmlspecialchars($row['duration'], ENT_QUOTES) . '-' . htmlspecialchars($row['education'], ENT_QUOTES) . '-' . htmlspecialchars($row['about'], ENT_QUOTES) . '<br>';
} 

return [
'title' => 'Репетиторы',
'content' => $content,

];

?>