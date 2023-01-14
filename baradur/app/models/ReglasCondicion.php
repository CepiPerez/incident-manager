<?php

class ReglasCondicion extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'regla';

    protected $fillable = [
        'regla', 'valor', 'operador', 'minimo', 'maximo', 'igual', 'helper'
    ];

}