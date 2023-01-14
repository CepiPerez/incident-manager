<?php

class AsignacionesCondicion extends Model
{    
    public $timestamps = false;

    protected $primaryKey = 'regla';

    protected $fillable = [
        'regla', 'condicion', 'valor', 'helper'
    ];
}
