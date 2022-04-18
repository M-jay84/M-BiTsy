<?php
class Forums
{

    public static function getIndex()
    {
        $stmt = DB::run("
              SELECT forumcats.id
              AS fcid, forumcats.name AS fcname, forum_forums.*
              FROM forum_forums
              LEFT JOIN forumcats
              ON forumcats.id = forum_forums.category WHERE sub = 0
              ORDER BY forumcats.sort, forum_forums.sort, forum_forums.name");
        return $stmt;
    }

    public static function getsub()
    {
        $stmt = DB::run("
              SELECT forumcats.id
              AS fcid, forumcats.name AS fcname, forum_forums.*
              FROM forum_forums
              LEFT JOIN forumcats
              ON forumcats.id = forum_forums.category WHERE sub != 0
              ORDER BY forumcats.sort, forum_forums.sort, forum_forums.name");
        return $stmt;
    }

    public static function gettopicpages($topicid, $count = 0)
    {
        $count = DB::column('forum_posts', 'COUNT(*)', ['topicid'=>$topicid]);
        list($pagerbuttons, $limit) = Pagination::pager(15, $count, URLROOT . "/topic?topicid=$topicid&amp;");
        $query = "SELECT forum_posts.id,
                        forum_posts.topicid,
                        forum_posts.userid,
                        forum_posts.added,
                        forum_posts.body,
                        forum_posts.editedby,
                        forum_posts.editedat,
                        forum_posts.attachments,
                        thanks.user,
                        thanks.thanked,
                        thanks.type
                FROM forum_posts
                LEFT JOIN thanks ON forum_posts.id = thanks.thanked
                WHERE forum_posts.topicid = $topicid
                GROUP BY forum_posts.id 
                ORDER BY forum_posts.id $limit";
        $res = DB::run($query);
        return array('res'=>$res,'pagerbuttons'=>$pagerbuttons);
    }

    public static function deltopic($topicid)
    {
        DB::delete('forum_topics', ['id'=>$topicid]);
        DB::delete('forum_posts', ['topicid'=>$topicid]);
        DB::delete('forum_readposts', ['topicid'=>$topicid]);
        // delete attachment
        $sql = DB::raw('attachments', '*', ['topicid'=>$topicid]);
        if ($sql->rowCount() != 0) {
            foreach ($sql as $row7) {
                //print("<br>&nbsp;<b>$row7[filename]</b><br>");
                $daimage = UPLOADDIR . "/attachment/$row7[file_hash].data";
                if (file_exists($daimage)) {
                    if (unlink($daimage)) {
                        DB::delete('attachments', ['content_id'=>$row7['id']]);
                    }
                }
                $extension = substr($row7['filename'], -3);
                if ($extension != 'zip') {
                    $dathumb = "uploads/thumbnail/$row7[file_hash].jpg";
                    if (!unlink($dathumb)) {
                            Redirect::autolink(URLROOT . "/topic?topicid=$topicid", "Could not remove thumbnail = $row7[file_hash].jpg");
                    }
                }
            }
        }
    }

    public static function searchForum($keywords) {
        $stmt = DB::run("SELECT forum_posts.topicid, forum_posts.userid, forum_posts.id, forum_posts.added,
                 MATCH ( forum_posts.body ) AGAINST ( ? ) AS relevancy
                 FROM forum_posts
                 WHERE MATCH ( forum_posts.body ) AGAINST ( ? IN BOOLEAN MODE )
                 ORDER BY added DESC", ['%' . $keywords . '%', '%' . $keywords . '%']);
        return $stmt;
    }

    public static function search($keywords, $type) {
        $keys = explode(" ", $keywords);
        foreach ($keys as $k) {
            $searcha[] = " forum_topics.subject LIKE '%$k%' ";
            $searchb[] = " forum_posts.body LIKE '%$k%' ";
            $searchc[] = " forum_topics.subject LIKE '%$k%' ";
            $searchd[] = " forum_posts.body LIKE '%$k%' ";
        }
        $whereasearch = '(' . implode(' OR ', $searcha) . ')';
        $wherebsearch = '(' . implode(' OR ', $searchb) . ')';
        $wherecsearch = implode(' OR ', $searchc);
        $wheredsearch = implode(' OR ', $searchd);
        $wherea = $whereasearch;
        $whereb = $wherebsearch;
        $wherec = $wherecsearch;
        $whered = $wheredsearch;
        if ($type == 'deep') {
            $res2 = DB::run("SELECT *, ( $wherea + $whereb ) as count_words
                             FROM forum_posts
                             JOIN forum_topics
                             ON forum_posts.topicid=forum_topics.id
                             WHERE ($whered)
                             ORDER BY count_words,added DESC");
            $count = $res2->rowCount();
            list($pagerbuttons, $limit) = Pagination::pager(30, $count, URLROOT . "/forum/result?keywords=$keywords&type=deep");
            $res = DB::run("SELECT *, ( $wherea + $whereb ) as count_words
                            FROM forum_posts
                            JOIN forum_topics
                            ON forum_posts.topicid=forum_topics.id
                            WHERE ($whered)
                            ORDER BY count_words,added DESC");
        } else {
            $res2 = DB::run("SELECT distinct topicid, subject, forumid, forum_posts.added, forum_posts.userid, ( $wherea ) as count_words
                             FROM forum_topics
                             JOIN forum_posts
                             ON forum_posts.topicid=forum_topics.id
                             WHERE ($wherec)");
            $count = $res2->rowCount();
            list($pagerbuttons, $limit) = Pagination::pager(30, $count, URLROOT . "/forum/result?keywords=$keywords&");

            $res = DB::run("SELECT distinct topicid, subject, forumid, forum_posts.added, forum_posts.userid, ( $wherea ) as count_words
                            FROM forum_topics
                            JOIN forum_posts
                            ON forum_posts.topicid=forum_topics.id
                            WHERE ($wherec)
                            ORDER BY count_words,added DESC $limit");
        }

        $var = ['res'=>$res, 'pager'=>$pagerbuttons, 'count'=>$count];

        return $var;
    }

    // Get User Forum Post By Id
    public static function getUsersPost($id, $limit)
    {
        $stmt = DB::run("SELECT
                        forum_posts.id, 
                        forum_posts.topicid, 
                        forum_posts.userid, 
                        forum_posts.added, 
                        forum_posts.body,
                        
                        users.avatar, 
                        users.signature, 
                        users.username, 
                        users.title, 
                        users.class, 
                        users.uploaded, 
                        users.downloaded, 
                        users.privacy, 
                        users.donated,
                        
                        forum_topics.subject,
                        forum_topics.id as tid,
                        forum_topics.forumid,

                        forum_forums.name
                        
                        FROM forum_posts
                        
                        LEFT JOIN users
                        ON forum_posts.userid = users.id
                        
                        LEFT JOIN forum_topics
                        ON forum_posts.topicid = forum_topics.id
            
                        LEFT JOIN forum_forums
                        ON forum_topics.forumid = forum_forums.id
                        
                        WHERE forum_posts.userid = $id 
                        ORDER BY forum_posts.added 
                        DESC $limit")->fetchAll();
                        //echo '<pre>'.print_r($stmt, true).'</pre>';
        return $stmt;
    }

}