addEventListener("DOMContentLoaded", () => {
   
    /*Validación fecha y hora inicio sea anterior a fecha y hora fin en creación de ronda */
    const startDateElement = document.getElementById('start_date');
    const endDateElement = document.getElementById('end_date');
    const startTimeElement = document.getElementById('start_time');
    const endTimeElement = document.getElementById('end_time');

    if (startDateElement) {
        startDateElement.addEventListener('change', function() {
            var startDate = this.value;
            var endDate = endDateElement ? endDateElement.value : null;
            var startTime = startTimeElement ? startTimeElement.value : null;
            var endTime = endTimeElement ? endTimeElement.value : null;
            checkDateTimeValidity(startDate, startTime, endDate, endTime);
        });
    }

    if (endDateElement) {
        endDateElement.addEventListener('change', function() {
            var endDate = this.value;
            var startDate = startDateElement ? startDateElement.value : null;
            var startTime = startTimeElement ? startTimeElement.value : null;
            var endTime = endTimeElement ? endTimeElement.value : null;
            checkDateTimeValidity(startDate, startTime, endDate, endTime);
        });
    }

    if (startTimeElement) {
        startTimeElement.addEventListener('change', function() {
            var startTime = this.value;
            var startDate = startDateElement ? startDateElement.value : null;
            var endDate = endDateElement ? endDateElement.value : null;
            var endTime = endTimeElement ? endTimeElement.value : null;
            checkDateTimeValidity(startDate, startTime, endDate, endTime);
        });
    }

    if (endTimeElement) {
        endTimeElement.addEventListener('change', function() {
            var endTime = this.value;
            var startDate = startDateElement ? startDateElement.value : null;
            var endDate = endDateElement ? endDateElement.value : null;
            var startTime = startTimeElement ? startTimeElement.value : null;
            checkDateTimeValidity(startDate, startTime, endDate, endTime);
        });
    }

     //Activar formulario edicion usuario
     if (document.getElementById("enableEdit")) {
        userUpdate(document.getElementById("enableEdit"));
    }
    if (document.querySelector('select[name="stage"]')) {
        showHideTopicsOnStageMenu(document.querySelector('select[name="stage"]'));
    }
    if (document.getElementById("programarCheckbox")) {
        showHideProgramming(document.getElementById("programarCheckbox"));
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

function checkDateTimeValidity(startDate, startTime, endDate, endTime) {
    if (startDate && endDate) {
        const startDateTime = new Date(`${startDate}T${startTime || '00:00'}`);
        const endDateTime = new Date(`${endDate}T${endTime || '00:00'}`);
        if (startDateTime > endDateTime) {
            startDateElement.setCustomValidity('La fecha y hora de inicio no puede ser posterior a la fecha y hora de fin.');
        } else {
            startDateElement.setCustomValidity('');
        }
    }
}
function showHideProgramming(checkbox){
    document.getElementById("programarCheckbox").addEventListener("change", function() {
        let isEnabled = this.checked;

        document.getElementById("start_date");
        document.getElementById("start_time");
        document.getElementById("end_date");
        document.getElementById("end_time"); 
        const programacionElement = document.getElementById("programacion");
        if (checkbox.checked) {
            programacionElement.style.display = "block";
            document.getElementById("start_date").disabled = false;
            document.getElementById("start_time").disabled = false;
            document.getElementById("end_date").disabled = false;
            document.getElementById("end_time").disabled = false;            
        } else {
            programacionElement.style.display = "none";
            document.getElementById("start_date").disabled = !isEnabled;
            document.getElementById("start_time").disabled = !isEnabled;
            document.getElementById("end_date").disabled = !isEnabled;
            document.getElementById("end_time").disabled = !isEnabled;
        }
    });
}
function showHideTopicsOnStageMenu (stage){
    stage.addEventListener('change', function() {
        var topicsAprobados = document.getElementById('lista-aprobados');
        var topicsFinalistas = document.getElementById('lista-finalistas');
        if (this.value == 'clasificatoria') {
            topicsAprobados.style.display = 'block';
            topicsAprobados.querySelector('select[name="topics[]"]').setAttribute('required', '');
            topicsFinalistas.style.display = 'none';
            topicsFinalistas.querySelector('select[name="topics[]"]').removeAttribute('required');
        } else if(this.value == 'propuestas') {
            topicsAprobados.style.display = 'none';
            topicsAprobados.querySelector('select[name="topics[]"]').removeAttribute('required');
            topicsFinalistas.style.display = 'none';
            topicsFinalistas.querySelector('select[name="topics[]"]').removeAttribute('required');
        }else if(this.value == 'final'){
            topicsAprobados.style.display = 'none';
            topicsAprobados.querySelector('select[name="topics[]"]').removeAttribute('required');
            topicsFinalistas.style.display = 'block';
            topicsFinalistas.querySelector('select[name="topics[]"]').setAttribute('required', '');
        }
    });
}

/* actualizar datos usuario */
function userUpdate(element){
    element.addEventListener("change", function() {
    let isEnabled = this.checked;

    document.getElementById("name").disabled = !isEnabled;
    document.getElementById("email").disabled = !isEnabled;
    document.getElementById("password").disabled = !isEnabled;
    document.getElementById("user_save").disabled = !isEnabled;
    document.getElementById("user_delete").disabled = !isEnabled;
});

}

function activarEliminar(){
    document.getElementById("deleteAll").toggleAttribute("disabled");

}