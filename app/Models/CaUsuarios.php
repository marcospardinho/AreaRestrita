<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CaUsuarios extends Authenticatable
{
    use HasFactory,Notifiable;
    protected $connection = 'mysql';
    protected  $guard = 'admin';
    protected $table = 'funcionarios';
    protected $primaryKey = 'id_funcionario';
    public $timestamps = false;

    public function departamento()
    {
        return $this->hasOne(Departamentos::class,'id_setor','id_setor');
    }

}
