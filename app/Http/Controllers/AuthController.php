<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;  // Adaugă această linie aici
use App\Models\User;
use Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;  // Asigură-te că DB este și el importat dacă îl folosești
use Illuminate\Support\Facades\Mail; // Asigură-te că Mail este și el importat dacă îl folosești
use Carbon\Carbon;



class AuthController extends Controller
{
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Dacă autentificarea a eșuat, înregistrăm un mesaj de logare de eroare
            Log::channel('db')->info('User has failed login', [
                'email' => $request->email,
                'user_id' => null,
                'performed_by' => null,
                'event_type_id' => 4,
                'ip_address' => request()->ip(),
            ]);

            // Returnăm un mesaj de eroare către client
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        // Dacă autentificarea a avut succes, înregistrăm un mesaj de logare de informare
        Log::channel('db')->info('User has successful login', [
            'user_id' => $user->id,
            'performed_by' => $user->id,
            'event_type_id' => 1,
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'accessToken' => $token,
            'userData' => [
                'id' => $user->id,
                'fullName' => $user->name,
                'username' => $user->username ?? '',
                'avatar' => url('/images/avatars/avatar-' . $user->id . '.png'),
                'email' => $user->email,
                'role' => $user->roles->first()->name ?? '',
            ],
            'userAbilityRules' => $user->getPermissionsViaRoles()->map(function ($permission) {
                $permNameParts = explode(' ', $permission->name);
                $subject = implode(' ', array_slice($permNameParts, 0, -1));
                $action = end($permNameParts);

                return [
                    'action' => $action,
                    'subject' => $subject,
                ];
            })->toArray(),
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'If that email address is in our records, we will send you an email to reset your password.'], 200);
        }

        $token = Str::random(60);

        // Verifică dacă există deja un token și actualizează-l sau creează unul nou
        $data = [
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ];

        DB::table('password_reset_tokens')->updateOrInsert(['email' => $request->email], $data);

        Mail::send('emails.password_reset', ['token' => $token, 'email' => $request->email], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password Notification');
        });

        return response()->json(['message' => 'If that email address is in our records, we will send you an email to reset your password.'], 200);
    }


    // Metoda pentru resetarea parolei
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $passwordReset = DB::table('password_reset_tokens')
            ->where([
                ['token', $request->token],
                ['email', $request->email]
            ])->first();

        if (!$passwordReset) {
            return response()->json(['message' => 'This password reset token is invalid.'], 404);
        }

        $user = User::where('email', $passwordReset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Ștergem tokenul după resetare
        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return response()->json(['message' => 'Your password has been successfully reset.']);
    }

    public function verifyToken(Request $request)
    {
        $token = $request->input('token');
        $passwordReset = DB::table('password_reset_tokens')->where('token', $token)->first();

        if (!$passwordReset || $passwordReset->created_at < now()->subHours(2)) {  // Exemplu: tokenul expiră după 2 ore
            return response()->json(['isValid' => false]);
        }

        return response()->json(['isValid' => true]);
    }
}
