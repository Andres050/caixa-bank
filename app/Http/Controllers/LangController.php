<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;

class LangController extends Controller
{
    /**
     * Handles the locale setting for the application.
     *
     * Checks if the provided locale is supported and updates the application locale
     * and session with the selected language. Redirects the user back to the previous page.
     *
     * @param string $locale The locale to be set (e.g., 'en', 'es').
     * @return RedirectResponse Redirects back to the previous page.
     */
    public function index(string $locale): RedirectResponse
    {
        if (in_array($locale, ['es', 'en'])) {
            session(['locale' => $locale]);
            App::setLocale($locale);
        }
        return redirect()->back();
    }
}
