
<?php 
$ronda = obtenerUltimaRondaActiva();
$ganador = obtenerGanadorRonda($ronda['id'] ?? null);
$resultados = obtenerResultadosRonda($ronda['id'] ?? null);
?>
<section class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5 p-3">
                <div class="card-body text-center">
                    <?php if (!$ronda) { ?>
                        <h2 class="card-title">Bienvenido a la plataforma de votación</h2>
                        <p class="text-muted">No hay rondas activas o finalizadas en este momento.</p>
                    <?php } elseif ($ganador) { ?>
                        <h2 class="card-title text-success">¡Ganador de la última ronda!</h2>
                        <p class="lead"><strong><?php echo htmlspecialchars($ganador['topic']); ?></strong></p>
                        <p class="text-muted">Total de votos: <?php echo $ganador['total_votes']; ?></p>
                        <meta http-equiv="refresh" content="10;url=index.php"> <!-- Refresca en 10s -->
                    <?php } else { ?>
                        <h2 class="card-title text-center">Resultados de la <?php echo htmlspecialchars($ronda['name']); ?></h2>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tema</th>
                                    <th>Votos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($resultados)) {
                                    foreach ($resultados as $row) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['topic']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['total_votes']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2' class='text-center'>No hay votos registrados para esta ronda</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>