<?php

namespace App\Http\Controllers;

use App\Models\SopPengairan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PengairanController extends Controller
{
    public function input_sop(Request $request)
    {
        try {
            $data = $request->validate([
                'nama' => 'required', // Assuming 'nama' is unique
                'min' => 'required',
                'max' => 'required',
            ]);

            $sop = SopPengairan::updateOrCreate(
                ['nama' => $data['nama']], // Key to find
                ['min' => $data['min'], 'max' => $data['max']] // Data to update or create
            );

            return response()->json([
                'success' => true,
                'message' => $sop->wasRecentlyCreated ? 'Data sop pengairan berhasil ditambahkan!' : 'Data sop pengairan berhasil diperbarui!',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }
}
