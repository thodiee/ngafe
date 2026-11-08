<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index() {
        $data = Customer::orderBy('name')->get();
        return view('admin.customers.index', compact('data'));
    }

    public function create() {
        return view('admin.customers.form');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:25|unique:customers,phone',
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
        ]);
        Customer::create($validated);
        return redirect()->route('admin.customers.index')->with('success', 'Customer saved');
    }

    public function edit(Customer $customer) {
        $item = $customer;
        return view('admin.customers.form', compact('item'));
    }

    public function update(Request $request, Customer $customer) {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:25|unique:customers,phone,' . $customer->id,
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
        ]);
        $customer->update($validated);
        return redirect()->route('admin.customers.index')->with('success', 'Customer updated');
    }

    public function destroy(Customer $customer) {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted');
    }
}