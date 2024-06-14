<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

use App\Models\Sensor;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function get_sensor(Request $request)
    {
        try {
            $data = $request->validate([
                'id_user' => 'required',
            ]);

            $id_user = $data['id_user'];
            $type_sensor = $request->query('type_sensor');
            $user = UserDevice::where('id_user', $id_user)->first();

            if ($user == null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ditemukan device untuk user ini. Mohon daftarkan device dahulu !'
                ], 404);
            };

            $id_device = $user->id_device;
            
            if (!empty($id_device)) {
                $data_sensor = Sensor::where('id_device', $id_device)->get();
                if ($data_sensor->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada data_sensor yang ditemukan untuk id_device ini.'
                    ], 404);
                }

                if($type_sensor){
                    $filtered_data = [];
                    foreach ($data_sensor as $sensor) {
                        if ($type_sensor && $sensor[$type_sensor]) {
                            $filtered_data[] = [
                                'id_device' => $id_device,
                                $type_sensor => $sensor[$type_sensor],
                                'timestamp_pengukuran' => $sensor['timestamp_pengukuran'],
                                'created_at' => $sensor['created_at'],
                                'updated_at' => $sensor['updated_at'],
                            ];
                        }
                    }
                 
                    return response()->json([
                        'success' => true,
                        'data' => $filtered_data
                    ], 200);
                };
                
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

    public function get_latest_sensor(Request $request)
    {
        try {
            $id_user = $request->query('id_user');
            $user = UserDevice::where('id_user', $id_user)->first();
            if ($user == null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ditemukan device untuk user ini. Mohon daftarkan device dahulu !'
                ], 404);
            };

            $id_device = $user->id_device;

            if (!empty($id_device)) {
                $data_sensor = Sensor::where('id_device', $id_device)
                    ->orderBy('created_at', 'desc') // Assuming 'created_at' is the timestamp column
                    ->first();
                if ($data_sensor == null) {
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
