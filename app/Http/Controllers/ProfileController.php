<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        return view($user->isAdmin() ? 'admin.profile' : 'profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => [$user->isAdmin() ? 'nullable' : 'required', 'string', 'max:20'],
            'address' => 'nullable|string|max:500',
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (! empty($data['password'])) {
            if (! Hash::check($data['current_password'] ?? '', $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'Kata sandi saat ini salah.'])
                    ->withInput($request->except('current_password', 'password', 'password_confirmation'));
            }
            $user->password = $data['password']; // di-hash otomatis oleh cast model
        }

        $user->fill(Arr::only($data, ['name', 'email', 'phone', 'address']))->save();

        return back()->with('success', 'Profil diperbarui.');
    }
}
