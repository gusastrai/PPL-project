<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
class buku extends Model
{
    use Notifiable;

    /**
     * The "booting" function of model
     *
     * @return void
     */
    protected static function boot() {
        static::creating(function ($model) {
            if ( ! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

     /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'buku';

    protected $fillable = [
        'id_buku',
        'id_kategori_buku',
        'id_jenis_buku',
        'author_buku',
        'publisher_buku',
        'judul_buku',
        'foto_buku',
        'tahun_terbit',
        'bahasa_buku',
        'stok_buku',
        'rak_buku',
        'tgl_ditambahkan',
    ];
    public function kategoriBuku()
    {
        return $this->belongsTo(kategori_buku::class, 'id_kategori_buku', 'id_kategori_buku');
    }

    // Relationship with JenisBuku
    public function jenisBuku()
    {
        return $this->belongsTo(jenis_buku::class, 'id_jenis_buku', 'id_jenis_buku');
    }
    public function peminjaman()
    {
        return $this->hasMany(transaksi_peminjaman::class,  'id_buku', 'id_buku');
    }




}
