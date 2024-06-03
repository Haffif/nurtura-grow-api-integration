<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function get_sensor(Request $request)
    {
        try {
            $id_device = $request->query('id_device');

            if (!empty($id_device)) {
                $data_sensor = Sensor::where('id_device', $id_device)->get();
                if ($data_sensor->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada data_sensor yang ditemukan untuk id_device ini.'
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => $data_sensor
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
