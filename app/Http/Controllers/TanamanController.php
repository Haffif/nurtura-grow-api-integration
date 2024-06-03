<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\Request;

class TanamanController extends Controller
{
    public function get_plant(Request $request)
    {
        try {
            $id_device = $request->query('id_device');

            if (!empty($id_device)) {
                $tanaman = Plant::where('id_device', $id_device)->get();
                if ($tanaman->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada tanaman yang ditemukan untuk id_device ini.'
                    ], 404);
                }
                return response()->json([
                    'success' => true,
                    'data' => $tanaman
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter id_device diperlukan.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }
}