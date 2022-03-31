<?php
class Group
{

    public function __construct()
    {
        Auth::user(0, 2);
    }

    public function index()
    {
        Redirect::to(URLROOT);
    }

    public function members()
    {
        if (Users::get("view_users") == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }

        $search = Input::get('search');
        $class = (int) Input::get('class');
        $letter = Input::get('letter');

        $data = Groups::search($search, $class, $letter);

        $count = DB::run("SELECT COUNT(*) FROM users WHERE " . $data['query'])->fetchcolumn();
        list($pagerbuttons, $limit) = Pagination::pager(25, $count, URLROOT . "/group/members?$data[q]&");
        $results = Groups::getGroupsearch($data['query'], $limit);

        $res = DB::run("SELECT group_id, level FROM `groups` ORDER BY group_id ASC");

        $data = [
            'title' => 'Members',
            'getgroups' => $res,
            'results' => $results,
            'pagerbuttons' => $pagerbuttons,
        ];
        View::render('group/members', $data, 'user');
    }

    public function staff()
    {
        $dt = TimeDate::get_date_time(TimeDate::gmtime() - 180);
        $res = Groups::getStaff();
        $col = [];
        $table = [];
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $table[$row['class']] = ($table[$row['class']] ?? '') .
            "<td>
            <i class='fa fa-user' aria-hidden='true' style='" . ($row["last_access"] > $dt ? "color:" : "color:red") . "' title='Profile'></i> " .
            "<a href='" . URLROOT . "/profile?id=" . $row["id"] . "'>" . Users::coloredname($row["id"]) . "</a> " .
                "<a href='" . URLROOT . "/message/create?id=" . $row["id"] . "'><i class='fa fa-comment' title='Send PM'></i></a></td>";
            $col[$row['class']] = ($col[$row['class']] ?? 0) + 1;
            if ($col[$row["class"]] <= 4) {
                $table[$row["class"]] = $table[$row["class"]] . "<td></td>";
            } else {
                $table[$row["class"]] = $table[$row["class"]] . "</tr><tr>";
                $col[$row["class"]] = 2;
            }
        }

        $where = null;
        if (Users::get("edit_users") == "no") {
            $where = "AND `staff_public` = 'yes'";
        }

        $res = Groups::getStaffLevel($where);
        if ($res->rowCount() == 0) {
            Redirect::autolink(URLROOT, Lang::T("NO_STAFF_HERE"));
        }

        $data = [
            'title' =>  Lang::T("STAFF"),
            'sql' => $res,
            'table' => $table,
        ];
        View::render('group/staff', $data, 'user');
    }

}