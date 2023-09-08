<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CrawlController extends Controller
{
    public function search(): View {
        return view('search');
    }

    public function results(): View {
        return view('results');
    }
}
