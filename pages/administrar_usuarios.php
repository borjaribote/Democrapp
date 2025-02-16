<?php
require_once '../core/init.php';
accesoAutorizado("usuario");
require_once BASE_PATH.'/includes/header.php';
require_once BASE_PATH.'/controladores/controlador_usuarios.php';
require_once BASE_PATH.'/functions/gestion_mensajes.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$result = consultarUsuarios($search);

?>

<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <div class="card-body">
                    <h2 class="card-title text-center">Administrar usuarios</h2>
                    
                    <!-- Barra de búsqueda -->
                    <form method="GET" action="">
                        <div class="input-group mb-3">
                            <input type="text" name="search" class="form-control" placeholder="Buscar usuarios..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>
                    </form>
                    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre usuario</th>
                                <th>Correo</th>
                                <th>Fecha creación</th>
                                <th>Borrar usuario</th>
                                <th>Administrador</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $row) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo date("Y-m-d", strtotime($row['registration_date'])); ?></td>
                                    <td>
                                        <form action="<?= BASE_URL ?>controladores/controlador_usuarios.php" method="post" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="email" value="<?= $row['username'] ?>">
                                            <input type="hidden" name="page" value="administrar_usuarios">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                    </td>
                                    <td>    
                                        
                                        <?php 
                                         if (strtolower($row['username']) == "admin") { ?>  
                                            <i class="fa-solid fa-check"></i>
                                        <?php } else { ?>                                                            
                                                <button type="button" 
                                                class="btn btn-sm <?= $row['is_admin'] == 1 ? 'btn-outline-danger' : 'btn-warning' ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#confirmAdminModal"
                                                data-userid="<?= $row['id'] ?>"
                                                data-username="<?= htmlspecialchars($row['username']) ?>"
                                                data-isadmin="<?= $row['is_admin'] ?>">                                              
                                                    <?= $row['is_admin'] == 1 ? 'Quitar permisos' : 'Dar permisos' ?>
                                                </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="confirmAdminModal" tabindex="-1" aria-labelledby="confirmAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmAdminModalLabel">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span id="modalMessage"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <br>
                <p id="modalWarning"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="adminForm" action="<?= BASE_URL ?>controladores/controlador_usuarios.php" method="post">
                    <input type="hidden" name="id" id="modalUserId">
                    <input type="hidden" name="is_admin" id="modalIsAdmin">
                    <button type="submit" name="action" value="update" class="btn btn-danger w-100" id="modalConfirmButton">
                        Confirmar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>




<?php
 require_once BASE_PATH.'/includes/footer.php'; ?>