<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Catalog\ItemService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(ItemService $itemService): View
    {
        $items = $itemService->getRandomValidatedItemsForHome();

        return view('pages.home.index', compact('items'));
    }
}
