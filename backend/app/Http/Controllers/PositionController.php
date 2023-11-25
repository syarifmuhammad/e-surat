<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $positions = Position::search($search)->paginate();
        return $positions;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // DB::beginTransaction();
        // try {
        //     Position::create($request->all());
        //     DB::commit();
        //     return response()->json([
        //         'message' => 'Berhasil menyimpan data jabatan',
        //     ]);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return response()->json([
        //         'message' => $e->getMessage()
        //     ]);
        // }
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
