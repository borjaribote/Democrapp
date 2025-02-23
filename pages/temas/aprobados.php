<?php
list($result, $new_direction) = consultarTemasporClasificarseOrdenado(); // Recibimos la nueva dirección

// Determinar el orden actual y la nueva dirección
$current_order = $_GET['order'] ?? '';
$new_direction = ($_GET['direction'] ?? 'ASC') === 'ASC' ? 'DESC' : 'ASC';
// Definir iconos solo para el elemento activo
$title_icon = ($current_order === 'title') ? ($new_direction === "ASC" ? 'fa-angle-up' : 'fa-angle-down') : 'fa-angle-left';
$topic_icon = ($current_order === 'topic') ? ($new_direction === "ASC" ? 'fa-angle-up' : 'fa-angle-down') : 'fa-angle-left';
$created_icon = ($current_order === 'created_at') ? ($new_direction === "ASC" ? 'fa-angle-up' : 'fa-angle-down') : 'fa-angle-left';


?>

<div class="container my-5">
    <h2 class="mb-4">Lista de temas aprobados</h2>

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
