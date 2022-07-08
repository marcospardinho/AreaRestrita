<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Endereco extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'sqlsrv';
    protected $table = 'dbo.Endereco';
    protected $primaryKey = 'Id_Endereco';
    public $timestamps = false;


}
