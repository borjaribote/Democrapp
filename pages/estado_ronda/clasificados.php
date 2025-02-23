
<?php 
$resultados = obtenerResultadosRonda($ronda['id'] ?? null);
?>
<section class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5 p-3">
                <div class="card-body text-center">
                        <h2 class="card-title text-center">Estado de la votación</h2>
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
                </div>
            </div>
        </div>
    </div>
</section>