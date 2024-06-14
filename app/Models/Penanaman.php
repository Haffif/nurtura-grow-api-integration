<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penanaman extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_user',
        'id_lahan',
        'id_device',
        'tinggi_tanaman',
        'jenis_tanaman',
        'tanggal_tanam',
        'tanggal_panen',
        'tanggal_pencatatan',
        'nama_penanaman',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    protected $primaryKey = 'id';
    protected $table = 'penanaman';

}
