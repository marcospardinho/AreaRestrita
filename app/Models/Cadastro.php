<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\CanResetPassword ;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Cadastro extends Authenticatable
{
    use HasFactory,Notifiable;
    protected $connection = 'sqlsrv';
    protected $table = 'dbo.Cadastro';
    protected $primaryKey = 'Id_Cadastro';
    public $timestamps = false;
    protected $fillable = ['foto'];

    public function siape()
    {
        return $this->hasMany(Siape::class,'Id_Cadastro','Id_Cadastro');
    }

    public function endereco()
    {
        return $this->hasOne(Endereco::class,'Id_Cadastro','Id_Cadastro');
    }

    public function email()
    {
        return $this->hasMany(Email::class,'Id_Cadastro','Id_Cadastro');
    }

    public function telefone()
    {
        return $this->hasMany(Telefone::class,'Id_Cadastro','Id_Cadastro');
    }

}
