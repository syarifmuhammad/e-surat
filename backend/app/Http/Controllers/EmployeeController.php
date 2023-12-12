<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeCollection;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\Position;
use App\Models\Rekening;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

    public function rekening(Request $request)
    {
        $rekening = Rekening::where('employee_id', $request->id)->get();
        return response()->json([
            'data' => $rekening
        ], 200);
    }

    public function signature() {
        $employee = Employee::find(auth()->id());

        if (!$employee) {
            return response()->json([
                'message' => "Data pegawai tidak ditemukan !"
            ], 404);
        }

        $file = storage_path('app/signature/' . $employee->signature);
        if ($employee->signature && file_exists($file)) {
            return response()->download($file, $employee->signature);
        } else {
            return response()->json(null, 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nip' => 'required|unique:employees,nip',
            'nik' => 'required|unique:employees,nik',
            'email' => 'required|unique:employees,email',
            'name' => 'required',
            'rekening' => 'required|array',
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

            foreach ($request->rekening as $rekening) {
                $employee_rekening = new Rekening();
                $employee_rekening->employee_id = $employee->id;
                $employee_rekening->nama_bank = $rekening['nama_bank'];
                $employee_rekening->atas_nama = $rekening['atas_nama'];
                $employee_rekening->nomor_rekening = $rekening['nomor_rekening'];
                $employee_rekening->save();
            }

            foreach ($request->positions as $position) {
                $employee_position = new EmployeePosition;
                $employee_position->employee_id = $employee->id;
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

    public function store_rekening(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => "Data pegawai tidak ditemukan !"
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'nama_bank' => 'required|string',
            'nomor_rekening' => 'required|string',
            'atas_nama' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors(),
                'message' => "Gagal validasi form !"
            ], 422);
        }

        DB::beginTransaction();
        try {
            $rekening = new Rekening;
            $rekening->employee_id = $id;
            $rekening->nama_bank = $request->nama_bank;
            $rekening->nomor_rekening = $request->nomor_rekening;
            $rekening->atas_nama = $request->atas_nama;
            $rekening->save();

            DB::commit();
            return response()->json([
                'message' => 'Berhasil menyimpan data rekening pegawai !',
                'data' => $rekening,
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
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => "Data pegawai tidak ditemukan !"
            ], 404);
        }

        return new EmployeeResource($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => "Data pegawai tidak ditemukan !"
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'nip' => 'required|unique:employees,nip,' . $id,
            'nik' => 'required|unique:employees,nik,' . $id,
            'email' => 'required|unique:employees,email,' . $id,
            'name' => 'required',
            'rekening' => 'required|array',
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
                $employee_position = new EmployeePosition;
                $employee_position->employee_id = $employee->id;
                $employee_position->position = $position;
                $employee_position->save();
            }

            DB::commit();
            return response()->json([
                'message' => 'Berhasil menyimpan data pegawai !',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update_roles(Request $request, $id)
    {

        try {
            $user = User::find($id);

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

    public function upload_signature($employee = null, $file) {
        if (!$employee || !$file) {
            return false;
        }
        $file = $file;
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $fileNameServer = 'signature/' . $fileName;
        Storage::put($fileNameServer, file_get_contents($file));

        $employee->signature = $fileName;
        $employee->save();
        return true;
    }

    public function update_signature_by_admin(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'signature' => 'required|file|mimes:jpg,jpeg,png|max:10000',
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => "Data pegawai tidak ditemukan !"
            ], 404);
        }

        $this->upload_signature($employee, $request->file('signature'));

        $response = [
            'message' => "Berhasil mengupload tanda tangan !"
        ];
        return response()->json($response, 200);
    }

    public function update_signature(Request $request) {
        $validate = Validator::make($request->all(), [
            'signature' => 'required|file|mimes:jpg,jpeg,png|max:10000',
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        $employee = Employee::find(auth()->id());

        if (!$employee) {
            return response()->json([
                'message' => "Data pegawai tidak ditemukan !"
            ], 404);
        }

        $this->upload_signature($employee, $request->file('signature'));

        $response = [
            'message' => "Berhasil mengupload tanda tangan !"
        ];
        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
