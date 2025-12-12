<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = \App\Models\User::count();
        $newsCount = \App\Models\News::count();
        $latestNews = \App\Models\News::where('is_published', true)->latest()->take(5)->get();

        return view('dashboard', compact('userCount', 'newsCount', 'latestNews'));
    }
}
