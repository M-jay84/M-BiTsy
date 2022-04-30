<?php

class Bonus
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Bonus Default Page
    public function index()
    {
        // Get All Bonus Options
        $bonus = DB::raw('bonus', '*', '', 'ORDER BY type');

        // Init Data
        $data = [
            'title' => Lang::T('Seed Bonus'),
            'bonus' => $bonus,
            'autoclean_interval' => floor(Config::get('ADDBONUS') / 60)
        ];

        // Load View
        View::render('bonus/index', $data, 'user');
    }

    // Bonus Form Submit
    public function submit()
    {
        // Check User Input
        $id = (int) Input::get("id");

        // Check Input Else Return To Form
        if (Input::exist() && Validate::Id($id)) {

            // Get/Set Bonus
            $row = DB::select('bonus', 'type, value, cost', ['id' =>$id]);
            if (!$row || Users::get('seedbonus') < $row['cost']) {
                Redirect::autolink(URLROOT."/bonus", "Demand not valid.");
            }
            DB::run("UPDATE `users` SET `seedbonus` = `seedbonus` - '$row[cost]' WHERE `id` = ".Users::get('id')."");
            Bonuses::switch($row);
            Redirect::autolink(URLROOT."/bonus", "Your account has been credited.");

        } else {
            Redirect::to(URLROOT."/bonus");
        }

    }

}