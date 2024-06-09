<?php

namespace App\Http\Controllers;

use App\Models\Irrigation;
use App\Models\SopPengairan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PengairanController extends Controller
{
    public function get_data(Request $request){
        $id_device = $request->query('id_device');
        try {
            if (!empty($id_device)) {
                // Mencari semua irrigation yang memiliki id_user yang sesuai
                $irrigation = Irrigation::where('id_device', $id_device)->get();

                // Memeriksa apakah hasilnya kosong
                if ($irrigation->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada data yang ditemukan untuk id_device ini.'
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => $irrigation
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter id_device diperlukan.'
                ], 400);
            }
        } catch (\Exception $e) {
            // Menangani kesairrigation server
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

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
