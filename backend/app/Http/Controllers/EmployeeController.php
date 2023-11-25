<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeCollection;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employees = Employee::search($request->search)->exceptSuperAdmin()->paginate();
        return new EmployeeCollection($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nip' => 'required|unique:employees,nip',
            'email' => 'required|unique:employees,email',
            'name' => 'required',
            'positions' => 'required|array',
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
            $employee = new Employee;
            $employee->nip = $request->nip;
            $employee->email = $request->email;
            $employee->name = $request->name;
            $employee->save();

            foreach ($request->positions as $position) {
                $employee_position = new EmployeePosition;
                $employee_position->nip = $employee->nip;
                $employee_position->position = $position;
                $employee_position->save();
            }

            DB::commit();
            return response()->json([
                'message' => 'Berhasil menyimpan data pegawai !',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function update_roles(Request $request, string $nip)
    {

        try {
            $user = User::find($nip);

            if (!$user) {
                return response()->json([
                    'message' => "Data pegawai tidak ditemukan !"
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'roles' => 'required|string|in:pegawai,admin_sdm,admin_sekretariat',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'errors' => $validate->errors(),
                    'message' => "Gagal validasi form !"
                ], 422);
            }

            $user->roles = $request->roles;
            $user->save();
            return response()->json([
                'message' => 'Berhasil menyimpan roles pegawai !',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
