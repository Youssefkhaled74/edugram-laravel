<?php

namespace Modules\ParentModule\Http\Controllers;

use App\UserLogin;
use Browser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Modules\ParentModule\Models\ParentModel;
use App\Models\User;
use Stevebauman\Location\Facades\Location;

class ImpersonationController extends Controller
{
    /**
     * Login as a child (impersonate)
     *
     * @param Request $request
     * @param int $childId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAsChild(Request $request, $childId)
    {
        try {
            // Get the current parent user
            $parentUser = Auth::user();
            
            if (!$parentUser) {
                return redirect()->route('parent.login')->with('error', 'Please login first.');
            }
            
            // Get the parent model
            $parent = ParentModel::where('user_id', $parentUser->id)->first();
            
            if (!$parent) {
                return redirect()->back()->with('error', 'Parent account not found.');
            }
            
            // Check if the child belongs to this parent
            $child = $parent->children()->where('users.id', $childId)->first();
            
            if (!$child) {
                return redirect()->back()->with('error', 'You do not have permission to access this child account.');
            }
            
            // Store parent information in session BEFORE logout
            $parentData = [
                'impersonating_parent_id' => $parentUser->id,
                'impersonating_parent_name' => $parentUser->name,
                'impersonating_parent_email' => $parentUser->email,
                'impersonating_parent_role' => $parentUser->role_id,
                'impersonating_parent_login_token' => Session::get('login_token'),
                'is_impersonating' => true
            ];
            
            // Save to session
            foreach ($parentData as $key => $value) {
                Session::put($key, $value);
            }
            
            // Log the impersonation for security audit
            Log::info('Parent impersonation started', [
                'parent_id' => $parentUser->id,
                'parent_name' => $parentUser->name,
                'child_id' => $childId,
                'child_name' => $child->name,
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);
            
            // Mark parent's current login as "impersonating" (keep it active)
            if (Session::get('login_token')) {
                DB::table('user_logins')
                    ->where('token', Session::get('login_token'))
                    ->where('user_id', $parentUser->id)
                    ->update(['status' => 2]); // 2 = impersonating
            }
            
            // Logout parent from Auth but keep session data
            Auth::logout();
            
            // Login as child WITHOUT triggering device limit checks
            Auth::loginUsingId($child->id, false);
            
            // Update child's last activity
            $child->last_activity_at = now();
            $child->save();
            
            // Create a special login record for impersonation
            $childLogin = UserLogin::create([
                'user_id' => $child->id,
                'ip' => $request->ip(),
                'browser' => !empty(Browser::browserName()) ? Browser::browserName() : 'Unknown',
                'os' => !empty(Browser::platformName()) ? Browser::platformName() : 'Unknown',
                'token' => Session::getId(),
                'login_at' => Carbon::now(Settings('active_time_zone') ?? 'UTC'),
                'location' => Location::get($request->ip()),
                'status' => 3 // 3 = impersonated session
            ]);
            
            // Store child login token
            Session::put('child_login_token', $childLogin->token);
            
            // Re-save impersonation data (in case login cleared it)
            foreach ($parentData as $key => $value) {
                Session::put($key, $value);
            }
            
            // Determine redirect based on child's role
            if ($child->role_id == 3) {
                // Student dashboard
                $redirectRoute = 'studentDashboard';
            } else {
                // Default dashboard
                $redirectRoute = 'dashboard';
            }
            
            return redirect()->route($redirectRoute)->with('info', 'You are now logged in as ' . $child->name . '. Click "Back to Parent Dashboard" to return.');
            
        } catch (\Exception $e) {
            Log::error('Impersonation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'parent_id' => Auth::id(),
                'child_id' => $childId
            ]);
            
            return redirect()->back()->with('error', 'Failed to login as child: ' . $e->getMessage());
        }
    }
    
    /**
     * Return to parent dashboard (stop impersonation)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function returnToParent(Request $request)
    {
        try {
            // Check if currently impersonating
            if (!Session::has('impersonating_parent_id')) {
                return redirect()->route('parent.dashboard')->with('warning', 'You are not currently impersonating a child.');
            }
            
            // Get the parent user ID from session
            $parentUserId = Session::get('impersonating_parent_id');
            $parentLoginToken = Session::get('impersonating_parent_login_token');
            $currentChildId = Auth::id();
            $currentChildName = Auth::user() ? Auth::user()->name : 'Unknown';
            $childLoginToken = Session::get('child_login_token');
            
            // Find the parent user
            $parentUser = User::find($parentUserId);
            
            if (!$parentUser) {
                // Clear session if parent not found
                Session::forget([
                    'impersonating_parent_id', 
                    'impersonating_parent_name', 
                    'impersonating_parent_email',
                    'impersonating_parent_role',
                    'impersonating_parent_login_token',
                    'is_impersonating',
                    'child_login_token'
                ]);
                return redirect()->route('parent.login')->with('error', 'Parent account not found. Please login again.');
            }
            
            // Log the return for security audit
            Log::info('Parent impersonation ended', [
                'parent_id' => $parentUserId,
                'parent_name' => $parentUser->name,
                'child_id' => $currentChildId,
                'child_name' => $currentChildName,
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);
            
            // Mark child's impersonated login as ended
            if ($childLoginToken) {
                DB::table('user_logins')
                    ->where('token', $childLoginToken)
                    ->where('user_id', $currentChildId)
                    ->update([
                        'status' => 0,
                        'logout_at' => Carbon::now(Settings('active_time_zone') ?? 'UTC')
                    ]);
            }
            
            // Logout child
            Auth::logout();
            
            // Restore parent's login status
            if ($parentLoginToken) {
                DB::table('user_logins')
                    ->where('token', $parentLoginToken)
                    ->where('user_id', $parentUserId)
                    ->update(['status' => 1]); // 1 = active
            }
            
            // Login back as parent WITHOUT triggering device limit
            Auth::loginUsingId($parentUserId, false);
            
            // Update parent's last activity
            $parentUser->last_activity_at = now();
            $parentUser->save();
            
            // Restore parent's login token
            if ($parentLoginToken) {
                Session::put('login_token', $parentLoginToken);
            }
            
            // Clear impersonation session data
            Session::forget([
                'impersonating_parent_id', 
                'impersonating_parent_name', 
                'impersonating_parent_email',
                'impersonating_parent_role',
                'impersonating_parent_login_token',
                'is_impersonating',
                'child_login_token'
            ]);
            
            return redirect()->route('parent.dashboard')->with('success', 'Welcome back! You have returned to your parent dashboard.');
            
        } catch (\Exception $e) {
            Log::error('Return to parent failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_parent_id' => Session::get('impersonating_parent_id')
            ]);
            
            // Try to recover by clearing impersonation session
            Session::forget([
                'impersonating_parent_id', 
                'impersonating_parent_name', 
                'impersonating_parent_email',
                'impersonating_parent_role',
                'impersonating_parent_login_token',
                'is_impersonating',
                'child_login_token'
            ]);
            
            return redirect()->route('parent.login')->with('error', 'Session error occurred. Please login again.');
        }
    }
    
    /**
     * Check if current user is impersonating
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkImpersonation(Request $request)
    {
        return response()->json([
            'is_impersonating' => Session::get('is_impersonating', false),
            'parent_name' => Session::get('impersonating_parent_name', null),
            'parent_id' => Session::get('impersonating_parent_id', null),
            'current_user_id' => Auth::id(),
            'current_user_name' => Auth::user() ? Auth::user()->name : null
        ]);
    }
}

