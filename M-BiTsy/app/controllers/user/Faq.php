<?php

class Faq
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 1);
    }

    // F.&.Q Default Page
    public function index()
    {
        // Get Rules
        $faq_categ = Faqs::select();
        
        // Init Data
        $data = [
            'title' => Lang::T("FAQ"),
            'faq_categ' => $faq_categ,
        ];
        
        // Load View
        View::render('faq/index', $data, 'user');
    }

}