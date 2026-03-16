<?php

namespace App\Http\Controllers;

use App\Services\Catalog\ItemService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(ItemService $itemService): View
    {
        $items = $itemService->getRandomValidatedItemsForHome();

        return view('home', compact('items'));
    }
}
