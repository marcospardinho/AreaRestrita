<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentos extends Model{
    use HasFactory;

    protected $connection = 'mysql';
    protected  $guard = 'admin';
    protected $table = 'documentos';
    protected $primaryKey = 'id_documento';
    public $timestamps = true;
    protected $fillable = ['id_menu','id_funcionario','id_tipo','titulo', 'link_doc', 'data', 'id_tipo' , 'id_diretoria', 'id_sub_menu', 'id_sub_s_menu'];

    public function funcionario()
    {
        return $this->hasOne(CaUsuarios::class,'id_funcionario','id_funcionario');
    }
    public function diretoria()
    {
        return $this->hasOne(Diretoria::class,'id_diretoria','id_diretoria');
    }

    public function tipo()
    {
        return $this->hasOne(TiposDocumento::class,'id_tipo','id_tipo');
    }
}

