<?php
list($result, $new_direction) = consultarTemasPendientes(); // Recibimos la nueva dirección

// Determinar el orden actual y la nueva dirección
$current_order = $_GET['order'] ?? '';
$new_direction = ($_GET['direction'] ?? 'ASC') === 'ASC' ? 'DESC' : 'ASC';
// Definir iconos solo para el elemento activo
$title_icon = ($current_order === 'title') ? ($new_direction === "ASC" ? 'fa-angle-up' : 'fa-angle-down') : 'fa-angle-left';
$topic_icon = ($current_order === 'topic') ? ($new_direction === "ASC" ? 'fa-angle-up' : 'fa-angle-down') : 'fa-angle-left';
$created_icon = ($current_order === 'created_at') ? ($new_direction === "ASC" ? 'fa-angle-up' : 'fa-angle-down') : 'fa-angle-left';


?>

<div class="container my-5">
    <h2 class="mb-4">Lista de temas por moderar</h2>

    <!-- Formulario de Búsqueda -->
    <form method="GET" action="" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control rounded-start" placeholder="   Buscar temas..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button class="btn btn-primary rounded-end" type="submit">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
    </form>

    <!-- Tabla con Ordenación -->
    <?php if (!empty($result) && is_array($result)): ?>
        <table class="table table-bordered table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th class="<?= ($current_order === 'title') ? 'sorted-column' : '' ?>">
                    <a href="?order=title&direction=<?= $new_direction ?>" class="text-decoration-none text-dark">
                        Título <?php if ($title_icon) : ?><i class="fa <?= $title_icon ?>"></i><?php endif; ?>
                    </a>
                </th>
                <th class="<?= ($current_order === 'topic') ? 'sorted-column' : '' ?>">
                    <a href="?order=topic&direction=<?= $new_direction ?>" class="text-decoration-none text-dark">
                        Tema <?php if ($topic_icon) : ?><i class="fa <?= $topic_icon ?>"></i><?php endif; ?>
                    </a>
                </th>
                <th class="<?= ($current_order === 'created_at') ? 'sorted-column' : '' ?>">
                    <a href="?order=created_at&direction=<?= $new_direction ?>" class="text-decoration-none text-dark">
                        Fecha <?php if ($created_icon) : ?><i class="fa <?= $created_icon ?>"></i><?php endif; ?>
                    </a>
                </th>
                <th>Tema completo</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['topic']); ?></td>
                    <td><?php echo date("Y-m-d", strtotime($row['created_at'])); ?></td>
                    <td><button type="button" class="btn btn-secondary btn-sm" 
                    data-bs-toggle="modal" 
                    data-bs-target="#viewTopicInfo" 
                    data-topic-title='<?= $row['title']?>'
                    data-topic-date='<?= date("Y-m-d", strtotime($row['created_at']))?>'
                    data-topic-topic='<?= $row['topic']?>'
                    data-topic-description='<?= $row['description']?>'
                    >
                       Mostrár
                    </button>
                    </td>
                    <td>
                        <form method="POST" action="<?= BASE_URL ?>controladores/controlador_temas.php" class="d-inline">
                            <input type="hidden" name="topic_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="update" class="btn btn-success btn-sm">Aprobar</button>
                            <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center" role="alert">
            <p>No hay temas pendientes de moderación.</p>
        </div>
    <?php endif; ?>
</div>
<div class="modal fade" id="viewTopicInfo" tabindex="-1" aria-labelledby="temaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-white border-bottom-0 pb-2">
                <h5 class="modal-title fw-semibold text-dark" id="temaModalLabel">Detalles del Tema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Título</h6>
                    <p class="fw-semibold text-dark" id="topicTitle"></p>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Fecha de Creación</h6>
                    <p class="text-dark" id="topicCreatedAt"></p>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Tema</h6>
                    <p class="text-dark" id="topicTopic"></p>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Descripción</h6>
                    <p class="text-dark" id="topicDescription"></p>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-dark px-4 py-2 rounded-pill" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>


    <!-- Modal -->
 <!--    <div class="modal fade" id="viewTopicInfo" tabindex="-1" aria-labelledby="temaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="temaModalLabel">Detalles del Tema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <strong>Título:</strong> <span id="topicTitle"></span><br>
                    <strong>Fecha de Creación:</strong> <span id="topicCreatedAt"></span><br>
                    <strong>Tema:</strong> <span id="topicTopic"></span><br>
                    <strong>Descripción:</strong> <span id="topicDescription"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div> -->