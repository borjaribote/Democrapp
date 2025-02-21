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
                    <th>Nombre</th>
                    <th>Fase</th>
                    <th>Temas incluidos</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Hora Fin</th>
                    <th>Estado Actual</th>
                    <th>Nuevo Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rondas as $ronda):                    
                        ?>
                    <tr>
                        <td><?= htmlspecialchars($ronda['name']); ?></td>
                        <td>
                            <?php 
                            switch ($ronda['stage']) {
                                case 'proposals':
                                    echo 'Propuestas';
                                    break;
                                case 'qualifying':
                                    echo 'Clasificación';
                                    break;
                                case 'tiebreaker':
                                    echo 'Desempate';
                                    break;
                                case 'final':
                                    echo 'Final';
                                    break;
                                default:
                                    echo htmlspecialchars($ronda['stage']);
                                    break;
                            }
                            ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#temasModal<?= $ronda['id']; ?>">
                                Ver temas
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="temasModal<?= $ronda['id']; ?>" tabindex="-1" aria-labelledby="temasModalLabel<?= $ronda['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="temasModalLabel<?= $ronda['id']; ?>">Temas de <?= htmlspecialchars($ronda['name']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="list-group">
                                                <?php 
                                                if (!empty($ronda['temas']) && is_array($ronda['temas'])): 
                                                    foreach ($ronda['temas'] as $tema): ?>
                                                        <li class="list-group-item">
                                                            <?= htmlspecialchars($tema['topic']); ?> 
                                                            <span class="badge bg-primary"><?= $tema['votos'] ?> votos</span>
                                                        </li>
                                                    <?php endforeach; 
                                                else: ?>
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


                        <td><?= htmlspecialchars($ronda['start_date']); ?></td>
                        <td><?= htmlspecialchars($ronda['end_date']); ?></td>
                        <td><?= htmlspecialchars($ronda['end_time']); ?></td>
                        <td><span class="badge bg-<?= $ronda['status'] == 'active' ? 'success' : ($ronda['status'] == 'inactive' ? 'warning' : 'danger') ?>">
                            <?= ucfirst($ronda['status']); ?>
                        </span></td>
                        <td>
                            <form method="POST" action="<?php echo BASE_URL; ?>controllers/controlador_rondas.php">
                                <input type="hidden" name="round_id" value="<?= $ronda['id']; ?>">
                                <select name="status" class="form-select">
                                    <option value="active" <?= $ronda['status'] == 'active' ? 'selected' : ''; ?>>Activa</option>
                                    <option value="inactive" <?= $ronda['status'] == 'inactive' ? 'selected' : ''; ?>>Inactiva</option>
                                    <option value="finished" <?= $ronda['status'] == 'finished' ? 'selected' : ''; ?>>Finalizada</option>
                                </select>
                        </td>
                        <td>
                                <button type="submit" name="action" value="update" class="btn btn-primary btn-sm">Actualizar</button>
                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                       
                    </tr>
                <?php 
            endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <p>No hay rondas disponibles.</p>
        </div>
    <?php endif; ?>
</section>
