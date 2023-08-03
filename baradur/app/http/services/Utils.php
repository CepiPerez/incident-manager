<?php

Class Utils
{

    public static function sla_expiration($horas, $sla)
    {
        if ($sla==0)
        {
            return [
                'expired' => false, 
                'text' => 'Incidente sin SLA',
                'hours' => 1000
            ];
        }

        /* $dateDiff = ($horas - $sla) *60;
        $expired = $dateDiff > 0;
        
        $day = abs(intval($dateDiff / 60 / 24));
        $hrs = abs(intval($dateDiff / 60) + ($expired?$day*24:-$day*24));
        $min = abs(intval($dateDiff % 60));
        
        $str = $day!=0? ($day==1? "1 día" : "$day días") : '';
        $str.= ($day==0 && $hrs!=0)? ($hrs==1? "1 hora " : "$hrs horas ") : '';
        $str.= ($day==0 && $min!=0)? ($min==1? "1 minuto" : "$min minutos") : '';

        if ($str=='') $str = 'menos de 1 minuto';

        return [
            'expired' => $expired, 
            'text' => ($expired? 'Vencido hace ' : 'Vence en '). $str,
            'hours' => $hrs
        ]; */

        $dateDiff = ($horas - $sla) *60;
        $expired = $dateDiff > 0;
        $created = $expired? now()->subMinutes(abs($dateDiff)) : now()->addMinutes(abs($dateDiff));

        return  [
            'expired' => $expired, 
            'text' => ($expired? 'Vencido ':'Vence '). now()->diffForHumans($created, Carbon::DIFF_RELATIVE_TO_NOW, true, 1),// $created->diffForHumans( $expired? [] : ['parts' => 2], Carbon::DIFF_RELATIVE_TO_NOW ),
            'hours' => $created->diffInHours()
        ];

    }

    public static function check_img($value)
    {
        if (!file_exists($value)) 
            return false;

        $allowedMimeTypes = ['image/jpeg','image/webp','image/png','image/svg+xml'];
        $contentType = mime_content_type($value);
        
        return in_array($contentType, $allowedMimeTypes);

    }

    public static function get_icon_svg($value)
    {
        $array = explode('.', $value);
        $extension = end($array);
        $val = 'txt';

        if (in_array($extension, ['xls', 'xlsx', 'csv'])) 
            $val = 'xls';

        elseif (in_array($extension, ['doc', 'docx'])) 
            $val = 'doc';
        
        elseif (in_array($extension, ['ppt', 'pptx'])) 
            $val = 'ppt';

        elseif (in_array($extension, ['rar', 'zip'])) 
            $val = 'zip';

        elseif ($extension=='pdf') 
            $val = 'pdf';

        return asset("assets/icons/$val.svg");

    }

}