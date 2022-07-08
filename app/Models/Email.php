<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Email extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $connection = 'sqlsrv';
    protected $table = 'dbo.Email';
    protected $primaryKey = 'Id_Email';
    public $timestamps = false;
}


