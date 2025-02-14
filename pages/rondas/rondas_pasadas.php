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
                    <th>Temas incluidos</th>
                    <th>Estado Actual</th>
                    <th>Ganador</th>
                    <th>Número Votos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rondas as $ronda): 
                    if ($ronda['status'] == 'finished'): ?>
                        <tr>
                            <td><?= htmlspecialchars($ronda['name']); ?></td>
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
                      <!--       <td><?= htmlspecialchars($ronda['start_date']); ?></td>
                            <td><?= htmlspecialchars($ronda['end_date']); ?></td> -->
                            <td>
                                <span class="badge bg-<?= $ronda['status'] == 'active' ? 'success' : ($ronda['status'] == 'inactive' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst($ronda['status']); ?>
                                </span>
                            </td>                         
                            <?php
                            $votos = 0;
                            $temas_ganadores = [];
                            
                            foreach ($ronda['temas'] as $tema) {
                                if ($tema['votos'] > $votos) {
                                    $votos = $tema['votos'];
                                    $temas_ganadores = [$tema['topic']];
                                } elseif ($tema['votos'] == $votos) {
                                    $temas_ganadores[] = $tema['topic'];
                                }
                            }
                            ?>
                            <td class="winner-list">
                                <ul>
                                    <?php foreach ($temas_ganadores as $ganador): ?>
                                        <li><?= htmlspecialchars($ganador); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td><span class="badge bg-primary"><?= $votos ?> votos</span></td>                       
                        </tr>
                    <?php endif;
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
