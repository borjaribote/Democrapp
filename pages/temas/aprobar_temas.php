<?php
$result = consultarTemasPendientes(); ?>
<div class="container my-5">
    <h2 class="mb-4">Lista de temas por moderar</h2>

<!-- Formulario de Búsqueda Mejorado -->
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
<?php if (!empty($result) && is_array($result)): 
    $title = 'fa-angle-up';
    $topic = 'fa-angle-up';
    $created = 'fa-angle-up';
    $order = isset($_GET['order']) ? $_GET['order'] : 'created_at';
    switch ($order) {
        case 'title':
            $title = 'fa-angle-down';
            break;
        case 'topic':
            $topic = 'fa-angle-down';
            break;
        case 'created_at':
            $created = 'fa-angle-down';
            break;
        default:
            $created = 'fa-angle-down';
            break;
    }?>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th><a href="?order=title" class="text-decoration-none text-dark">Título <i class="fa <?php echo $title?>"></i></a></th>
                <th><a href="?order=topic" class="text-decoration-none text-dark">Tema <i class="fa <?php echo $topic?>"></i></a></th>
                <th><a href="?order=created_at" class="text-decoration-none text-dark">Fecha <i class="fa <?php echo $created?>"></i></a></th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['topic']); ?></td>
                    <td><?php echo $row['created_at']; ?></td>
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
