<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Divisao extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $connection = 'mysql';
    protected $table = 'ca_divisao';
    protected $primaryKey = 'id_divisao';
    public $timestamps = true;
    protected $fillable = ['id_acervo', 'descricao'];

    public function documentos()
    {
        return $this->hasMany(CaDocumentos::class,'id_divisao','id_divisao');
    }

    public function documentosWithoutSub()
    {
        return $this->hasMany(CaDocumentos::class,'id_divisao','id_divisao')->where('id_sub_divisao',null);
    }

    public function subdivisao()
    {
        return $this->hasMany(SubDivisao::class,'id_divisao','id_divisao');
    }

}


