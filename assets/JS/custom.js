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

    
    //Confirmar usuarioadministrador edicion usuario
    if (document.getElementById("confirmAdminModal")) {
        setupAdminModal(document.getElementById("confirmAdminModal"));
    }
    if (document.getElementById("viewTopicInfo")) {
        viewTopicInfo(document.getElementById("viewTopicInfo"));
    }
});


/*Crear Cookie para pestañas en temas y rondas */
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



/*Modal de confirmación para dar o quitar permisos administrador a un usuario */
function setupAdminModal(modal) {

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

function viewTopicInfo(modal) {

    modal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;

        var title = button.getAttribute('data-topic-title');  
        var created_at = button.getAttribute('data-topic-date');
        var topic = button.getAttribute('data-topic-topic');
        var description = button.getAttribute('data-topic-description');

        // Actualizar los campos dentro del modal
        document.getElementById('topicTitle').textContent = title || "Sin título";
        document.getElementById('topicCreatedAt').textContent = created_at || "Sin fecha";
        document.getElementById('topicTopic').textContent = topic || "Sin tema";
        document.getElementById('topicDescription').textContent = description || "Sin descripción";
    });
}



