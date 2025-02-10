<?php
require_once "conexion.php";
require_once BASE_PATH.'includes/header.php';

 
if (isset($_SESSION['user_id'])) {  
   ?>
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
                                            t.topic AS topic_category,
                                            t.category AS general_category,
                                            t.is_approved,
                                            COUNT(v.user_id) AS total_votes -- Número total de votos por tema
                                        FROM 
                                            topics t
                                        LEFT JOIN 
                                            votes v ON t.id = v.topic_id -- Relación de votos con temas
                                        WHERE 
                                            t.is_approved = TRUE -- Solo temas aprobados
                                        GROUP BY 
                                            t.id, t.title, t.topic, t.category, t.is_approved
                                        ORDER BY 
                                            total_votes DESC; -- Ordena por el total de votos de mayor a menor
                                        ";// Ordena por mayor cantidad de votos
                                $result = $conexion->query($query);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['topic']) . "</td>";
/*                                         echo "<td>" . htmlspecialchars($row['votos']) . "</td>";
 */                                        echo "</tr>";
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
    <?php
} else {

    ?>
    <section class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card mt-5 p-3">
                    <div class="card-body">
                        <h2 class="card-title text-center">Login</h2>
                        <form action="acciones/login.php" method="post" onsubmit="return usedEmail(event, this)">
                            <input type="hidden" name="action" value="login">
                            <div class="mb-3">
                                <label for="email" class="form-label">correo:</label>
                                <input type="email" id="correo" name="email" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <?php
                                if (isset($_SESSION['error_message'])) {
                                    echo '<input type="password" id="password" name="password" class="form-control is-invalid" required>';
                                    echo '<div class="invalid-feedback">' . $_SESSION['error_message'] . '</div>';
                                    unset($_SESSION['error_message']); 
                                }else{
                                    echo '<input type="password" id="password" name="password" class="form-control" required>';
                                }
                            ?>
                                
                            </div>
                          
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                          <!--   <div class="mt-3 d-flex justify-content-between align-items-end">
                                <a class="primary" href="/registro.php">Registrate</a>
                            </div> -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
}
require_once BASE_PATH.'includes/footer.php'; ?>