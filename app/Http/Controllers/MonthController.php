<?php

namespace App\Http\Controllers;

class MonthController extends Controller
{
    public function index($month): \Illuminate\Http\JsonResponse
    {
        session('month', date('m-Y'));

        if ($month) {
            session(['month' => date('m-Y', strtotime($month))]);
            return response()->json('ok');
        }

        return response()->json('default');
    }
}
