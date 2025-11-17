// ==========================================
// 1. INICIALIZAR EL SLIDER - SOLUCIÓN COMPLETA
// ==========================================

$(window).on('load', function(){
    
    if ($('.hero-slider').length) {
        
        // Destruir cualquier instancia previa
        if ($('.hero-slider').hasClass('slick-initialized')) {
            $('.hero-slider').slick('unslick');
        }
        
        // SOLUCIÓN: Establecer dimensiones explícitas ANTES de inicializar
        $('.hero-slider img').css({
            'width': '100%',
            'height': '100%',
            'object-fit': 'cover',
            'display': 'block'
        });
        
        // Inicializar Slick
        $('.hero-slider').slick({
            dots: true,
            infinite: true,
            speed: 800,
            fade: false,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 4000,
            arrows: true,
            pauseOnHover: true,
            cssEase: 'ease-in-out',
            adaptiveHeight: false,
            draggable: true,
            swipe: true,
            // CRÍTICO: Prevenir el recálculo automático de dimensiones
            waitForAnimate: false,
            // Mantener posición fija
            variableWidth: false
        });
        
        // Forzar dimensiones después de cada cambio de slide
        $('.hero-slider').on('afterChange', function(event, slick, currentSlide){
            $('.hero-slider img').css({
                'width': '100%',
                'height': '100%',
                'object-fit': 'cover'
            });
        });
        
        // Forzar recálculo inicial
        setTimeout(function() {
            $('.hero-slider').slick('setPosition');
            $('.hero-slider img').css({
                'width': '100%',
                'height': '100%',
                'object-fit': 'cover'
            });
        }, 100);
        
        console.log('Slider inicializado correctamente');
    }
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