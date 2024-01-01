<?php

namespace App\Http\Controllers;

use App\Http\Resources\UnitCollection;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $unit = Unit::search($search)->paginate();
        return new UnitCollection($unit);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nama' => "required|string|unique:unit",
            'singkatan' => "required|string",
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
            $new_unit = new Unit();
            $new_unit->nama = $request->nama;
            $new_unit->singkatan = $request->singkatan;
            $new_unit->save();

            DB::commit();
            return response()->json([
                'message' => 'Berhasil menyimpan data unit kerja',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Unit = Unit::find($id);
        if (!$Unit) {
            return response()->json([
                'message' => 'Data Unit Kerja tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil menampilkan data unit kerja',
            'data' => new UnitResource($Unit)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = Validator::make($request->all(), [
            'nama' => "required|string|unique:unit",
            'singkatan' => "required|string",
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        $unit = Unit::find($id);

        if (!$unit) {
            return response()->json([
                'message' => 'Data Unit Kerja tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $unit->nama = $request->nama;
            $unit->singkatan = $request->singkatan;
            $unit->save();

            DB::commit();
            return response()->json([
                'message' => 'Berhasil mengubah data unit kerja',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $unit = Unit::find($id);

        if (!$unit) {
            return response()->json([
                'message' => 'Data Unit Kerja tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $unit->delete();

            DB::commit();
            return response()->json([
                'message' => 'Berhasil menghapus data unit kerja',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}
