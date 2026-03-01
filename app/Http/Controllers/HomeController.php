<?php

namespace App\Http\Controllers;

use App\Models\Catalog\Item;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $items = Item::with('coverImage')->where('validation', true)->inRandomOrder()->take(5)->get();

        return view('home', compact('items'));
    }
}
