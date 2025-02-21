<section class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-5 p-3">
                    <div class="card-body">
                        <h2 class="card-title text-center">Temas propuestos</h2>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tema</th>
                                    <th>Votos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT 
                                            t.id AS topic_id,
                                            t.title AS topic_title,
                                            t.topic AS topic,
                                            t.is_approved,
                                            COUNT(v.user_id) AS total_votes -- Número total de votos por tema
                                        FROM 
                                            topics t
                                        LEFT JOIN 
                                            votes v ON t.id = v.topic_id -- Relación de votos con temas
                                        WHERE 
                                            t.is_approved = TRUE -- Solo temas aprobados
                                        GROUP BY 
                                            t.id, t.title, t.topic, t.is_approved
                                        ORDER BY 
                                            total_votes DESC; -- Ordena por el total de votos de mayor a menor
                                        ";// Ordena por mayor cantidad de votos
                                $result = $conexion->query($query);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['topic']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['total_votes']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2' class='text-center'>No hay temas disponibles</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>