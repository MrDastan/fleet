<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_no', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'employee_no' => 'nullable|string',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'phone' => 'nullable|string',
            'role' => 'required|exists:roles,name',
        ]);

        $initials = collect(explode(' ', $validated['name']))
            ->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))
            ->take(2)->join('');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'employee_no' => $validated['employee_no'],
            'department' => $validated['department'],
            'position' => $validated['position'],
            'phone' => $validated['phone'],
            'avatar_initials' => $initials,
            'pin' => '123456',
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', 'Pengguna baharu ditambah.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'phone' => 'nullable|string',
            'employee_no' => 'nullable|string',
            'role' => 'required|exists:roles,name',
            'is_active' => 'nullable|boolean',
            'password' => 'nullable|string|min:6',
        ]);

        $data = collect($validated)->except(['role', 'password'])->toArray();
        $data['is_active'] = $request->has('is_active');

        $initials = collect(explode(' ', $validated['name']))
            ->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))
            ->take(2)->join('');
        $data['avatar_initials'] = $initials;

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', 'Pengguna dikemaskini.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Tidak boleh padam akaun sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna dipadam.');
    }
}
