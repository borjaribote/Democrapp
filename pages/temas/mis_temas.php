<?php
require_once '../../includes/header.php';
accesoAutorizado("usuario");
require_once BASE_PATH.'/controllers/controlador_temas.php';

?>
<!-- FAse de propuestas -->
<?php
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
                                        <td>
                                            <?php 
                                            switch ($row['stage']) {
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
                                                    echo 'Sin fase';
                                                    break;
                                            }
                                            ?>
                                        </td>
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
 require_once BASE_PATH.'/includes/footer.php'; ?>