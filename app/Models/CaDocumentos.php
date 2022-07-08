<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\CanResetPassword ;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CaDocumentos extends Authenticatable
{
    use HasFactory,Notifiable;
    protected $connection = 'mysql';
    protected $table = 'ca_documento';
    protected $primaryKey = 'id_documento';
    public $timestamps = true;
    protected $fillable = ['id_origem','id_divisao','id_sub_divisao', 'id_funcionario', 'documento', 'arquivo' , 'referencia',];

    public function divisao()
    {
        return $this->hasOne(Divisao::class,'id_divisao','id_divisao');
    }

    public function subdivisao()
    {
        return $this->hasOne(SubDivisao::class,'id_sub_divisao','id_sub_divisao');
    }

}
