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
