<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Models\Penanaman;

class PenanamanController extends Controller
{
    public function get_penanaman(Request $request){
        $id_user = $request->query('id_user');

        try {
            if (!empty($id_user)) {
                $penanaman = Penanaman::where('id_user', $id_user)->get();
                if ($penanaman->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada penanaman yang ditemukan untuk id_user ini.'
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => $penanaman
                ], 200);
                
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter id_user diperlukan.'
                ], 400);
            }

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function input_penanaman(Request $request){
        try {
            $data = $request->validate([
                'id_user' => 'required',
                'id_lahan' => 'required',
                'nama_penanaman' => 'required',   
                'jenis_tanaman' => 'required',             
                'tanggal_tanam' => 'required',
            ]);

            Penanaman::create([
                'id_user' => $data['id_user'],
                'id_lahan' => $data['id_lahan'],
                'nama_penanaman' => $data['nama_penanaman'],
                'jenis_tanaman' => $data['jenis_tanaman'],
                'tanggal_tanam' => $data['tanggal_tanam'],
            ]);
            return response()->json([
                'success' => true,
                'message'    => 'Data penanaman ditambahkan !',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update_tinggi(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'required',
                'tanggal_pencatatan' => 'required',
                'tinggi_tanaman' => 'required', // based in cm
            ]);

            $penanaman = Penanaman::find($data['id']);

            if ($penanaman) {
                // Record exists, update the record
                $penanaman->update([
                    'tanggal_pencatatan' => $data['tanggal_pencatatan'],
                    'tinggi_tanaman' => $data['tinggi_tanaman'],
                ]);
                return response()->json([
                    'success' => true,
                    'message'    => 'Data tinggi diupdate !',
                ], 200);
            } else {
                return response()->json(['error' => 'Server error', 'message' => 'Data penanaman tidak ditemukan!'], 500);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }
}