<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\KeyPair;
use App\Models\Rekening;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

class AuthenticationController extends Controller
{
    public function me()
    {
        $user = Employee::find(auth()->id());
        return response()->json([
            'data' => new EmployeeResource($user),
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
        DB::beginTransaction();
        try {
            $user = new User;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->email_verified_at = now();
            $user->save();

            KeyPair::storeKeys($user->id, $request->password);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => "Registrasi gagal !",
            ], 500);
        }

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

    public function update_me(Request $request)
    {
        $employee = Employee::find(auth()->id());

        if (!$employee) {
            return response()->json([
                'message' => "Data user tidak ditemukan !"
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'nip' => 'required|unique:employees,nip,' . auth()->id(),
            'nik' => 'required|unique:employees,nik,' . auth()->id(),
            'email' => 'required|unique:employees,email,' . auth()->id(),
            'name' => 'required',
            'rekening' => 'required|array',
            'positions' => 'required|array|min:1',
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {
            $employee->nip = $request->nip;
            $employee->email = $request->email;
            $employee->name = $request->name;
            $employee->save();

            $employee->rekening()->delete();
            foreach ($request->rekening as $rekening) {
                $employee_rekening = new Rekening();
                $employee_rekening->employee_id = $employee->id;
                $employee_rekening->nama_bank = $rekening['nama_bank'];
                $employee_rekening->atas_nama = $rekening['atas_nama'];
                $employee_rekening->nomor_rekening = $rekening['nomor_rekening'];
                $employee_rekening->save();
            }

            $employee->positions()->delete();
            foreach ($request->positions as $position) {
                $employee_position = new EmployeePosition();
                $employee_position->employee_id = $employee->id;
                $employee_position->position = $position;
                $employee_position->save();
            }

            $user = User::find($employee->id);
            if ($user) {
                $user->email = $request->email;
                $user->save();
            }

            DB::commit();
            return response()->json([
                'message' => 'Berhasil menyimpan data user !',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function change_password(Request $request)
    {
        
        $validate = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.confirmed' => "Konfirmasi password tidak sesuai !",
            'password.min' => "Password minimal 8 karakter !",
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        $user = User::find(auth()->id());

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'old_password' => ["Password lama tidak sesuai !"]
                ],
                'message' => "Password lama tidak sesuai !"
            ], 422);
        }

        DB::beginTransaction();
        try {
            $user->password = Hash::make($request->password);
            $user->save();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil mengubah password !',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
