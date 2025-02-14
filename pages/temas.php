<?php
require_once '../core/init.php';
require_once BASE_PATH.'/includes/header.php';
require_once BASE_PATH.'/controladores/controlador_temas.php';

if (isset($_SESSION['user_id'])) {?>
<section class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <div class="card-body">
                        <h2 class="card-title text-center">Proponer Tema</h2>
                        <form action="<?= BASE_URL ?>controladores/controlador_temas.php" method="post">
                            <input type="hidden" name="action" value="insert">
                            <div class="mb-3">
                                <label for="topic" class="form-label">Tema:</label>
                                <input type="text" id="topic" name="topic" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Proponer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
   <?php 
    if (isset($_SESSION['new_topic'])) { ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 border-0">
                    <div class="card-body">
                        <div class="alert alert-success mt-3" role="alert">
                            Su tema <strong><?php echo $_SESSION['new_topic']; ?></strong> se ha registrado correctamente.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    unset($_SESSION['new_topic']);
    }
       $result = consultarDatosTemas($_SESSION['user_id']);


    ?>
<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <div class="card-body">
                    <h2 class="card-title text-center">Mis Temas</h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tema</th>
                                <th>Estado</th>
                                <th>Fase</th>
                                <th>Votos</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($result) > 0): ?>
                            <?php foreach ($result as $row) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['topic']); ?></td>
                                        <td><?php echo htmlspecialchars($row['estado_tema']); ?></td>
                                        <td><?php echo htmlspecialchars($row['stage'] ?? 'Sin fase'); ?></td>
                                        <td><?php echo htmlspecialchars($row['total_votes'] ?? 0); ?></td>
                                    </tr>
                                <?php } else: ?>
                                <tr>
                                    <td colspan="2" class="text-center">No has propuesto ningún tema aún.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-muted">
                    *Si un tema que ingresaste no aparece, es posible que haya sido eliminado por considerarse similar a otro existente.
                </p>
            </div>
        </div>
    </div>
</section>
    <?php

}else{
    header("Location: " . BASE_URL . "pages/404.php");
    exit();
}
 require_once BASE_PATH.'/includes/footer.php'; ?>