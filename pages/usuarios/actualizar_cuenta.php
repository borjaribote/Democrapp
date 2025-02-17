<?php
require_once '../../includes/header.php';
accesoAutorizado('usuario');
require_once BASE_PATH.'controladores/controlador_usuarios.php';
require_once BASE_PATH.'/functions/gestion_mensajes.php';
// Obtener datos del usuario
$user = consultarUsuario($_SESSION['user_id']);
?>
<section class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card mt-5 p-3">
                <div class="card-body">
                    <h2 class="card-title text-center">Editar Perfil</h2>

                    <!-- Checkbox para activar/desactivar los campos -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="enableEdit">
                        <label class="form-check-label" for="enableEdit">Habilitar edición</label>
                    </div>

                    <form action="<?= BASE_URL ?>controladores/controlador_usuarios.php" method="post">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="page" value="actualizar_cuenta">
                        <input type="hidden" name="id" value="<?= $_SESSION['user_id'] ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" id="name" name="username" class="form-control" 
                                   value="<?= $user['username']; ?>" disabled required>
                        </div>
                        <divclass="mb-3">
                            <label for="email" class="form-label">Correo</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?= $user['email']; ?>" disabled required>
                        </divclass=>
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva contraseña (opcional)</label>
                            <input type="password" id="password" name="password" class="form-control" disabled>
                        </div>
                        <button type="submit" id="user_save" class="btn btn-success w-100" disabled> Guardar Cambios</button>         
                    </form>
                    <?php if (!isset($_SESSION['is_admin'])): ?>
                        <button id="user_delete" type="button" class="btn btn-danger w-100 mt-3" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" disabled> Eliminar Cuenta</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel"><i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <span>
                    ¿Estás seguro de que deseas eliminar tu cuenta? Esta acción es irreversible.
                </span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" action="<?= BASE_URL ?>controladores/controlador_usuarios.php" method="post">
                    <input type="hidden" name="id" value="<?= $_SESSION['user_id'] ?>">
                    <input type="hidden" name="email" value="<?= $_SESSION['user_email'] ?>">
                    <button type="submit" name="action" value="delete" class="btn btn-danger w-100"> Sí, eliminar cuenta</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH.'/includes/footer.php'; ?>