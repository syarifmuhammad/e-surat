<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProdiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $prodi = Prodi::search($search)->paginate();
        return $prodi;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nama_prodi' => "required|string|unique:prodi",
            'singkatan_prodi' => "required|string",
            'nama_fakultas' => "required|string",
            'singkatan_fakultas' => "required|string",
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
            $new_prodi = new Prodi();
            $new_prodi->nama_prodi = $request->nama_prodi;
            $new_prodi->singkatan_prodi = $request->singkatan_prodi;
            $new_prodi->nama_fakultas = $request->nama_fakultas;
            $new_prodi->singkatan_fakultas = $request->singkatan_fakultas;
            $new_prodi->save();
            
            DB::commit();
            return response()->json([
                'message' => 'Berhasil menyimpan data prodi',
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
