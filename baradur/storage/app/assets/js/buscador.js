// ----------------------------------------------------
// Buscador con botones de limpiar y buscar
// (C) 2021. Matias Perez para New Rol IT
// ----------------------------------------------------
// form = el id del formulario padre 
// (necesario para poder hacer el submit)
//
// valor = el valor que tiene el buscador 
// (si no se viene de una busqueda deberia estar vacio)
//
// NOTA: Es necesario el custom.css para los iconos
// ----------------------------------------------------

class Buscador extends HTMLElement
{
    constructor() {
        super();

        var myid = $(this).attr("id")? $(this).attr("id") : 'buscador';
        var buscarval = $(this).attr("valor") ? $(this).attr("valor") : '';
        var placeholder = $(this).attr("placeholder") ? $(this).attr("placeholder") : '';
        var formulario = $(this).attr("form");
        //console.log("Inicializando buscador: " + myid + " - valor: " + buscarval);

        //this.classList.add("mt-3");
        this.classList.add("pb-2");
        this.classList.add("mb-1");
        this.setAttribute("id", myid);
    
        var inputtext = document.createElement('input');
        

        inputtext.classList.add("border");
        inputtext.classList.add("shadow");
        inputtext.classList.add("dark:bg-zinc-800");
        inputtext.classList.add("dark:text-zinc-100");
        inputtext.classList.add("text-slate-700");
        inputtext.classList.add("rounded");
        inputtext.classList.add("w-full");
        inputtext.classList.add("py-1.5");
        inputtext.classList.add("px-3");
        inputtext.classList.add("border-slate-300");
        inputtext.classList.add("dark:border-zinc-600");
        inputtext.classList.add("focus:border-sky-500");
        inputtext.classList.add("focus:ring");
        inputtext.classList.add("focus:outline-none");

        //inputtext.classList.add("text");
        inputtext.setAttribute("placeholder", placeholder);
        inputtext.setAttribute("name", "buscar");
        inputtext.setAttribute("value", buscarval);
        $('#'+myid).append(inputtext);
    
        var limpiar = document.createElement('a');
        limpiar.classList.add("fa-times-circle");
        limpiar.classList.add("hidden");
        limpiar.classList.add("dark:text-slate-700");
        limpiar.setAttribute("id", "limpiar");
        limpiar.setAttribute("href", "");
        limpiar.innerHTML = '<svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256zM175 208.1L222.1 255.1L175 303C165.7 312.4 165.7 327.6 175 336.1C184.4 346.3 199.6 346.3 208.1 336.1L255.1 289.9L303 336.1C312.4 346.3 327.6 346.3 336.1 336.1C346.3 327.6 346.3 312.4 336.1 303L289.9 255.1L336.1 208.1C346.3 199.6 346.3 184.4 336.1 175C327.6 165.7 312.4 165.7 303 175L255.1 222.1L208.1 175C199.6 165.7 184.4 165.7 175 175C165.7 184.4 165.7 199.6 175 208.1V208.1z"/></svg>';
        $('#'+myid).append(limpiar);
    
        var separador = document.createElement('p');
        separador.classList.add("separator");
        separador.classList.add("hidden");
        separador.innerHTML = "|"
        $('#'+myid).append(separador);
    
        var buscar = document.createElement('a');
        buscar.classList.add("fa-search");
        buscar.classList.add("disabled");
        buscar.setAttribute("id", "buscar");
        buscar.setAttribute("href", "");
        buscar.innerHTML = '<svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M500.3 443.7l-119.7-119.7c27.22-40.41 40.65-90.9 33.46-144.7C401.8 87.79 326.8 13.32 235.2 1.723C99.01-15.51-15.51 99.01 1.724 235.2c11.6 91.64 86.08 166.7 177.6 178.9c53.8 7.189 104.3-6.236 144.7-33.46l119.7 119.7c15.62 15.62 40.95 15.62 56.57 0C515.9 484.7 515.9 459.3 500.3 443.7zM79.1 208c0-70.58 57.42-128 128-128s128 57.42 128 128c0 70.58-57.42 128-128 128S79.1 278.6 79.1 208z"/></svg>';
        $('#'+myid).append(buscar);
    
    
        if ($(inputtext).val()!=="")
        {
            $(limpiar).removeClass("hidden");
            $(separador).removeClass("hidden");
        }
    
        $(limpiar).click(function(e) {
            e.preventDefault()
            $(inputtext).val("");
            $(buscar).addClass("disabled");
            $(limpiar).addClass("hidden");
            $(separador).addClass("hidden");
            $(inputtext).focus();
            if (buscarval!=="") {
                //$(inputtext).removeAttr("name");
                $('#'+formulario).submit();
            }
        });
    
        $(inputtext).on('change keyup paste', function () {
            if ($(inputtext).val()=="" && buscarval=="") {
              $(buscar).addClass("disabled");
              $(limpiar).addClass("hidden");
              $(separador).addClass("hidden");
            } else {
              $(buscar).removeClass("disabled");
              $(limpiar).removeClass("hidden");
              $(separador).removeClass("hidden");
            }
        });
        
        $(buscar).click(function(e) {
            e.preventDefault()
            $('#'+formulario).submit();
        });
    
    }

}
customElements.define('mi-buscador', Buscador);

