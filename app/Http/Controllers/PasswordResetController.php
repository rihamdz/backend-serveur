<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
    
        // Generate and save reset code for the user
        $user = User::where('email', $request->email)->first();
        $resetCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); // Générer un code de 4 chiffres
    
        // Save the reset code in the password_resets table
        PasswordReset::updateOrCreate(
            ['email' => $user->email],
            ['reset_code' => $resetCode]
        );
    
        // Send reset code to user via email 
        $user->notify(new ResetPasswordNotification($resetCode));
    
        return response()->json(['message' => 'Reset code sent successfully']);
    }
    
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'reset_code' => 'required',
        ]);
    
        // Check if the reset code matches
        $passwordReset = PasswordReset::where('email', $request->email)
            ->where('reset_code', $request->reset_code)
            ->first();
    
        if (!$passwordReset) {
            return response()->json(['error' => 'Invalid reset code'], 400);
        }
    
        return response()->json(['message' => 'Reset code verified successfully']);
    }
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'new_password' => 'required|min:6',
        ]);
    
        // Update user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->new_password);
        $user->save();
    
        // Delete all existing password reset records for the user
        PasswordReset::where('email', $request->email)->delete();
    
        return response()->json(['message' => 'Password reset successfully']);
    }
}
