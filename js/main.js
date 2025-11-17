// ==========================================
// 1. INICIALIZAR EL SLIDER (CORREGIDO)
// ==========================================

$(document).ready(function(){
    
    // Verificamos que el slider exista
    if ($('.hero-slider').length) {
        
        // Método 1: Inicializar inmediatamente
        $('.hero-slider').slick({
            dots: true,           // Puntos de navegación
            infinite: true,       // Bucle infinito
            speed: 500,          // Velocidad de transición
            fade: true,          // Efecto fade
            cssEase: 'linear',   // Tipo de transición
            autoplay: true,      // Reproducción automática
            autoplaySpeed: 3000, // Cada 3 segundos
            arrows: true,        // Mostrar flechas
            adaptiveHeight: true // Ajusta altura automáticamente
        });
        
        // Opcional: Forzar recálculo después de que carguen las imágenes
        $('.hero-slider img').on('load', function() {
            $('.hero-slider').slick('setPosition');
        });
    }

}); // Fin de document.ready

// ==========================================
// 2. VALIDACIÓN DE FORMULARIOS
// ==========================================

const formDatosCliente = document.getElementById('form-datos-cliente');

if (formDatosCliente) {
    formDatosCliente.addEventListener('submit', function(event) {
        event.preventDefault(); 
        
        if (validarFormularioCliente()) {
            this.submit();
        }
    });
}

function validarFormularioCliente() {
    let esValido = true;
    
    const nombre = document.getElementById('nombre');
    const email = document.getElementById('email');
    const telefono = document.getElementById('telefono');
    
    const emailRegex = /^\S+@\S+\.\S+$/;
    const telefonoRegex = /^\d{10}$/;

    // Validación de Nombre
    if (nombre.value.trim() === '') {
        mostrarError('error-nombre', 'El nombre es obligatorio.');
        esValido = false;
    } else {
        ocultarError('error-nombre');
    }

    // Validación de Email
    if (email.value.trim() === '') {
        mostrarError('error-email', 'El correo es obligatorio.');
        esValido = false;
    } else if (!emailRegex.test(email.value)) {
        mostrarError('error-email', 'El formato del correo no es válido.');
        esValido = false;
    } else {
        ocultarError('error-email');
    }

    // Validación de Teléfono
    if (telefono.value.trim() === '') {
        mostrarError('error-telefono', 'El teléfono es obligatorio.');
        esValido = false;
    } else if (!telefonoRegex.test(telefono.value)) {
        mostrarError('error-telefono', 'El teléfono debe tener 10 dígitos.');
        esValido = false;
    } else {
        ocultarError('error-telefono');
    }

    return esValido;
}

function mostrarError(idSpan, mensaje) {
    const spanError = document.getElementById(idSpan);
    if (spanError) {
        spanError.textContent = mensaje;
        spanError.style.display = 'block';
    }
}

function ocultarError(idSpan) {
    const spanError = document.getElementById(idSpan);
    if (spanError) {
        spanError.textContent = '';
        spanError.style.display = 'none';
    }
}

// ==========================================
// 3. BOTÓN VOLVER ARRIBA
// ==========================================

const btnVolverArriba = document.getElementById('btn-volver-arriba');

if (btnVolverArriba) {
    window.onscroll = function() {
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
            btnVolverArriba.style.display = 'flex';
        } else {
            btnVolverArriba.style.display = 'none';
        }
    };

    btnVolverArriba.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}