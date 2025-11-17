// Espera a que TODO (imágenes, CSS, etc.) esté cargado
$(window).on('load', function(){
    
    // 1. INICIALIZAR EL SLIDER (Requisito 1)
    if ($('.hero-slider').length) {
        $('.hero-slider').slick({
            dots: true,       // Muestra los puntos de navegación
            infinite: true,   // El slider es un bucle infinito
            speed: 500,       // Velocidad de la transición
            fade: true,       // Efecto de desvanecimiento
            cssEase: 'linear',// Tipo de transición
            autoplay: true,   // Se mueve solo
            autoplaySpeed: 3000 // Cada 3 segundos
        });
    }

}); // Fin de window.load

// 2. VALIDACIÓN DE FORMULARIOS (Requisito 2)
// DOM: Usamos getElementById para encontrar el formulario
const formDatosCliente = document.getElementById('form-datos-cliente');

if (formDatosCliente) {
    // DOM: Usamos addEventListener para escuchar el evento 'submit'
    formDatosCliente.addEventListener('submit', function(event) {
        
        // Prevenimos que el formulario se envíe automáticamente
        event.preventDefault(); 
        
        if (validarFormularioCliente()) {
            // Si la validación es exitosa, enviamos el formulario
            this.submit();
        }
    });
}

function validarFormularioCliente() {
    let esValido = true;
    
    // DOM: Seleccionamos los inputs y spans de error
    const nombre = document.getElementById('nombre');
    const email = document.getElementById('email');
    const telefono = document.getElementById('telefono');
    
    // Definimos las Expresiones Regulares (RegEx)
    const emailRegex = /^\S+@\S+\.\S+$/; // Un email simple
    const telefonoRegex = /^\d{10}$/; // Espera 10 dígitos (ej. 8711234567)

    // --- Validación de Nombre (Vacío) ---
    if (nombre.value.trim() === '') {
        mostrarError('error-nombre', 'El nombre es obligatorio.');
        esValido = false;
    } else {
        ocultarError('error-nombre');
    }

    // --- Validación de Email (Vacío y RegEx) ---
    if (email.value.trim() === '') {
        mostrarError('error-email', 'El correo es obligatorio.');
        esValido = false;
    } else if (!emailRegex.test(email.value)) {
        mostrarError('error-email', 'El formato del correo no es válido.');
        esValido = false;
    } else {
        ocultarError('error-email');
    }

    // --- Validación de Teléfono (Vacío y RegEx) ---
    if (telefono.value.trim() === '') {
        mostrarError('error-telefono', 'El teléfono es obligatorio.');
        esValido = false;
    } else if (!telefonoRegex.test(telefono.value)) {
        mostrarError('error-telefono', 'El teléfono debe tener 10 dígitos.');
        esValido = false;
    } else {
        ocultarError('error-telefono');
    }

    // (Aquí deberías añadir las validaciones para calle, colonia, cp, etc.)

    return esValido;
}

// --- Funciones de ayuda para mostrar/ocultar errores (USO DE DOM) ---
function mostrarError(idSpan, mensaje) {
    // DOM: Usamos getElementById para encontrar el span
    const spanError = document.getElementById(idSpan);
    // DOM: Usamos textContent para cambiar el mensaje
    spanError.textContent = mensaje;
    // DOM: Usamos style.display para mostrar el mensaje
    spanError.style.display = 'block';
}

function ocultarError(idSpan) {
    const spanError = document.getElementById(idSpan);
    spanError.textContent = '';
    spanError.style.display = 'none';
}

// 3. FUNCIONALIDAD JS "LIBRE" (Botón Volver Arriba)
// DOM: Seleccionamos el botón
const btnVolverArriba = document.getElementById('btn-volver-arriba');

if (btnVolverArriba) {
    // DOM: Escuchamos el evento 'scroll' de la ventana
    window.onscroll = function() {
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
            // DOM: Manipulamos el estilo para mostrarlo
            btnVolverArriba.style.display = 'flex';
        } else {
            // DOM: Manipulamos el estilo para ocultarlo
            btnVolverArriba.style.display = 'none';
        }
    };

    // DOM: Escuchamos el evento 'click' en el botón
    btnVolverArriba.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth' // Desplazamiento suave
        });
    });
}