<?php

namespace App\Http\Controllers\Antares;

use Carbon\Carbon;

use App\Models\Sensor;
use App\Models\Device;
use App\Models\Irrigation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SensorDevice
{
    public static function handleSensor(Request $request)
    {
        try {
            $con = $request->input('m2m:sgn.m2m:nev.m2m:rep.m2m:cin.con');
            $string = $request->input('m2m:sgn.m2m:nev.m2m:rep.m2m:cin.pi');
            $parts = explode("/", $string);
            $id_device = end($parts);
            $con_data = json_decode($con, true);

            Self::processSensorData($con_data, $id_device);

            return response()->json([
                "status" => 200,
                "message" => "Data Received Successfully",
            ], 200);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return response()->json([
                "status" => 500,
                "message" => "An error occurred: " . $e->getMessage(),
            ], 500);
        }
    }

    static function processSensorData($con_data, $id_device)
    {
        $currentTimestamp = Carbon::now()->format('Y:m:d H:i');
        $isComplete = false;

        if ($con_data['id_device'] != 'master') {
            $sensor = Sensor::where('timestamp_pengukuran', $currentTimestamp)->where('id_plant', 'CAEP0v54HFOtV1FsuyB');

            if ($sensor->exists()) {
                Log::info("Slave update");
                $isComplete = true;

                $sensor->update([
                    'id_plant' => $con_data['id_device'] == '-' ? 0 : $con_data['id_device'],
                    'kelembapan_tanah' => $con_data['kelembapan_tanah'] == '-' ? 0 : $con_data['kelembapan_tanah'],
                    'ph_tanah' => $con_data['ph_tanah'] == '-' ? 0 : $con_data['ph_tanah'],
                    'nitrogen' => $con_data['nitrogen'] == '-' ? 0 : $con_data['nitrogen'],
                    'fosfor' => $con_data['fosfor'] == '-' ? 0 : $con_data['fosfor'],
                    'kalium' => $con_data['pottasium'] == '-' ? 0 : $con_data['pottasium'],
                ]);
            } else {
                $affectedRows = Sensor::where('timestamp_pengukuran', $currentTimestamp)
                    ->update([
                        'id_plant' => $con_data['id_device'] == '-' ? 0 : $con_data['id_device'],
                        'kelembapan_tanah' => $con_data['kelembapan_tanah'] == '-' ? 0 : $con_data['kelembapan_tanah'],
                        'ph_tanah' => $con_data['ph_tanah'] == '-' ? 0 : $con_data['ph_tanah'],
                        'nitrogen' => $con_data['nitrogen'] == '-' ? 0 : $con_data['nitrogen'],
                        'fosfor' => $con_data['fosfor'] == '-' ? 0 : $con_data['fosfor'],
                        'kalium' => $con_data['pottasium'] == '-' ? 0 : $con_data['pottasium'],
                    ]);
                if ($affectedRows > 0) {
                    $isComplete = true;
                    Log::info("Slave Updated {$affectedRows} sensors.");
                } else {
                    Log::info("Slave create");
                    Sensor::create([
                        'id_device' => 'CAEP0v54HFOtV1FsuyB',
                        'id_plant' => $con_data['id_device'] == '-' ? 0 : $con_data['id_device'],
                        'suhu' => $con_data['suhu'] == '-' ? 0 : $con_data['suhu'],
                        'kelembapan_udara' => $con_data['kelembapan_udara'] == '-' ? 0 : $con_data['kelembapan_udara'],
                        'kelembapan_tanah' => $con_data['kelembapan_tanah'] == '-' ? 0 : $con_data['kelembapan_tanah'],
                        'ph_tanah' => $con_data['ph_tanah'] == '-' ? 0 : $con_data['ph_tanah'],
                        'nitrogen' => $con_data['nitrogen'] == '-' ? 0 : $con_data['nitrogen'],
                        'fosfor' => $con_data['fosfor'] == '-' ? 0 : $con_data['fosfor'],
                        'kalium' => $con_data['pottasium'] == '-' ? 0 : $con_data['pottasium'],
                        'timestamp_pengukuran' => $currentTimestamp,
                    ]);
                }
            }
        }

        if ($con_data['id_device'] == 'master') {
            $affectedRows = Sensor::where('timestamp_pengukuran', $currentTimestamp)
                ->update([
                    'suhu' => $con_data['suhu'] == '-' ? 0 : $con_data['suhu'],
                    'kelembapan_udara' => $con_data['kelembapan_udara'] == '-' ? 0 : $con_data['kelembapan_udara'],
                ]);

            if ($affectedRows > 0) {
                $isComplete = true;
                Log::info("Master Updated {$affectedRows} sensors.");
            } else {
                Log::info("Master create");

                Sensor::create([
                    'id_device' => 'CAEP0v54HFOtV1FsuyB',
                    'suhu' => $con_data['suhu'] == '-' ? 0 : $con_data['suhu'],
                    'kelembapan_udara' => $con_data['kelembapan_udara'] == '-' ? 0 : $con_data['kelembapan_udara'],
                    'kelembapan_tanah' => $con_data['kelembapan_tanah'] == '-' ? 0 : $con_data['kelembapan_tanah'],
                    'ph_tanah' => $con_data['ph_tanah'] == '-' ? 0 : $con_data['ph_tanah'],
                    'nitrogen' => $con_data['nitrogen'] == '-' ? 0 : $con_data['nitrogen'],
                    'fosfor' => $con_data['fosfor'] == '-' ? 0 : $con_data['fosfor'],
                    'kalium' => $con_data['pottasium'] == '-' ? 0 : $con_data['pottasium'],
                    'timestamp_pengukuran' => $currentTimestamp,
                ]);
            }
        }

        if ($isComplete) {
            $datas = Sensor::where('timestamp_pengukuran', $currentTimestamp)->where('id_device', 'CAEP0v54HFOtV1FsuyB')->first();
            $data = [
                "SoilMoisture" => $datas->kelembapan_tanah,
                "Humidity" => $datas->kelembapan_udara,
                "temperature" => $datas->suhu
            ];
            
            // // Rekomendasi ML irigasi 
            $response = Http::post(route('ml.irrigation'), $data);
            $data_response = json_decode($response, true)['data'];
            // Log::info($data_response);

            if ($data_response['Informasi Kluster']['nyala']) {
                $type = 0;
                $status = 'OPEN';
                $durasi = $data_response['Informasi Kluster']['waktu'];
                $menit = $durasi / 60;
                $volume = 7 * $menit;

                $dataDownlink = ([
                    'data' => $type . $status . $durasi
                ]);

                $responseDownlink = Http::post(route('antares.downlink'), $dataDownlink);

                Log::info($responseDownlink);

                if ($responseDownlink->status() == 200) {
                    $irrigation = Irrigation::create([
                        'id_device' => 'CAEP0v54HFOtV1FsuyB',
                        'rekomendasi_volume' => $volume,
                        'kondisi' => $data_response['Kondisi'],
                        'saran' => $data_response['Saran'],
                    ]);

                    // Access the ID of the newly created record
                    $irrigationId = $irrigation->id;

                    Device::create([
                        'id' => 'CAEP0v54HFOtV1FsuyB',
                        'irrigation' => $irrigationId,
                        'tipe_intruksi' => $type,
                        'durasi' => $durasi,
                        'volume' => $volume
                    ]);
                }
            } else {
                $irrigation = Irrigation::create([
                    'id_device' => 'CAEP0v54HFOtV1FsuyB',
                    'rekomendasi_volume' => 0,
                    'kondisi' => $data_response['Kondisi'],
                    'saran' => $data_response['Saran'],
                ]);
            }
        }
    }
}
