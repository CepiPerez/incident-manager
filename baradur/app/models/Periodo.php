<?php

class Periodo extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'codigo';
    
    protected $fillable = [
        'descripcion', 'desde', 'hasta'
    ];
    
}