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
<<<<<<< HEAD
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:25|unique:customers,phone',
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
        ]);
        Customer::create($validated);
=======
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:customers,phone',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
        ]);
        Customer::create($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.customers.index')->with('success', 'Customer saved');
    }

    public function edit(Customer $customer) {
        $item = $customer;
        return view('admin.customers.form', compact('item'));
    }

    public function update(Request $request, Customer $customer) {
<<<<<<< HEAD
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:25|unique:customers,phone,' . $customer->id,
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
        ]);
        $customer->update($validated);
=======
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:customers,phone,' . $customer->id,
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
        ]);
        $customer->update($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.customers.index')->with('success', 'Customer updated');
    }

    public function destroy(Customer $customer) {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted');
    }
}