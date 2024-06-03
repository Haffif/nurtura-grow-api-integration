<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Models\Plant;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use ImageKit\ImageKit;
use Intervention\Image\Facades\Image;

class AntaresController extends Controller
{
    public function handleAntaresWebhook(Request $request)
    {
        try {
            // Direktori untuk menyimpan file .txt
            $txtDir = storage_path('app/public/txt');
            if (!file_exists($txtDir)) {
                mkdir($txtDir, 0777, true);
            }

            // Buka atau buat file .txt
            $txtFile = $txtDir . '/base64_images.txt';

            // Inisialisasi string untuk menyimpan data base64
            $all_base64_images = '';

            // Jika file .txt sudah ada, baca kontennya
            if (file_exists($txtFile)) {
                $all_base64_images = file_get_contents($txtFile);
            }
            // Ambil base64 dari request
            $con = $request->input('m2m:sgn.m2m:nev.m2m:rep.m2m:cin.con');
            $string = $request->input('m2m:sgn.m2m:nev.m2m:rep.m2m:cin.pi');
            $parts = explode("/", $string);
            $id_device = end($parts);
            $con_data = json_decode($con, true);

            if (array_key_exists('image_data', $con_data)) {

                // Tambahkan base64 ke dalam string dengan prefix
                $all_base64_images .= "data:image/jpeg;base64," . $con_data['image_data'];
                // Log::info($con_data['image_data']);

                // Jika status "end", kirim data ke ImageKit dan "https://genial-union-415103.et.r.appspot.com/predict/"
                if ($con_data['status'] === "end") {
                    Log::info($con_data['status']);

                    // Encode base64 menjadi gambar
                    $decoded_image = base64_decode(str_replace("data:image/jpeg;base64,", "", $all_base64_images));

                    // Kirim gambar ke ImageKit
                    $imageKit = new ImageKit(
                        env('IMAGEKIT_PUBLIC'),
                        env('IMAGEKIT_PRIVATE'),
                        env('IMAGEKIT_URL'),
                    );

                    // Kirim gambar ke ImageKit
                    $uploadResponse = $imageKit->upload(array(
                        "file" => base64_encode($decoded_image),
                        "fileName" => "bawang.jpg", // Ganti dengan nama file yang sesuai
                        "useUniqueFileName" => true
                    ));
                    $imageUrl = $uploadResponse->result->url;

                    Log::info('Imagekit:', [
                        'imgkit' => $imageUrl,
                    ]);

                    $image = Image::make($imageUrl);

                    // // Kompresi gambar, misal dengan kualitas 90%
                    $image->encode('jpg', 90);

                    // Kirim juga gambar ke "https://genial-union-415103.et.r.appspot.com/predict/" menggunakan HTTP attach
                    $predictResponse = Http::attach(
                        'file',
                        $image,
                        'image.jpg'
                    )->post('https://soy-analog-423112-r2.et.r.appspot.com/predict/');

                    Log::info($predictResponse->body());
                    $data = json_decode($predictResponse->body());

                    // Setelah selesai mengirim gambar, hapus file .txt
                    unlink($txtFile);

                    $plant = Plant::updateOrCreate(
                        ['id' => $con_data['id']],  
                        [
                            'id_device' => $id_device,              
                            'url' => $imageUrl,
                            'posisi' => $con_data['posisi'],
                            'detection' => $data->class,
                            'accuration' => $data->confidence,
                            'created_at' => Carbon::now(),          
                            'updated_at' => Carbon::now()           
                        ]
                    );

                    // Optional: Add a custom timestamp field for measurement time, not covered by default timestamps
                    $plant->save();

                    return response()->json([
                        "status" => 200,
                        "message" => "All images uploaded and processed successfully.",
                    ], 200);
                }
                // Simpan string base64 ke dalam file .txt
                file_put_contents($txtFile, $all_base64_images);

                return response()->json([
                    "status" => 200,
                    "message" => "Base64 images received and saved successfully.",
                ], 200);
            } else {
                Log::info($id_device);
                $data = json_decode($con, true);

                Log::info($con);


                Sensor::create([
                    'id_device' => $id_device,
                    'suhu' => $data['suhu'],
                    'kelembapan_udara' => $data['kelembapan_udara'],
                    'kelembapan_tanah' => $data['kelembapan_tanah'],
                    'ph_tanah' => $data['ph_tanah'],
                    'npk' => $data['npk'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "status" => 500,
                "message" => "An error occurred: " . $e->getMessage(),
            ], 500);
        }
    }
}
