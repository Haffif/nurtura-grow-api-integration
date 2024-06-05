<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SopPengairan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'nama',
        'min',
        'max',
        'created_at',
        'updated_at'
    ];

    // $guarded bisa digunakan sebagai alternatif untuk melindungi field tertentu
    // protected $guarded = [];
    protected $table = 'sop_pengairan';
}
