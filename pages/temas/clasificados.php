<?php
list($temas, $new_direction) = temasClasificadosOrdenado();

// Determinar orden actual y nueva dirección
$current_order = $_GET['order'] ?? 'total_puntos';
$new_direction = ($_GET['direction'] ?? 'DESC') === 'ASC' ? 'DESC' : 'ASC';

// Definir iconos solo para el elemento activo
$topic_icon = ($current_order === 'topic') ? ($new_direction === "ASC" ? 'fa-angle-up' : 'fa-angle-down') : 'fa-angle-left';
$votos_icon = ($current_order === 'total_votos') ? ($new_direction === "ASC" ? 'fa-angle-up' : 'fa-angle-down') : 'fa-angle-left';
$puntos_icon = ($current_order === 'total_puntos') ? ($new_direction === "ASC" ? 'fa-angle-up' : 'fa-angle-down') : 'fa-angle-left';
?>

<div class="container mt-4">
    <h2 class="mb-4">Lista de temas clasificados</h2>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>
                    <a href="?order=topic&direction=<?= $new_direction ?>" class="text-decoration-none text-dark">
                        Tema <i class="fa <?= $topic_icon ?>"></i>
                    </a>
                </th>
                <th>
                    <a href="?order=total_votos&direction=<?= $new_direction ?>" class="text-decoration-none text-dark">
                        Votos <i class="fa <?= $votos_icon ?>"></i>
                    </a>
                </th>
                <th>
                    <a href="?order=total_puntos&direction=<?= $new_direction ?>" class="text-decoration-none text-dark">
                        Puntuación total <i class="fa <?= $puntos_icon ?>"></i>
                    </a>
                </th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($temas as $tema): ?>
                <tr>
                    <td><?= htmlspecialchars($tema['topic']) ?></td>
                    <td><?= htmlspecialchars($tema['total_votos']) ?></td>
                    <td><?= htmlspecialchars($tema['total_puntos']) ?></td>
                    <td>
                        <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTema<?= $tema['id'] ?>">
                            Mostrar
                        </button>
                    </td>
                </tr>

                <!-- Modal Bootstrap -->
                <div class="modal fade" id="modalTema<?= $tema['id'] ?>" tabindex="-1" aria-labelledby="modalTemaLabel<?= $tema['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTemaLabel<?= $tema['id'] ?>">Tema completo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Tema:</strong> <?= htmlspecialchars($tema['topic']) ?></p>
                                <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($tema['description'])) ?></p>
                                <p><strong>Votos:</strong> <?= htmlspecialchars($tema['total_votos']) ?></p>
                                <p><strong>Puntuación total:</strong> <?= htmlspecialchars($tema['total_puntos']) ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
