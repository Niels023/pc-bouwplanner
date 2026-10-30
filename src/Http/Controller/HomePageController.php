<?php

namespace App\Http\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Http\View;

class HomePageController
{
    public function __construct(private View $view)
    {
    }

    public function show(Request $request): Response
    {
        return $this->view->render('home', ['title' => 'Stel je eigen PC samen']);
    }
}
