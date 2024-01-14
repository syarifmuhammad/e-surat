<?php

namespace App\Http\Controllers;

use App\Http\Resources\PositionCollection;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $positions = Position::search($search);
        if (isset($request->type) && ($request->type == 'struktural' || $request->type == 'fungsional') ) {
                $positions = $positions->where('type', $request->type);
        }
        $positions = $positions->paginate();
        return new PositionCollection($positions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => "required|string|unique:positions",
            'type' => "required|string|in:struktural,fungsional",
        ]);

        $validate->setAttributeNames([
            'name' => 'Posisi / Jabatan',
            'type' => "Jenis Posisi / Jabatan"
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
            $new_position = new Position();
            $new_position->name = $request->name;
            $new_position->type = $request->type;
            $new_position->save();
            
            DB::commit();
            return response()->json([
                'message' => 'Berhasil menyimpan data jabatan',
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
        $position = Position::find($id);
        if (!$position) {
            return response()->json([
                'message' => 'Data jabatan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil menampilkan data jabatan',
            'data' => $position
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => "required|string|unique:positions",
            'type' => "required|string|in:struktural,fungsional",
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        $position = Position::find($id);

        if (!$position) {
            return response()->json([
                'message' => 'Data jabatan tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $position->name = $request->name;
            $position->type = $request->type;
            $position->save();
            
            DB::commit();
            return response()->json([
                'message' => 'Berhasil menyimpan data jabatan',
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
        $position = Position::find($id);

        if (!$position) {
            return response()->json([
                'message' => 'Data jabatan tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $position->delete();
            
            DB::commit();
            return response()->json([
                'message' => 'Berhasil menghapus data jabatan',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}
