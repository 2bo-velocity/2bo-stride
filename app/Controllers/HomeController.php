<?php

namespace App\Controllers;

use Stride\Core\Http\Request;
use Stride\Core\Http\Response;

class HomeController
{
    public function index(Request $request): Response
    {
        return new Response(view('pages/home', [
            'title' => 'Stride Home',
            'message' => 'Welcome to 2bo Stride Framework!',
        ]));
    }
}
