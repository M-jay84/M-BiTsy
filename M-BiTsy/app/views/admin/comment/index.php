<?php
foreach  ($data['res'] as $arr) {
    $userid = $arr->user;
    $username = Users::coloredname($arr->user);
    $data = $arr->added;
    $tid = $arr->torrent;
    $nid = $arr->news;
    $title = ($arr->title) ? $arr->title : $arr->name;
    $comentario = stripslashes(format_comment($arr->text));
    $cid = $arr->id;

    $type = 'Torrent: <a href="'.URLROOT.'/torrent?id=' . $tid . '">' . $title . '</a>&nbsp;
             Posted in <b>' . $data . '</b> by <a href=\"' . URLROOT . '/profile?id=' . $userid . '\">' . $username . '</a><!--  [ <a href=\"edit-/comment?cid=' . $cid . '\">edit</a> | <a href=\"edit-/comment?action=delete&amp;cid=' . $cid . '\">delete</a> ] -->';
    if ($nid > 0) {
        $type = 'News: <a href="'.URLROOT.'/comment?id=' . $nid . '&amp;type=news">' . $title . '</a>&nbsp;
        Posted in <b>' . $data . '</b> by <a href=\"' . URLROOT . '/profile?id=' . $userid . '\">' . $username . '</a><!--  [ <a href=\"edit-/comment?cid=' . $cid . '\">edit</a> | <a href=\"edit-/comment?action=delete&amp;cid=' . $cid . '\">delete</a> ] -->';
    }

    echo "<div class='table-responsive'>
    <table class='table'><thead>
    <th>" . $type . "</th></tr>
    <tr><td>" . $comentario . "</td></tr>
    </table>
    </div>";
}