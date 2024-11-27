<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

     /**
     * fillable
     *
     * @var array
     */

    //  proses Mass Assignment yang dimana bertujuan agar field yang kita tambahkan, dapat menyimpan sebuah nilai 
    protected $fillable = [
        'image',
        'title',
        'content'
    ];

        /**
     * image
     *
     * @return Attribute
     */

    //  ini berfungsi dimana jika kita mengambil sebuah data field image secara langsung terdeteksi pada path yang telah di sediakan
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/posts/' . $image),
        );
    }
}
