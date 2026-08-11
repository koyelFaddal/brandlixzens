<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.index');
    }

    public function show(string $page): View
    {
        $section = request()->route('section');
        $view = 'pages.'.$section.'.'.str_replace('-', '_', $page);

        abort_unless(view()->exists($view), 404);

        return view($view);
    }

    public function navbarMobile(): View
    {
        return view('partials.navbar-mobile');
    }

    public function navbarDesktop(): View
    {
        return view('partials.navbar-desktop');
    }

    public function footer(): View
    {
        return view('partials.footer');
    }
}
