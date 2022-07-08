<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diretoria extends Model{
    use HasFactory;

    protected $connection = 'mysql';
    protected  $guard = 'admin';
    protected $table = 'diretorias';
    protected $primaryKey = 'id_diretoria';
    public $timestamps = true;


    public function departamentos()
    {
        return $this->belongsToMany(Departamentos::class,'grupos_acessos','id_diretoria','id_setor');
    }
}

