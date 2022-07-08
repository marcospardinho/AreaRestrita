<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Funcionario extends Authenticatable
{
    use HasFactory,Notifiable;
    protected $connection = 'mysql';
    protected $table = 'funcionarios';
    protected $primaryKey = 'id_funcionario';
    public $timestamps = true;
    protected $fillable = ['id_setor','nome_funcionario','usuario','senha', 'cpf', 'foto','email'];


    public function setor()
    {
        return $this->hasOne(Departamentos::class,'id_setor','id_setor');
    }
}
