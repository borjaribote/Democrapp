<?php
require_once '../../includes/header.php';
accesoAutorizado('publico');
?>

<section class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card mt-5 p-3">
                    <div class="card-body">
                        <h2 class="card-title text-center">Formulario de registro</h2>
                        <form action="<?= BASE_URL ?>controladores/controlador_usuarios.php" method="post" onsubmit="return usedEmail(event, this)">
                            <input type="hidden" name="action" value="insert">
                            <div class="mb-3">
                                <label for="name" class="form-label required">Nombre</label>
                                <input type="text" id="name" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label required">Correo</label>
                                <input type="email" id="register_email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label required">Contraseña</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Registrate</button>
                           
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php require_once BASE_PATH.'/includes/footer.php'; ?>