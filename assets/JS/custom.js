const base_url = document.body.getAttribute("data-baseurl");
addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll("#rondasTabs .nav-link").forEach(link => {
        link.addEventListener("click", function () {
            createCookie(this, "rondas");
        });
    }); 
    document.querySelectorAll("#temasTabs .nav-link").forEach(link => {
        link.addEventListener("click", function () {
            createCookie(this, "temas");
        });
    }); 
    document.querySelectorAll("[name='email']").forEach(emailInput => {
        emailInput.addEventListener("change", function () {
            validEmailPattern(emailInput);
        });
    }); 
    
    setupAdminModal('confirmAdminModal');

   
});

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
    let pattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if (!email.value.match(pattern) || email.value.length === 0) {
        email.setCustomValidity("Por favor, ingrese un email válido en el formato: ejemplo@dominio.com");
        email.classList.add("is-invalid");
        email.addEventListener("input", function () {
            validEmailPattern(email);
            email.setCustomValidity("Por favor, ingrese un email válido en el formato: ejemplo@dominio.com");
        });
    } else {
        email.setCustomValidity("");
        email.classList.remove("is-invalid");
    }
}
function createCookie (element, page) {
    let tab = element.hash.slice(1);
    fetch(base_url + "functions/crear_cookie.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "tab=" + encodeURIComponent(tab) + "&page=" + encodeURIComponent(page)
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
    })
    .catch(error => {
        console.error("Error en la creación de la cookie:", error);
    });
}

/* actualizar datos usuario */

document.getElementById("enableEdit").addEventListener("change", function() {
    let isEnabled = this.checked;

    document.getElementById("name").disabled = !isEnabled;
    document.getElementById("email").disabled = !isEnabled;
    document.getElementById("password").disabled = !isEnabled;
    document.getElementById("user_save").disabled = !isEnabled;
    document.getElementById("user_delete").disabled = !isEnabled;
});



    function setupAdminModal(modalId) {
        var modal = document.getElementById(modalId);

        modal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var userId = button.getAttribute('data-userid');
            var username = button.getAttribute('data-username');
            var isAdmin = button.getAttribute('data-isadmin');

            // Actualizar el nombre del usuario en el modal
            document.getElementById('modalUserId').value = userId;
            document.getElementById('modalIsAdmin').value = isAdmin;
            document.getElementById('modalIsAdmin').value = isAdmin == 1 ? 0 : 1;


            // Modificar el mensaje según la acción
            if (isAdmin == 1) {
                document.getElementById('modalMessage').innerHTML =
                    "¿Estás seguro de que deseas quitar los permisos de administrador a <strong>" + username + "</strong>?";
                document.getElementById('modalWarning').innerHTML =
                    "Esta acción revocará sus privilegios de administrador, impidiendo que gestione usuarios, modifique contenido y realice cambios en la configuración. Asegúrate de que esta es la decisión correcta antes de continuar.";
                document.getElementById('modalConfirmButton').textContent = "Sí, quitar permisos de administrador";
            } else {
                document.getElementById('modalMessage').innerHTML =
                    "¿Estás seguro de que deseas hacer administrador a <strong>" + username + "</strong>?";
                document.getElementById('modalWarning').innerHTML =
                    "Otorgar permisos de administrador a este usuario le dará control total sobre la aplicación, incluyendo la gestión de usuarios, modificaciones de contenido y ajustes críticos del sistema. Asegúrate de confiar plenamente en esta persona antes de continuar.";
                document.getElementById('modalConfirmButton').textContent = "Sí, hacer administrador";
            }
        });
    }


