<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthenticationController extends Controller
{
    public function me()
    {
        $user = auth()->user();
        return response()->json([
            'data' => $user,
            'message' => "Berhasil mendapatkan data user !",
        ], 200);
    }

    public function store(Request $request)
    {
        // $user = User::where('email', $request->email)->first();

        // if ($user && $user->email_verified_at) {
        $email_validate = ['string', 'email', 'max:255', 'exists:' . Employee::class, 'unique:users'];
        // } else {
        //     $email_validate = ['string', 'email', 'max:255', 'exists:' . Employee::class];
        // }

        $validate = Validator::make($request->all(), [
            'email' => $email_validate,
            'password' => ['required', 'confirmed'],
        ], [
            'email.exists' => "Email tidak terdaftar, hubungi admin untuk mendaftarkan email terlebih dahulu !",
            'password.confirmed' => "Konfirmasi password tidak sesuai !",
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        $employee = Employee::where('email', $request->email)->first();

        // if ($user) {
        //     $user = User::where('email', $request->email)->update([
        //         'password' => Hash::make($request->password),
        //     ]);
        // } else {
        User::create([
            'nip' => $employee->nip,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);
        // }

        // event(new Registered($user));

        return response()->json([
            'message' => "Registrasi berhasil !",
        ], 201);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form login gagal !"
            ];
            return response()->json($response, 422);
        }
        if (auth()->attempt($request->only('email', 'password'))) {
            $token = $request->user()->createToken("main-app-token");
            return response()->json([
                'data' => [
                    'token' => $token->plainTextToken,
                    'user' => auth()->user(),
                ],
                'message' => "Login berhasil !",
            ], 200);
        } else {
            return response()->json([
                'message' => 'Email atau password salah !'
            ], 401);
        }
    }
}
