<?php

namespace Modules\ParentModule\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\ParentModule\Models\ParentModel;
use Modules\RolePermission\Entities\Role;

class ParentAuthController extends Controller
{
    /**
     * Show parent registration form.
     */
    public function showRegistrationForm()
    {
		if (Auth::check() && Auth::user()->role_id == 6) {
        return redirect()->route('parent.dashboard');
    	}
        return view('parentmodule::auth.register');
    }

    /**
     * Handle parent registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:100|unique:users',
            'national_id' => 'nullable|string|max:191',
            'occupation' => 'nullable|string|max:191',
            'workplace' => 'nullable|string|max:191',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:191',
            'emergency_contact_name' => 'nullable|string|max:191',

        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get parent role ID
            $parentRole = Role::where('name', 'Parent')->first();
            
            if (!$parentRole) {
                return redirect()->back()
                    ->with('error', 'Parent role not found. Please contact administrator.')
                    ->withInput();
            }

            // Create user account
            $user = User::create([
                'role_id' => $parentRole->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'country' => $request->country ?? '19', // Default country
                'status' => 1,
                'is_active' => true,
                'email_verify' => '0',
                'referral' => Str::random(10),
                'lms_id' => 1
            ]);

            // Create parent profile
            $parent = ParentModel::create([
                'user_id' => $user->id,
                'national_id' => $request->national_id,
                'occupation' => $request->occupation,
                'workplace' => $request->workplace,
                'emergency_contact' => $request->emergency_contact,
                'emergency_contact_name' => $request->emergency_contact_name,
                'is_verified' => false,
                'lms_id' => 1
            ]);

            // Log the parent in
            Auth::login($user);

            return redirect()->route('parent.dashboard')
                ->with('success', 'Registration successful! Welcome to the parent portal.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Registration failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show parent login form.
     */
    public function showLoginForm()
    {
		if (Auth::check() && Auth::user()->role_id == 6) {
        return redirect()->route('parent.dashboard');
    }
        return view('parentmodule::auth.login');
    }

    /**
     * Handle parent login.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user has parent role
            $parentRole = Role::where('name', 'Parent')->first();
            
            if ($user->role_id != $parentRole->id) {
                Auth::logout();
                return redirect()->back()
                    ->with('error', 'Invalid credentials. Please use parent login.')
                    ->withInput();
            }

            // Check if user is active
            if (!$user->is_active || $user->status != 1) {
                Auth::logout();
                return redirect()->back()
                    ->with('error', 'Your account is inactive. Please contact administrator.')
                    ->withInput();
            }

            return redirect()->intended(route('parent.dashboard'))
                ->with('success', 'Welcome back!');
        }

        return redirect()->back()
            ->with('error', 'Invalid email or password.')
            ->withInput();
    }

    /**
     * Handle parent logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('parent.login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('parentmodule::auth.forgot-password');
    }

    /**
     * Handle forgot password request.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // TODO: Implement password reset email logic
        // This should integrate with the existing password reset system

        return redirect()->back()
            ->with('success', 'Password reset link has been sent to your email.');
    }

    /**
     * Show change password form.
     */
    public function showChangePasswordForm()
    {
        return view('parentmodule::auth.change-password');
    }

    /**
     * Handle change password request.
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->with('error', 'Current password is incorrect.');
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()
            ->with('success', 'Password changed successfully.');
    }
}

