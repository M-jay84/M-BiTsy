<?php

class Ratings
{

    public static function ratingtor($id, $rating = 0)
    {
        if (!$rating) {
            $rating = "<b>" . Lang::T("RATINGS") . ":</b> <b>Not Yet Rated</b><br>";
        } else {
            $average = ceil($rating);
            $rating = "<img src=\"" . URLROOT . "/assets/images/rating/$average.png\" border=\"0\" alt=\"rating: $average/5\" title=\"rating: $average/5\" /></a><br>";
        }
        if (Users::get('id')) {
            $ratings = [
                5 => Lang::T("COOL"),
                4 => Lang::T("PRETTY_GOOD"),
                3 => Lang::T("DECENT"),
                2 => Lang::T("PRETTY_BAD"),
                1 => Lang::T("SUCKS"),
            ];
            $res = DB::run("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . Users::get("id"));
            $row = $res->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $rating .= "<i>(" . Lang::T("YOU_RATED") . " \"" . $row["rating"] . " - " . $ratings[$row["rating"]] . "\")</i>";
            } else {
                $rating .= "<form style=\"display:inline;\" method=\"post\" action=\"" . URLROOT . "/rating?id=$id\"><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
                $rating .= "<select name=\"rating\">\n";
                $rating .= "<option value=\"0\">(" . Lang::T("ADD_RATING") . ")</option>\n";
                foreach ($ratings as $k => $v) {
                    $rating .= "<option value=\"$k\">$k - $v</option>\n";
                }
                $rating .= "</select>\n";
                $rating .= "<input type=\"submit\" value=\"" . Lang::T("VOTE") . "\" />";
                $rating .= "</form>\n";
            }
        }

        return $rating;
    }

    public static function addRating($id, $rating)
    {
        $res = DB::insert('ratings', ['torrent'=>$id, 'user'=>Users::get("id") , 'rating'=>$rating, 'added'=>TimeDate::get_date_time()]);
        if ($res) {
            if ($res->errorCode() == 1062) {
                Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("YOU_ALREADY_RATED_TORRENT"));
            } else {
                Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("A_UNKNOWN_ERROR_CONTACT_STAFF"));
            }
        }
        
        DB::run("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
    }
    
}