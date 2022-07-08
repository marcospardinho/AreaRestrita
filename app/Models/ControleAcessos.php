<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ControleAcessos extends Model
{
    use HasFactory,Notifiable;
    protected $connection = 'mysql';
    protected $table = 'controle_acessos';
    protected $primaryKey = 'Id_Cadastro';
    public $timestamps = ["created_at"];
    const UPDATED_AT = null;
    protected $fillable = ['Id_Cadastro', 'created_at'];

}
