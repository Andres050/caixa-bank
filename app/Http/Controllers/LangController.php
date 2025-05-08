<?php

namespace App\Http\Controllers;

class LangController extends Controller
{
    public function index($locale): \Illuminate\Http\RedirectResponse
    {
        if (in_array($locale, ['en', 'es'])) {
            session(['locale' => $locale]);
            \Illuminate\Support\Facades\App::setLocale($locale);
        }
        return redirect()->back();
    }
}
