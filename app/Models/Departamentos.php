<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Departamentos extends Authenticatable
{
    use HasFactory,Notifiable;
    protected $connection = 'mysql';
    protected $table = 'setores';
    protected $primaryKey = 'id_setor';
    public $timestamps = false;


    public function diretorias()
    {
        return $this->belongsToMany(Diretoria::class,'grupos_acessos','id_setor','id_diretoria');
    }


}
