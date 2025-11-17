// ==========================================
// 1. INICIALIZAR EL SLIDER - SOLUCIÓN CORRECTA
// ==========================================

$(document).ready(function(){
    $('.hero-slider').slick({
        autoplay: true,          // Reproducción automática
        autoplaySpeed: 3000,     // Velocidad en ms (3 segundos)
        dots: true,              // Mostrar puntos de navegación
        arrows: true,            // Mostrar flechas
        infinite: true,          // Bucle infinito
        slidesToShow: 1,         // Mostrar 1 slide a la vez
        slidesToScroll: 1        // Desplazar 1 slide
    });
});


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

    if (nombre && nombre.value.trim() === '') {
        mostrarError('error-nombre', 'El nombre es obligatorio.');
        esValido = false;
    } else if (nombre) {
        ocultarError('error-nombre');
    }

    if (email && email.value.trim() === '') {
        mostrarError('error-email', 'El correo es obligatorio.');
        esValido = false;
    } else if (email && !emailRegex.test(email.value)) {
        mostrarError('error-email', 'El formato del correo no es válido.');
        esValido = false;
    } else if (email) {
        ocultarError('error-email');
    }

    if (telefono && telefono.value.trim() === '') {
        mostrarError('error-telefono', 'El teléfono es obligatorio.');
        esValido = false;
    } else if (telefono && !telefonoRegex.test(telefono.value)) {
        mostrarError('error-telefono', 'El teléfono debe tener 10 dígitos.');
        esValido = false;
    } else if (telefono) {
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