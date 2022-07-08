<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DocsComplementares extends Authenticatable
{
    use HasFactory,Notifiable;
    protected $connection = 'mysql';
    protected $table = 'dados_complementares';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['id','id_novo_filiado','data_adimissao', 'data_aposentadoria', 'siape_instituidor', 'nome_instituidor' , 'data_obito_instituidor', 'created_at', 'updated_at'];


}
