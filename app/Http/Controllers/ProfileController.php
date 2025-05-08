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
            'user' => auth()->user(),
            'bank' => auth()->user()->bank,
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

    public function schedule(Request $request)
    {
        if ($request->schedule_times && $request->schedule_times > \App\Models\ScheduledTasks::$MAX_TIMES) {
            return Redirect::route('profile.edit')->with('status', 'schedule-error');
        }

        $user = Auth::user();
        $user->schedule_times = $request->schedule_times;
        $user->execute_login = $request->execute_login == 'on' ? 1 : 0;
        $user->save();

        $scheduledTasks = \App\Models\ScheduledTasks::where('user_id', $user->id)->truncate();

        if ($request->times) {
            foreach ($request->times as $index => $time) {
                if ($index >= $request->schedule_times) {
                    break;
                }
                $scheduledTask = new \App\Models\ScheduledTasks();
                $scheduledTask->hour = $time;
                $scheduledTask->user_id = $user->id;
                $scheduledTask->save();
            }
        }

        return Redirect::route('profile.edit')->with('status', 'schedule-updated');
    }

    public function scheduleTasks(Request $request)
    {
        \Artisan::call('schedule:run');
        return response()->json(['status' => 'excuted']);
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
