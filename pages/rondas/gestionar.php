<?php
$rondas = obtenerRondasConTemas(); // Obtener todas las rondas
?>
<section class="container mt-5">
    <h2 class="mb-4">Actualizar Estado de Rondas</h2>
    <?php if (!empty($rondas)): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Fase</th>
                    <th>Temas incluidos</th>
                    <th>Programación</th>
                    <th>Estado actual</th>
                    <th >Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rondas as $ronda): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ronda['stage']); ?></td>
                        <td>
                            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#temasModal<?= $ronda['id']; ?>">
                                Ver temas
                            </button>

                            <!-- Modal de Temas -->
                            <div class="modal fade" id="temasModal<?= $ronda['id']; ?>" tabindex="-1" aria-labelledby="temasModalLabel<?= $ronda['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Temas de la ronda</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="list-group">
                                                <?php if (!empty($ronda['temas']) && is_array($ronda['temas'])): ?>
                                                    <?php foreach ($ronda['temas'] as $tema): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span><?= htmlspecialchars($tema['topic']); ?> 
                                                            <span class="badge bg-primary"><?= $tema['total_puntos'] ?> votos</span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <li class="list-group-item text-muted">No hay temas asignados.</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                    <?php
                    if ($ronda['start_date'] === "00-00-0000"){
                      
                            echo "no existe";
                    }else{
                        ?>
                        <button type="button" class="btn  btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#programacionModal<?= $ronda['id']; ?>">
                                Mostrar
                            </button>

                            <!-- Modal de Actualización -->
                            <div class="modal fade" id="programacionModal<?= $ronda['id']; ?>" tabindex="-1" aria-labelledby="programacionLabel<?= $ronda['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content p-4">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detalles de la Programación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="card border-secondary">
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-md-6">
                                                    <h6 class="card-title">Inicio</h6>
                                                    <p class="mb-1">
                                                    <i class="bi bi-calendar"></i>
                                                    <?= isset($ronda['start_date']) ? date('d/m/Y', strtotime($ronda['start_date'])) : 'N/A'; ?>
                                                    </p>
                                                    <p class="mb-0">
                                                    <i class="bi bi-clock"></i>
                                                    <?= htmlspecialchars($ronda['start_time']); ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="card-title">Fin</h6>
                                                    <p class="mb-1">
                                                    <i class="bi bi-calendar"></i>
                                                    <?= isset($ronda['end_date']) ? date('d/m/Y', strtotime($ronda['end_date'])) : 'N/A'; ?>
                                                    </p>
                                                    <p class="mb-0">
                                                    <i class="bi bi-clock"></i>
                                                    <?= htmlspecialchars($ronda['end_time']); ?>
                                                    </p>
                                                </div>  
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                    </div>
                                </div>
                                </div>

                        <?php
                       
                    }
                    ?>
         </td>
                        <td><span class="badge bg-<?= $ronda['status'] == 'active' ? 'success' : ($ronda['status'] == 'inactive' ? 'warning' : 'danger') ?>">
                            <?= ucfirst($ronda['status']); ?>
                        </span></td>
                        <td>
                            <!-- Botón para abrir el modal de actualización -->
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateRondaModal<?= $ronda['id']; ?>">
                                Actualizar
                            </button>

                            <!-- Modal de Actualización -->
                            <div class="modal fade" id="updateRondaModal<?= $ronda['id']; ?>" tabindex="-1" aria-labelledby="updateRondaLabel<?= $ronda['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content p-4">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Actualizar ronda</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="<?= BASE_URL; ?>controllers/controlador_rondas.php">
                                            <div class="modal-body text-start">
                                                <input type="hidden" name="round_id" value="<?= $ronda['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Estado de la ronda</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" <?= $ronda['status'] == 'active' ? 'selected' : ''; ?>>Activa</option>
                                                        <option value="inactive" <?= $ronda['status'] == 'inactive' ? 'selected' : ''; ?>>Inactiva</option>
                                                        <option value="finished" <?= $ronda['status'] == 'finished' ? 'selected' : ''; ?>>Finalizada</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="start_date" class="form-label">Fecha de inicio</label>
                                                    <input type="date" class="form-control" name="start_date" value="<?= isset($ronda['start_date']) ? date('Y-m-d', strtotime($ronda['start_date'])) : ''; ?>">                                                </div>
                                                <div class="mb-3">
                                                    <label for="end_time" class="form-label">Hora de inicio</label>
                                                    <input type="time" class="form-control" name="start_time" value="<?= $ronda['start_time']; ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="end_date" class="form-label">Fecha de fin</label>
                                                    <input type="date" class="form-control" name="end_date" value="<?= isset($ronda['end_date']) ? date('Y-m-d', strtotime($ronda['end_date'])) : ''; ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="end_time" class="form-label">Hora de fin</label>
                                                    <input type="time" class="form-control" name="end_time" value="<?= $ronda['end_time']; ?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" name="action" value="update" class="btn btn-primary">Guardar Cambios</button>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <form method="POST" action="<?= BASE_URL; ?>controllers/controlador_rondas.php">
                                <input type="hidden" name="round_id" value="<?= $ronda['id']; ?>">
                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="mt-3 text-muted">
            *Las horas mostradas son en formato 24h
        </p>
    </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <p>No hay rondas disponibles.</p>
        </div>
    <?php endif; ?>
</section>
