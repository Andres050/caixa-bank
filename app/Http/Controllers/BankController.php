<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Models\ScheduledTasks;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class BankController extends Controller
{
    /**
     * Display the bank edit form.
     *
     * @return View
     *
     */
    public function edit(): View
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        return view('pages.profile.bank', [
            'user' => $user,
            'bank' => $user->bank,
        ]);
    }

    /**
     * Update the user's bank information.
     *
     * @return RedirectResponse
     */
    public function update(): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return Redirect::route('login');
        }

        $validated = request()->validate([
            'institution' => ['required', 'exists:institutions,id'],
        ]);

        try {
            Bank::updateOrCreate(
                ['user_id' => $user->id],
                ['institution_id' => $validated['institution']]
            );

            return Redirect::route('profile.bank.edit')
                ->with('status', __('status.bankcontroller.update-account-success'));
        } catch (Exception $e) {
            Log::error('Error actualizando banco del usuario: ' . $e->getMessage());

            return Redirect::route('profile.bank.edit')
                ->with('error', __('status.bankcontroller.update-account-failed'));
        }
    }

    /**
     * Schedule tasks for the authenticated user.
     *
     * Validates the incoming request data, updates the user's schedule configuration,
     * and manages the scheduling tasks. Rolls back changes and logs an error
     * if an exception occurs during the process.
     *
     * @param Request $request The incoming HTTP request containing the scheduling data.
     * @return RedirectResponse A redirection to the profile edit page with a status message.
     *
     * @throws Exception|Throwable If an error occurs during the scheduling process.
     */
    public function schedule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_times' => ['required', 'integer', 'max:' . ScheduledTasks::$MAX_TIMES],
            'times.*' => ['nullable', 'date_format:H:i'],
            'execute_login' => ['nullable', 'string']
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            $user->update([
                'schedule_times' => $validated['schedule_times'],
                'execute_login' => ($validated['execute_login'] ?? '') === 'on' ? 1 : 0,
            ]);

            ScheduledTasks::where('user_id', $user->id)->delete();

            if (!empty($validated['times'])) {
                $tasksToCreate = collect(array_slice($validated['times'], 0, $validated['schedule_times']))
                    ->filter(fn($time) => !is_null($time))
                    ->map(fn($time) => [
                        'hour' => $time,
                        'user_id' => $user->id,
                        'created_at' => now()
                    ])
                    ->values()
                    ->toArray();

                if (!empty($tasksToCreate)) {
                    ScheduledTasks::insert($tasksToCreate);
                }
            }

            DB::commit();
            return redirect()->route('profile.bank.edit')->with('status', __('status.bankcontroller.schedule-updated'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al programar tareas: ' . $e->getMessage());
            return redirect()->route('profile.bank.edit')->with('status', __('status.bankcontroller.schedule-error'));
        }
    }

    /**
     * Execute scheduled tasks.
     *
     * This method checks the user's authentication status and attempts to trigger
     * the Laravel schedule commands asynchronously. Logs an error if an exception
     * occurs during execution.
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function scheduleTasks(): JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'No autorizado'], 401);
            }

            dispatch(function () {
                Artisan::call('schedule:run');
            })->afterResponse();

            return response()->json(['status' => 'executed']);
        } catch (Exception $e) {
            Log::error('Error ejecutando tareas programadas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al ejecutar tareas programadas'], 500);
        }
    }
}
