<?php
Style::begin(Lang::T("NEWS"));
$res = DB::run("SELECT news.id, news.title, news.added, news.body, users.username FROM news LEFT JOIN users ON news.userid = users.id ORDER BY added DESC LIMIT 10");
if ($res->rowCount() > 0) {
    print("<div class='container'><table class='table table-striped'><tr><td>\n<ul>");
    $news_flag = 0;
    while ($array = $res->fetch(PDO::FETCH_LAZY)) {
        if (!$array["username"]) {
            $array["username"] = Lang::T('UNKNOWN_USER');
        }
        $userid = DB::raw('users', 'id', ['username' => $array["username"]])->fetch();
        $numcomm = get_row_count("comments", "WHERE news='" . $array['id'] . "'");
        // Show first 2 items expanded
        if ($news_flag < 2) {
            $disp = "block";
            $pic = "minus";
        } else {
            $disp = "none";
            $pic = "plus";
        }
        print("<br /><a href=\"javascript: klappe_news('a" . $array['id'] . "')\"><img border=\"0\" src=\"" . URLROOT . "/assets/images/$pic.gif\" id=\"pica" . $array['id'] . "\" alt=\"Show/Hide\" />");
        print("&nbsp;<b>" . $array['title'] . "</b></a> - <b>" . Lang::T("POSTED") . ":</b> " . date("d-M-y", TimeDate::utc_to_tz_time($array['added'])) . " <b>" . Lang::T("BY") . ":</b><a href='" . URLROOT . "/profile?id=$userid[id]'>  " . Users::coloredname($userid['id']) . "</a>");
        print("<div id=\"ka" . $array['id'] . "\" style=\"display: $disp;\"> " . format_comment($array["body"]) . " <br /><br />" . Lang::T("COMMENTS") . " (<a href='" . URLROOT . "/comment?type=news&amp;id=" . $array['id'] . "'>" . number_format($numcomm) . "</a>)</div>");

        $news_flag++;
    }
    print("</ul></td></tr></table></div>\n");
} else {
    echo "<br /><b>" . Lang::T("NO_NEWS") . "</b>";
}
Style::end();
