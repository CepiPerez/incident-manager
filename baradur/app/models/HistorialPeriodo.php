<?php

class HistorialPeriodo extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'incidente',
        'periodo',
        'grupo',
        'asignado',
        'status'
    ];

    public function inc_asignado()
    {
        return $this->hasOne(User::class, 'Usuario', 'asignado');
    }

    public function avances()
    {
        return $this->hasMany(Avance::class, 'incidente', 'incidente')
            ->selectRaw('avances.*, ta.descripcion as tipo_desc, ta.visible, gr.descripcion as grupo_desc')
            ->leftJoin('tipo_avances as ta', 'ta.codigo', '=', 'avances.tipo_avance')
            ->leftJoin('grupos as gr', 'gr.codigo', '=', 'avances.grupo_destino')
            ->where('tipo_avance', '<', 101)
            ->orderBy('fecha_ingreso');
    }

    public function avances_estimado()
    {
        return $this->hasOne(Avance::class, 'incidente', 'incidente')
                ->selectRaw('incidente, fecha_ingreso, tipo_avance, descripcion')
                ->where('tipo_avance', 8);
    }

}