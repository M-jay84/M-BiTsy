<?php
foreach  ($data['res'] as $arr) {
    $title = ($arr['title']) ? $arr['title'] : $arr['name'];
    $comentario = stripslashes(format_comment($arr['text']));

    $type = 'Torrent: <a href="'.URLROOT.'/torrent?id=' . $arr['torrent'] . '">' . $title . '</a>&nbsp;
             Posted in <b>' . $arr['added'] . '</b> by <a href=\"' . URLROOT . '/profile?id=' . $arr['user'] . '\">' . Users::coloredname($arr['user']) . '</a><!--  [ <a href=\"edit-/comment?cid=' . $arr['id'] . '\">edit</a> | <a href=\"edit-/comment?action=delete&amp;cid=' . $arr['id'] . '\">delete</a> ] -->';
    if ($arr['news'] > 0) {
        $type = 'News: <a href="'.URLROOT.'/comment?id=' . $arr['torrent'] . '&amp;type=news">' . $title . '</a>&nbsp;
        Posted in <b>' . $arr['added'] . '</b> by <a href=\"' . URLROOT . '/profile?id=' . $arr['user'] . '\">' . Users::coloredname($arr['user']) . '</a><!--  [ <a href=\"edit-/comment?cid=' . $arr['id'] . '\">edit</a> | <a href=\"edit-/comment?action=delete&amp;cid=' . $arr['id'] . '\">delete</a> ] -->';
    }

    echo "<div class='table-responsive'>
    <table class='table'><thead>
    <th>" . $type . "</th></tr>
    <tr><td>" . $comentario . "</td></tr>
    </table>
    </div>";
}