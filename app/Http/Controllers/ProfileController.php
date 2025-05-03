<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Bank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function reorder(Request $request)
    {
        foreach ($request->ids as $index => $id) {
            \App\Models\Account::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function bankUpdate(): RedirectResponse
    {
        $user = Auth::user();

        $bank = Bank::where('user_id', $user->id)->first();
        if ($bank) {
            $bank->institution_id = request('institution');
            $bank->save();
        } else {
            $banknew = new Bank();
            $banknew->user_id = $user->id;
            $banknew->institution_id = request('institution');
            $banknew->save();
        }

        return Redirect::route('profile.edit')->with('status', 'bank-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
