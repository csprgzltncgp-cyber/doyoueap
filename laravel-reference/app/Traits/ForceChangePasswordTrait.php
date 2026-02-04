<?php

namespace App\Traits;

use App\Models\OldPassword;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

trait ForceChangePasswordTrait
{
    public function force_change_password()
    {
        if (Auth::user()->type == 'client') {
            return view('client.force-change-password');
        }

        return view('auth.force-change-password');
    }

    public function force_change_password_process()
    {
        $user = Auth::user();

        if (request()->input('password') != request()->input('password_confirmation')) {
            return redirect()->back()->withErrors(['password_mismatch' => __('common.password-incorrect')]);
        }

        // Contain at least one uppercase/lowercase letters, one number and one special character
        // Must be at least 8 characters long
        request()->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/'],
        ]);

        // Cant be the same as the previous 10 password
        if ($this->is_password_in_history($user, request()->input('password'))) {
            return redirect()->back()->withErrors(['old_password' => __('common.old_password_validation_error')]);
        }

        $user->password = bcrypt(request()->input('password'));
        $user->password_changed_at = now();
        $user->save();

        // Store new password in history
        $this->store_password_history($user);
        // Redirect client user after doing a manual password change (NOT forced password change!)
        if (Auth::user()->type == 'client' && request()->route()->uri() == 'client/new-password') {
            return redirect()->back()->with('success', __('common.edit-successful'));
        }

        if (Auth::user()->type == 'client') {
            // Redirect client user on force change
            return redirect()->route('client.customer_satisfaction');
        }

        // Redirect expert user after doing a manual password change (NOT forced password change!)
        if (Auth::user()->type == 'expert' && request()->route()->uri() == 'expert/password-change') {
            return redirect()->back()->with('status', 0);
        }

        return redirect()->route($user->type.'.dashboard');
    }

    private function is_password_in_history(User $user, string $password): bool
    {
        $user->load('old_passwords');
        foreach ($user->old_passwords->pluck('password')->toArray() as $previous_password) {
            if (Hash::check($password, $previous_password)) {
                return true;
            }
        }

        return false;
    }

    public function store_password_history(User $user): void
    {
        // If the user's stored password count reaches 10, delete the first record
        if ($user->old_passwords->count() >= 10) {
            $user->old_passwords->first()->delete();
        }

        OldPassword::query()->create([
            'user_id' => $user->id,
            'password' => $user->password,
        ]);
    }
}
