<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\UserRole;
use  App\Notifications\EmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function checkIfUserExists($email)
{
    return User::where('email', $email)->exists();
}
    public function registerCommittee(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);
    
        // Recherchez l'employé avec l'e-mail spécifié
        $employee = Employee::where('email', $request->email)->first();
    
        if ($employee) {
            // L'employé existe
            // Vérifiez si l'employé a un rôle dans le comité
            $hasRoleInCommittee = UserRole::where('user_id', $employee->id)
                                          ->exists();
     // Si l'employé a  un rôle dans le comité, créez un nouvel utilisateur
            if ($hasRoleInCommittee) {

                
            if (!$this->checkIfUserExists($request->email)) {
                // Si l'employé n'a pas encore de compte utilisateur, créez-en un
                $user = User::create([
                    'id' => $employee->id,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'email_verified_at' => null,
                    'verification_token' => Str::random(64),
                ]);
                $user->sendEmailVerificationNotification();
                return response()->json(['message' => 'User registered successfully']);
            } else {
                // Si l'employé a déjà un compte utilisateur, renvoyez une erreur
                return response()->json(['message' => 'Employee already has a user account'], 403);
            }
            } else {
                // Si l'employé n'a pas de rôle dans le comité, renvoyez une erreur
                return response()->json(['message' => 'Employee does not have a role in the committee'], 403);
            }
        } else {
            // Si aucun employé avec cet e-mail n'existe, renvoyez une erreur
            return response()->json(['message' => 'Employee does not exist'], 404);
        }
    }
    public function registeremployee(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);
    
        // Recherchez l'employé avec l'e-mail spécifié
        $employee = Employee::where('email', $request->email)->first();
    
        if ($employee) {
            // L'employé existe
 
            if (!$this->checkIfUserExists($request->email)) {
                // Si l'employé n'a pas encore de compte utilisateur, créez-en un
                $user = User::create([
                    'id' => $employee->id,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'email_verified_at' => null,
                ]);
               // Send email verification notification
        $user->sendEmailVerificationNotification();
                return response()->json(['message' => 'User registered successfully']);
            } else {
                // Si l'employé a déjà un compte utilisateur, renvoyez une erreur
                return response()->json(['message' => 'Employee already has a user account'], 403);
            }
        } else {
            // Si aucun employé avec cet e-mail n'existe, renvoyez une erreur
            return response()->json(['message' => 'Employee does not exist'], 404);
        }
    }
        
    
    public function verifyEmail(Request $request, $token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid verification token'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['error' => 'Email already verified']);
        }

        $user->markEmailAsVerified();

        return response()->json(['message' => 'Email verified successfully']);
    }

// Login Endpoint
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8',
    ]);

    // Find the user by email
    $user = User::where('email', $request->email)->first();

    if ($user) {
        // Check if the user's email is verified
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email not verified'], 403);
        } else {
            if ($user->email === 'admin@gmail.com' && $request->password === 'adminADMIN') {
            // Generate an authentication token for the admin
            $token = $user->createToken('auth_token')->plainTextToken;
        
            // Return the authentication token and admin details
            return response()->json([
                'access_token' => $token,
                'user' => $user,
                'message' => 'Admin login successful',
                'isAdmin'=>'true'
            ]);
        } else {
            // If the user's email is verified and is an employee, verify the password
            if (Hash::check($request->password, $user->password)) {
                // Get the corresponding employee details
                $employee = Employee::find($user->id);

                // Generate an authentication token
                $token = $user->createToken('auth_token')->plainTextToken;
                
                // Return the authentication token, user details, and employee details
                return response()->json([
                    'access_token' => $token,
                    'user' => $user,
                    'employee' => $employee,
                    'message' => 'Login successful',
                ]);
            } 
                
                // If the password is incorrect, return an error
                 else return response()->json(['message' => 'Invalid credentials'], 401);
            
    
        }}

         
            
}
}

}   
