<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\CanResetPassword ;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Acervos extends Authenticatable
{
    use HasFactory,Notifiable;
    protected $connection = 'mysql';
    protected $table = 'ca_acervo';
    protected $primaryKey = 'id_acervo';
    public $timestamps = true;
    protected $fillable = ['descricao'];

    public function divisao()
    {
        return $this->hasMany(Divisao::class,'id_acervo','id_acervo')->orderby('created_at', 'asc');
    }

}
