document.addEventListener("DOMContentLoaded", function ()
{
    let form = document.getElementById('formUsuarios') || document.getElementById('formEditarUsuarios');
    if(!form) return;

    form.addEventListener("submit", function(event) {
        let dni = document.getElementById("dni").value.trim();
        let email = document.getElementById("email").value.trim();
        let password = document.getElementById("pass").value.trim();
    
        let dniRegex = /^\d{8}[A-Z]$/;
        let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        let passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    
    
        if (!dniRegex.test(dni)) {
            alert("El DNI debe tener 8 números seguidos de una letra mayúscula.");
            event.preventDefault();
            return;
        }
    
        if (!emailRegex.test(email)) {
            alert("Por favor, introduce un correo electrónico válido.");
            event.preventDefault();
            return;
        }
    
        if (form.id === "formUsuarios" || (form.id === "formEditarUsuarios" && password.length > 0)) {
            if (!passwordRegex.test(password)) {
                alert("La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.");
                event.preventDefault();
                return;
            }
        }
    });
});