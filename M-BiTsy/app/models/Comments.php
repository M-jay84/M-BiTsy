<?php
class Comments
{

    public static function commentPager($id, $type)
    {
        $commcount = DB::column('comments', 'COUNT(*)', [$type=>$id]);
        if ($commcount) {
            list($pagerbuttons, $limit) = Pagination::pager(10, $commcount, "comments?id=$id&amp;type=$type");
            $commres = DB::run("SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE $type = $id ORDER BY comments.id $limit");
        } else {
            unset($commres);
        }
        return $pager = [
            'commres' => $commres,
            'pagerbuttons' => $pagerbuttons,
            'limit' => $limit,
            'commcount' => $commcount,
        ];
    }

    public static function join($id, $limit)
    {
        $row = DB::run("SELECT comments.id, comments.type, comments.news, comments.torrent, text, user, comments.added, avatar,
                               signature, username, users.title, class, uploaded, downloaded, privacy, donated,
                               news.title as newstitle,
                               torrents.name as torrenttitle
                        FROM comments

                        LEFT JOIN users
                        ON comments.user = users.id

                        LEFT JOIN news
                        ON comments.news = news.id

                        LEFT JOIN torrents
                        ON comments.torrent = torrents.id

                        WHERE user = $id ORDER BY comments.id $limit")->fetchAll();
        return $row;
    }

    public static function graball($limit)
    {
        $res = DB::run("SELECT c.id, c.text, c.user, c.torrent, c.news, t.name, n.title, u.username, c.added 
        FROM comments c 
        LEFT JOIN torrents t ON c.torrent = t.id 
        LEFT JOIN news n ON c.news = n.id 
        LEFT JOIN users u ON c.user = u.id 
        ORDER BY c.added DESC $limit")->fetchAll(PDO::FETCH_OBJ);
        return $res;
    }
}