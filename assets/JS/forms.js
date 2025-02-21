addEventListener("DOMContentLoaded", () => {
   
    /*Validación fecha inicio sea anterior a fecha fin en creación de ronda */
    if ( document.getElementById('start_date')) {
        startDateElement.addEventListener('change', function() {
            var startDate = this.value;
            var endDate = endDateElement ? endDateElement.value : null;
            checkDateValidity(startDate, endDate);
        });
    }

    if (document.getElementById('end_date')) {
        endDateElement.addEventListener('change', function() {
            var endDate = this.value;
            var startDate = startDateElement ? startDateElement.value : null;
            checkDateValidity(startDate, endDate);
        });
    }
    
    /*Validación de correo */
    document.querySelectorAll("[name='email']").forEach(emailInput => {
        emailInput.addEventListener("change", function () {
            validEmailPattern(emailInput);
        });
        emailInput.addEventListener("input", function () {
            validEmailPattern(emailInput);
        });
    });
});
/*Validación de correo */
function usedEmail(event, form) {
    event.preventDefault();
    const emailInput = form.querySelector("[name='email']");
    const email = emailInput.value.trim();
    if (email.length > 5) {
        fetch(base_url + "functions/verificar_email.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "email=" + encodeURIComponent(email)
        })
            .then(response => response.text())
            .then(data => {
                const isLogin = form.action.value === "login"; // Si el formulario no es login entonces es register
                const emailExists = data.trim() === "true"; // Comprobamos si el email existe en la base de datos
                const message = emailExists ? "Este email ya está registrado. Intenta con otro." : "Este email no está registrado. Intenta con otro.";
                // Si es login y el email existe o si es register y el email no existe, entonces no se envía el formulario
                if ((isLogin && emailExists) || (!isLogin && !emailExists)) {
                    emailInput.setCustomValidity("");
                    form.submit();
                } else {
                    emailInput.setCustomValidity(message);
                    emailInput.reportValidity();
                }
            })
            .catch(error => {
                console.error("Error en la validación del email:", error);
            });
    } else {
        emailInput.setCustomValidity("El email es demasiado corto.");
        emailInput.reportValidity();
    }
}

function validEmailPattern(email) {
    let pattern = /^[^\s@]+@[^\s@]+\.[a-z]{2,}$/i; // Permite dominios de más de 3 letras y no usa espacios

    if (!pattern.test(email.value)) { // Verifica si el correo es válido
        email.setCustomValidity("Por favor, ingrese un email válido en el formato: ejemplo@dominio.com");
        email.classList.add("is-invalid");
    } else {
        email.setCustomValidity("");
        email.classList.remove("is-invalid");
    }
}

function checkDateValidity(startDate, endDate){
        if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
            this.setCustomValidity('La fecha de inicio no puede ser posterior a la fecha de fin.');
        } else {
            this.setCustomValidity('');
        }
}
