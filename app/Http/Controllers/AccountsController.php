<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AccountsController extends Controller
{
    /**
     * Display the account's page.
     *
     * @return View The view for the account's page.
     */
    public function edit(): View
    {
        if (!auth()->check()) {
            abort(403);
        }

        try {
            $user = Auth::user();
            $accounts = Account::where('user_id', $user->id)
                ->orderBy('order')
                ->get();

            return view('pages.profile.accounts', compact('user', 'accounts'));
        } catch (Exception $e) {
            Log::error('Error al cargar cuentas del usuario: ' . $e->getMessage());
            abort(500);
        }
    }

    /**
     * Create a new account and redirect to the account edit page.
     *
     * @param Request $request The incoming HTTP request containing the account details.
     * @return RedirectResponse Redirects to the account edit view with a success status.
     */
    public function create(Request $request): RedirectResponse
    {
        $request->validate([
            'owner_name' => ['required', 'string', 'max:255'],
            'bban' => ['nullable', 'string', 'max:255'],
            'iban' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
        ]);

        try {
            $user = Auth::user();

            Account::create([
                'id' => Str::uuid()->toString(),
                'iban' => $request->input('iban'),
                'bban' => $request->input('bban'),
                'status' => $request->input('status', 'active'),
                'owner_name' => $request->input('owner_name'),
                'institution_id' => $user->bank->institution_id,
                'user_id' => $user->id,
                'type' => Account::$accountTypes['manual']
            ]);

            return redirect()->route('profile.accounts.edit')
                ->with('status', __('status.accountscontroller.create-account-success'));
        } catch (Exception $e) {
            Log::error('Error al crear la cuenta: ' . $e->getMessage());
            return redirect()->route('profile.accounts.edit')
                ->with('error', __('status.accountscontroller.create-account-failed'));
        }
    }

    /**
     * Update the account details for the authenticated user.
     *
     * @param Request $request The incoming HTTP request containing the updated account details.
     * @return RedirectResponse Redirects to the account edit view with a success status.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'exists:accounts,id'],
            'owner_name' => ['required', 'string', 'max:255'],
            'bban' => ['nullable', 'string', 'max:255'],
            'iban' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $account = Account::where('user_id', Auth::id())
                ->where('id', $validated['id'])
                ->firstOrFail();

            $account->update([
                'owner_name' => $validated['owner_name'],
                'bban' => $validated['bban'],
                'iban' => $validated['iban'],
                'status' => $validated['status'],
            ]);

            return Redirect::route('profile.accounts.edit')
                ->with('status', __('status.accountscontroller.update-account-success'));
        } catch (ModelNotFoundException $e) {
            Log::error('Error al actualizar la cuenta: ' . $e->getMessage());
            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.accountscontroller.update-account-not-found'));
        } catch (Exception $e) {
            Log::error('Error al actualizar la cuenta: ' . $e->getMessage());
            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.accountscontroller.update-account-failed'));
        }
    }

    /**
     * Handle the deletion of a user account.
     *
     * @param Request $request The incoming HTTP request.
     * @param string $id The ID of the account to be deleted.
     * @return RedirectResponse A redirect response to the account's edit page with a status message.
     * @throws Throwable
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        try {
            $user = Auth::user();
            $account = Account::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            DB::beginTransaction();
            try {
                $account->delete();
                DB::commit();
                return Redirect::route('profile.accounts.edit')
                    ->with('status', __('status.accountscontroller.destroy-account-success'));
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ModelNotFoundException $e) {
            Log::error('Error al eliminar la cuenta: ' . $e->getMessage());
            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.accountscontroller.destroy-account-not-found'));
        } catch (Exception $e) {
            Log::error('Error al eliminar la cuenta: ' . $e->getMessage());
            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.accountscontroller.destroy-account-failed'));
        }
    }

    /**
     * Reorder the accounts based on the provided IDs.
     *
     * @param Request $request The incoming HTTP request containing the ordered IDs.
     * @return JsonResponse A JSON response indicating the status of the operation.
     * @throws Throwable
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['ids'] as $index => $id) {
                Account::where('id', $id)
                    ->update(['order' => $index]);
            }

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            Log::error('Error al reordenar cuentas: ' . $e->getMessage());
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo reordenar los elementos'
            ], 500);
        }
    }
}
