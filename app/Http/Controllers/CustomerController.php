<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::latest()->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = Customer::create($request->validated());
        ActivityLogService::log(Auth::id(), 'create', 'customers', 'Created customer #'.$customer->id, $request);
        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Quick-store a customer via AJAX from the Add Sale modal.
     * Returns JSON: { id, customer_name } on success.
     */
    public function quickStore(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'customer_name'    => ['required', 'string', 'max:255'],
            'customer_contact' => ['nullable', 'string', 'max:50'],
            'customer_address' => ['required', 'string'],
        ]);

        $customer = Customer::create($validated);
        ActivityLogService::log(Auth::id(), 'create', 'customers', 'Quick-created customer #'.$customer->id.' from Sales form', $request);

        return response()->json([
            'success'       => true,
            'id'            => $customer->id,
            'customer_name' => $customer->customer_name,
        ]);
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());
        ActivityLogService::log(Auth::id(), 'update', 'customers', 'Updated customer #'.$customer->id, $request);
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Request $request, Customer $customer): RedirectResponse
    {
        if (!Auth::user()->isOwner()) {
            abort(403, 'Unauthorized action.');
        }
        $id = $customer->id;
        $customer->delete();
        ActivityLogService::log(Auth::id(), 'delete', 'customers', 'Deleted customer #'.$id, $request);
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
