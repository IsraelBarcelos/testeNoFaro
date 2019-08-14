<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
	use SoftDeletes;

    protected $table = 'pessoas';

    protected $fillable = [
        'nome',
        'email',
        'ddd',
        'telefone'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];
}
