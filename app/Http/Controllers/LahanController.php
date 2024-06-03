<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Models\Lahan;

class LahanController extends Controller
{
    public function input_lahan(Request $request){
        try {
            
            $data_lahan = $request->validate([
                'id_user' => 'required',
                'nama_lahan' => 'required',
                'deskripsi' => 'required',
                'longitude' => 'required',
                'latitude' => 'required'
            ]);

            Lahan::create([
                'id_user' => $data_lahan['id_user'],
                'nama_lahan' => $data_lahan['nama_lahan'],
                'deskripsi' => $data_lahan['deskripsi'],
                'longitude' => $data_lahan['longitude'],
                'latitude' => $data_lahan['latitude'],
            ]);

            return response()->json([
                'success' => true,
                'message'    => 'Data lahan ditambahkan !',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }

    public function get_lahan(Request $request)
    {
        // Mendapatkan id_user dari query string
        $id_user = $request->query('id_user');

        try {
            if (!empty($id_user)) {
                // Mencari semua lahan yang memiliki id_user yang sesuai
                $lahan = Lahan::where('id_user', $id_user)->get();

                // Memeriksa apakah hasilnya kosong
                if ($lahan->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada lahan yang ditemukan untuk id_user ini.'
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => $lahan
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter id_user diperlukan.'
                ], 400);
            }
        } catch (\Exception $e) {
            // Menangani kesalahan server
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
