<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AccountController extends Controller
{
    /** Show the signed-in user's own account settings (profile + password + security). */
    public function edit(): View
    {
        return view('account.edit', ['user' => auth()->user()]);
    }

    /** Update the signed-in user's own name and email. */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        // Re-verification if the email actually changed.
        if ($user->email !== $data['email']) {
            $user->email_verified_at = null;
        }

        $user->fill($data)->save();

        return back()->with('success', 'Your profile has been updated.');
    }

    /** Change the signed-in user's own password (requires the current password). */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.current_password' => 'The current password you entered is incorrect.',
        ]);

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Your password has been changed.');
    }
}
