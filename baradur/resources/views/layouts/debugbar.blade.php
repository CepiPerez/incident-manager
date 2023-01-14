<!DOCTYPE html>
<html lang="es">

<body>

    <div class="row bg-danger">
        <div class="col">
            <p>HOLA MUNDO</p>
        </div>
        <div class="col-auto">
            @php
                function convert($size)
                {
                    $unit=array('b','kb','mb','gb','tb','pb');
                    return round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
                }  
                echo '<p>Mem: '. convert(memory_get_usage(true)) . '</p>';
            @endphp

        </div>



    </div>

</body>
</html>