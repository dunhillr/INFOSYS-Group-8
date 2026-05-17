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
