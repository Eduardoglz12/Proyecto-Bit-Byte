// ==========================================
// 1. INICIALIZAR EL SLIDER - SIN FADE (Solución al bug)
// ==========================================

$(window).on('load', function(){
    
    if ($('.hero-slider').length) {
        
        // Destruir cualquier instancia previa
        if ($('.hero-slider').hasClass('slick-initialized')) {
            $('.hero-slider').slick('unslick');
        }
        
        // Pequeño delay para asegurar que todo está cargado
        setTimeout(function() {
            $('.hero-slider').slick({
                dots: true,
                infinite: true,
                speed: 600,
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                arrows: true,
                pauseOnHover: true,
                cssEase: 'ease-in-out',
                adaptiveHeight: false,
                draggable: true,
                swipe: true,
                touchMove: true,
                waitForAnimate: false
            });
            
            console.log('Slider inicializado correctamente');
            
            // Verificar que funciona
            console.log('Total de slides:', $('.hero-slider .slick-slide:not(.slick-cloned)').length);
            console.log('Slide actual:', $('.hero-slider').slick('slickCurrentSlide'));
        }, 200);
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