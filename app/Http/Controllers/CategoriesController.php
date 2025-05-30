<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryFilter;
use App\Models\Transaction;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoriesController extends Controller
{
    /**
     * Display the categories associated with the current user's account transactions.
     *
     * Fetches all categories, including their related transactions and filters, for the authenticated user.
     * Passes the authenticated user and categories data to the view.
     *
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application The rendered view for the categories page.
     */
    public function show(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        // Get all categories from accounts transactions of the current user
        $categories = auth()->user()->categories()
            ->with(['transactions', 'filters'])
            ->get();
        $user = auth()->user();

        return view('pages.profile.categories', compact('categories', 'user'));
    }

    /**
     * Handle the creation of a new category after validating the input.
     *
     * @param Request $request The incoming HTTP request containing form data.
     *
     * @return RedirectResponse Redirects to the categories page with a success message after creation.
     *
     * @throws ValidationException If the request validation fails.
     */
    public function create(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,user_id,' . auth()->id(),
        ]);

        auth()->user()->categories()->create([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('profile.categories')->with('success', __('status.categoriescontroller.create-category-success') );
    }

    /**
     * Update the specified category with new data after validating the input.
     *
     * @param Request $request The incoming HTTP request containing the updated category data.
     * @param int $id The ID of the category to be updated.
     *
     * @return RedirectResponse Redirects to the categories page with a success message.
     *
     * @throws ValidationException If the request validation fails.
     * @throws ModelNotFoundException If the category is not found for the authenticated user.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id . ',id,user_id,' . auth()->id(),
        ]);

        $category = auth()->user()->categories()->findOrFail($id);
        $category->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('profile.categories')->with('success', __('status.categoriescontroller.update-category-success') );
    }

    /**
     * Delete the specified category along with all its associated filters.
     *
     * @param int $id The ID of the category to be deleted.
     *
     * @return RedirectResponse Redirects to the categories page with a success message.
     *
     * @throws ModelNotFoundException If the category ID does not exist for the authenticated user.
     */
    public function destroy($id): RedirectResponse
    {
        $category = auth()->user()->categories()->findOrFail($id);

        // Delete all filters associated with the category
        $category->filters()->delete();

        // Delete the category itself
        $category->delete();

        return redirect()->route('profile.categories')->with('success', __('status.categoriescontroller.delete-category-success') );
    }

    /**
     * Create a new filter for a specified category after validating the input.
     *
     * @param Request $request The incoming HTTP request containing form data.
     *
     * @return RedirectResponse Redirects to the categories page with success or error messages.
     *
     * @throws ValidationException If the request validation fails.
     * @throws ModelNotFoundException If the specified category ID does not exist.
     */
    public function createFilter(Request $request): RedirectResponse
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'type' => 'required|in:exact,contains,starts_with,ends_with',
            'category_id' => 'required|exists:categories,id',
        ]);

        $category = Category::findOrFail($request->input('category_id'));
        if ($category->user_id !== auth()->id()) {
            return redirect()->route('profile.categories')->withErrors(__('status.categoriescontroller.unauthorized-action') );
        }

        $category->filters()->create([
            'value' => $request->input('value'),
            'type' => $request->input('type'),
            'enabled' => $request->input('enabled', false) == 'on',
        ]);

        return redirect()->route('profile.categories')->with('success', __('status.categoriescontroller.create-filter-success') );
    }

    /**
     * Update the specified filter with new data after validating the input.
     *
     * @param Request $request The incoming HTTP request containing form data.
     * @param int $id The ID of the filter to be updated.
     *
     * @return RedirectResponse Redirects to the categories page with success or error messages.
     *
     * @throws ValidationException If the request validation fails.
     * @throws ModelNotFoundException If the filter ID does not exist.
     */
    public function updateFilter(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'type' => 'required|in:exact,contains,starts_with,ends_with',
        ]);

        $filter = CategoryFilter::findOrFail($id);

        if ($filter->category->user_id !== auth()->id()) {
            return redirect()->route('profile.categories')->withErrors(__('status.categoriescontroller.unauthorized-action') );
        }

        $filter->update([
            'value' => $request->input('value'),
            'type' => $request->input('type'),
            'enabled' => $request->input('enabled', false) == 'on',
        ]);

        return redirect()->route('profile.categories')->with('success', __('status.categoriescontroller.update-filter-success') );
    }

    /**
     * Deletes a specific filter associated with the authenticated user.
     *
     * @param int|string $id The unique identifier of the filter to be deleted.
     * @return RedirectResponse Redirects to the profile categories route with a success message after deletion.
     */
    public function destroyFilter($id): RedirectResponse
    {
        $filter = auth()->user()->filters()->findOrFail($id);
        $filter->delete();

        return redirect()->route('profile.categories')->with('success', __('status.categoriescontroller.delete-filter-success') );
    }

    /**
     * Update the category of all transactions for the authenticated user's accounts.
     *
     * The method iterates through all accounts of the authenticated user and their transactions.
     * It attempts to determine a category for each transaction based on the remittance information,
     * and updates the transaction's category if it is found.
     *
     * @param Request $request The incoming HTTP request.
     *
     * @return RedirectResponse Redirects to the categories page with a success or informational message.
     *
     * @throws AuthenticationException If the user is not authenticated.
     */
    public function setAllCategoriesFilter(Request $request): RedirectResponse
    {
        $accounts = auth()->user()->accounts()->get();
        $updatedCount = 0;
        $totalTransactions = 0;

        foreach ($accounts as $account) {
            $transactions = $account->transactions()->get();
            $totalTransactions += $transactions->count();

            if ($transactions->isEmpty()) {
                continue;
            }

            foreach ($transactions as $transaction) {
                $categoryId = Transaction::getCategoryId($transaction->remittanceInformationUnstructured);

                if ($categoryId) {
                    $transaction->update(['category_id' => $categoryId]);
                    $updatedCount++;
                }
            }
        }

        if ($updatedCount > 0) {
            return redirect()->route('profile.categories')->with('success', __('status.categoriescontroller.update-transactions-categories-success', [
                'updatedCount' => $updatedCount,
                'totalTransactions' => $totalTransactions
            ]));
        }

        return redirect()->route('profile.categories')->with('info', __('status.categoriescontroller.no-transactions-categories') );
    }
}
