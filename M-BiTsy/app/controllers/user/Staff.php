<?php

class Staff
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Staff Default Page
    public function index()
    {
        $dt = TimeDate::get_date_time(TimeDate::gmtime() - 180);

        // Get Staff Data
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

        // Init Data
        $data = [
            'title' =>  Lang::T("STAFF"),
            'sql' => $res,
            'table' => $table,
        ];

        // Load Data
        View::render('staff/index', $data, 'user');
    }

}