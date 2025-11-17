
$(document).ready(function(){
    $('.hero-slider').slick({
        autoplay: true,
        autoplaySpeed: 3000,
        dots: true,
        arrows: true,        
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        
        responsive: [
            {
                breakpoint: 769,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false
                }
            },
            {
                breakpoint: 481,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false
                }
            }
        ]
    });
});


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
    
    // --- 1. Seleccionar TODOS los inputs ---
    const nombre = document.getElementById('nombre');
    const email = document.getElementById('email');
    const telefono = document.getElementById('telefono');
    const calle = document.getElementById('calle');
    const colonia = document.getElementById('colonia');
    const cp = document.getElementById('cp');
    const ciudad = document.getElementById('ciudad');
    const estado = document.getElementById('estado');
    
    // --- 2. Definir Expresiones Regulares ---
    const emailRegex = /^\S+@\S+\.\S+$/; // Email simple
    const telefonoRegex = /^\d{10}$/; // Teléfono de 10 dígitos
    const cpRegex = /^\d{5}$/; // Código Postal de 5 dígitos
    const textoRegex = /^[a-zA-Z\s]+$/; // Solo letras y espacios

    // --- 3. Validar cada campo ---

    // Nombre (Vacío)
    if (nombre && nombre.value.trim() === '') {
        mostrarError('error-nombre', 'El nombre es obligatorio.');
        esValido = false;
    } else if (nombre) {
        ocultarError('error-nombre');
    }

    // Email (Vacío y RegEx)
    if (email && email.value.trim() === '') {
        mostrarError('error-email', 'El correo es obligatorio.');
        esValido = false;
    } else if (email && !emailRegex.test(email.value)) {
        mostrarError('error-email', 'El formato del correo no es válido.');
        esValido = false;
    } else if (email) {
        ocultarError('error-email');
    }

    // Teléfono (Vacío y RegEx)
    if (telefono && telefono.value.trim() === '') {
        mostrarError('error-telefono', 'El teléfono es obligatorio.');
        esValido = false;
    } else if (telefono && !telefonoRegex.test(telefono.value)) {
        mostrarError('error-telefono', 'El teléfono debe tener 10 dígitos.');
        esValido = false;
    } else if (telefono) {
        ocultarError('error-telefono');
    }

    // Calle (Vacío)
    if (calle && calle.value.trim() === '') {
        mostrarError('error-calle', 'La calle es obligatoria.');
        esValido = false;
    } else if (calle) {
        ocultarError('error-calle');
    }

    // Colonia (Vacío)
    if (colonia && colonia.value.trim() === '') {
        mostrarError('error-colonia', 'La colonia es obligatoria.');
        esValido = false;
    } else if (colonia) {
        ocultarError('error-colonia');
    }

    // Código Postal (Vacío y RegEx)
    if (cp && cp.value.trim() === '') {
        mostrarError('error-cp', 'El C.P. es obligatorio.');
        esValido = false;
    } else if (cp && !cpRegex.test(cp.value)) {
        mostrarError('error-cp', 'El C.P. debe tener 5 dígitos.');
        esValido = false;
    } else if (cp) {
        ocultarError('error-cp');
    }

    // Ciudad (Vacío y RegEx opcional)
    if (ciudad && ciudad.value.trim() === '') {
        mostrarError('error-ciudad', 'La ciudad es obligatoria.');
        esValido = false;
    } else if (ciudad && !textoRegex.test(ciudad.value)) {
        mostrarError('error-ciudad', 'La ciudad solo debe contener letras.');
        esValido = false;
    } else if (ciudad) {
        ocultarError('error-ciudad');
    }

    // Estado (Vacío y RegEx opcional)
    if (estado && estado.value.trim() === '') {
        mostrarError('error-estado', 'El estado es obligatorio.');
        esValido = false;
    } else if (estado && !textoRegex.test(estado.value)) {
        mostrarError('error-estado', 'El estado solo debe contener letras.');
        esValido = false;
    } else if (estado) {
        ocultarError('error-estado');
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


// DOM: Usamos getElementById para encontrar el formulario de tarjeta
const formTarjeta = document.getElementById('form-tarjeta');

if (formTarjeta) {
    // DOM: Usamos addEventListener para escuchar el evento 'submit'
    formTarjeta.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevenimos el envío
        if (validarFormularioTarjeta()) {
            this.submit(); // Si todo está bien, enviamos
        }
    });
}

function validarFormularioTarjeta() {
    let esValido = true;

    // DOM: Seleccionamos todos los inputs
    const nombre = document.getElementById('nombre_tarjeta');
    const numero = document.getElementById('numero_tarjeta');
    const mes = document.getElementById('mes_vencimiento');
    const ano = document.getElementById('ano_vencimiento');
    const cvv = document.getElementById('cvv');

    // Reglas de Expresión Regular
    const nombreRegex = /^[a-zA-Z\s]+$/; // Solo letras y espacios
    const numeroRegex = /^\d{16}$/; // Exactamente 16 dígitos
    const cvvRegex = /^\d{3,4}$/; // 3 o 4 dígitos

    // Limpiamos los espacios del número de tarjeta
    const numeroLimpio = numero.value.replace(/\s+/g, '');

    // 1. Validar Nombre (Vacío y RegEx)
    if (nombre && nombre.value.trim() === '') {
        mostrarError('error-nombre-tarjeta', 'El nombre es obligatorio.');
        esValido = false;
    } else if (nombre && !nombreRegex.test(nombre.value)) {
        mostrarError('error-nombre-tarjeta', 'El nombre solo debe contener letras.');
        esValido = false;
    } else if (nombre) {
        ocultarError('error-nombre-tarjeta');
    }

    // 2. Validar Número (Vacío y RegEx)
    if (numero && numeroLimpio === '') {
        mostrarError('error-numero-tarjeta', 'El número de tarjeta es obligatorio.');
        esValido = false;
    } else if (numero && !numeroRegex.test(numeroLimpio)) {
        mostrarError('error-numero-tarjeta', 'Debe ser un número de 16 dígitos.');
        esValido = false;
    } else if (numero) {
        ocultarError('error-numero-tarjeta');
    }

    // 3. Validar CVV (Vacío y RegEx)
    if (cvv && cvv.value.trim() === '') {
        mostrarError('error-cvv', 'El CVV es obligatorio.');
        esValido = false;
    } else if (cvv && !cvvRegex.test(cvv.value)) {
        mostrarError('error-cvv', 'Debe tener 3 o 4 dígitos.');
        esValido = false;
    } else if (cvv) {
        ocultarError('error-cvv');
    }

    // 4. Validar Fecha de Vencimiento
    const fechaActual = new Date();
    const anoActual = fechaActual.getFullYear();
    const mesActual = fechaActual.getMonth() + 1;

    const anoTarjeta = parseInt(ano.value);
    const mesTarjeta = parseInt(mes.value);

    // Comprueba si el año es menor que el actual, O si es el mismo año pero el mes ya pasó
    if (ano && mes && (anoTarjeta < anoActual || (anoTarjeta === anoActual && mesTarjeta < mesActual))) {
        mostrarError('error-fecha', 'La tarjeta ha expirado.');
        esValido = false;
    } else if (ano && mes) {
        ocultarError('error-fecha');
    }

    return esValido;
}


// DOM: Usamos getElementById para encontrar el formulario de login
const formLogin = document.getElementById('form-login');

if (formLogin) {
    // DOM: Escuchamos el evento 'submit'
    formLogin.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevenimos el envío
        
        if (validarFormularioLogin()) {
            this.submit(); // Si es válido, enviamos
        }
    });
}

function validarFormularioLogin() {
    let esValido = true;
    
    // DOM: Seleccionamos los inputs
    const usuario = document.getElementById('usr_user');
    const password = document.getElementById('usr_password');

    // --- Validación de Usuario (Vacío) ---
    if (usuario && usuario.value.trim() === '') {
        mostrarError('error-usuario', 'El nombre de usuario es obligatorio.');
        esValido = false;
    } else if (usuario) {
        ocultarError('error-usuario');
    }

    // --- Validación de Contraseña (Vacío) ---
    if (password && password.value.trim() === '') {
        mostrarError('error-password', 'La contraseña es obligatoria.');
        esValido = false;
    } else if (password) {
        ocultarError('error-password');
    }

    return esValido;
}

// DOM: Usamos getElementById para encontrar el formulario de registro
const formRegistro = document.getElementById('form-registro');

if (formRegistro) {
    // DOM: Escuchamos el evento 'submit'
    formRegistro.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevenimos el envío
        
        if (validarFormularioRegistro()) {
            this.submit(); // Si es válido, enviamos
        }
    });
}

function validarFormularioRegistro() {
    let esValido = true;
    
    // DOM: Seleccionamos los inputs
    const usuario = document.getElementById('usr_user');
    const password = document.getElementById('usr_password');
    const passwordConfirm = document.getElementById('usr_password_confirm');

    // --- Validación de Usuario (Vacío y Longitud) ---
    if (usuario && usuario.value.trim() === '') {
        mostrarError('error-usuario', 'El nombre de usuario es obligatorio.');
        esValido = false;
    } else if (usuario && usuario.value.trim().length < 4) {
        mostrarError('error-usuario', 'El usuario debe tener al menos 4 caracteres.');
        esValido = false;
    } else if (usuario) {
        ocultarError('error-usuario');
    }

    // --- Validación de Contraseña (Vacío y Longitud) ---
    if (password && password.value.trim() === '') {
        mostrarError('error-password', 'La contraseña es obligatoria.');
        esValido = false;
    } else if (password && password.value.trim().length < 6) {
        mostrarError('error-password', 'La contraseña debe tener al menos 6 caracteres.');
        esValido = false;
    } else if (password) {
        ocultarError('error-password');
    }

    // --- Validación de Confirmar Contraseña (Vacío y Coincidencia) ---
    if (passwordConfirm && passwordConfirm.value.trim() === '') {
        mostrarError('error-password-confirm', 'Por favor, confirma tu contraseña.');
        esValido = false;
    } else if (password && passwordConfirm && password.value.trim() !== passwordConfirm.value.trim()) {
        mostrarError('error-password-confirm', 'Las contraseñas no coinciden.');
        esValido = false;
    } else if (passwordConfirm) {
        ocultarError('error-password-confirm');
    }

    return esValido;
}

const formPerfil = document.getElementById('form-perfil');

if (formPerfil) {
    formPerfil.addEventListener('submit', function(event) {
        event.preventDefault();
        
        if (validarFormularioCliente()) {
            this.submit();
        }
    });
}