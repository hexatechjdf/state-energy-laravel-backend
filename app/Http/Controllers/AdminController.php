<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:6',
        ]);

        try {
            $user = loginUser();

            // Update user details
            $user->name = $request->username;
            $user->email = $request->email;

            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();
            $message = !empty($request->password) ? 'User profile and password updated successfully' : 'User profile updated successfully';
            return response()->json(['status' => 'Success', 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'Error', 'message' => $e->getMessage()], 500);
        }
    }
    public function dashboard(Request $request)
    {
        $user = loginUser();
        return view('location.dashboard',compact('user'));
    }
}
