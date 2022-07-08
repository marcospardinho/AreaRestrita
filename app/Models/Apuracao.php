<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\CanResetPassword ;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Apuracao extends Authenticatable
{
    use HasFactory,Notifiable;
    protected $connection = 'mysql';
    protected $table = 'as_apuracao';
    protected $primaryKey = 'id_apuracao';
    public $timestamps = true;
    protected $fillable = ['id_enquete', 'id_opcao', 'id_participante'];

}
