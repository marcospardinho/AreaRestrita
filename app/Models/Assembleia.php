<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\CanResetPassword ;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Assembleia extends Authenticatable
{
    use HasFactory,Notifiable;
    protected $connection = 'mysql';
    protected $table = 'as_assembleia';
    protected $primaryKey = 'id_assembleia';
    public $timestamps = true;
    protected $fillable = ['titulo', 'descricao', 'inicio', 'fim', 'status'];

    public function enquetes()
    {
        return $this->hasMany(Enquete::class,'id_assembleia','id_assembleia');
    }

    public function permissao()
    {
        return $this->hasMany(UsuarioAssembleia::class,'id_assembleia','id_assembleia');
    }

    public function participantes()
    {
        return $this->hasMany(Participantes::class,'id_assembleia','id_assembleia');
    }

}
