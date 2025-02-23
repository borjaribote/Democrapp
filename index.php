<?php
require_once 'includes/header.php';
require_once BASE_PATH.'/functions/gestion_mensajes.php';   
require_once BASE_PATH.'/controllers/controlador_rondas.php';   
require_once BASE_PATH.'/controllers/controlador_temas.php';
require_once BASE_PATH.'/controllers/controlador_clasificados.php';
require_once BASE_PATH.'/controllers/controlador_votos.php';


if (isset($_SESSION['user_id'])) { 
    $temas_clasificados = null;
    $ronda_activa = obtenerRondaActiva();
/* Esto no está hecho */
    $ganador = obtenerGanadorRonda($ronda['id'] ?? null);
    $winner = null;
/* ********************************************************** */

    $nombres_fases = [
        'propuestas' => 'Ronda de Propuestas',
        'clasificatoria' => 'Ronda Clasificatoria',
        'final' => 'Ronda Final',
        'desempate' => 'Ronda de Desempate'
    ];
    
?> 
    <section  id="mainContent" class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2 class="text-center titulo-ronda">
                    <?php 
                    if (!empty($ronda_activa)) {                          
                        $votado = haVotadoEnRonda($_SESSION['user_id'], $ronda_activa['round_id']);
                        if ($votado) {
                            echo "Resultados de la votación</h2>"; 
                            $ruta = BASE_PATH . "pages/estado_ronda/resultados.php";
                            include $ruta;
                        } else {
                            $nombre_fase = $nombres_fases[$ronda_activa['stage']] ?? 'Fase Desconocida';                     
                            echo "$nombre_fase</h2>";                           
                            $ruta = BASE_PATH . "pages/estado_ronda/votacion.php";
                            if (file_exists($ruta)) {
                               
                                include $ruta;
                            } else {
  
                                echo "<p class='alert alert-warning text-center'>No se encontró la página de la ronda.</p>";
                            }
                        }
                    } else if ($winner) {
                     ?>
                      <h2 class="card-title text-success">¡Ganador de la última ronda!</h2>
                        <p class="lead"><strong><?php echo htmlspecialchars($ganador['topic']); ?></strong></p>
                        <p class="text-muted">Total de votos: <?php echo $ganador['total_votes']; ?></p>
                     <?php
                    }  else if ($temas_clasificados) {
                        ?>
                         <h2 class="card-title text-success">¡Temas clasificados para la final!</h2>
                           <?php include  BASE_PATH . "pages/estado_ronda/clasificados.php";?>
                        <?php
                       } else {
                        ?>
                           <span class="card-title greek">DemocrApp</span>
                           <p class="text-muted">No hay rondas activas o finalizadas en este momento.</p>
                        <?php
                    }
                    ?>
                </h2>
            </div>
        </div>
    </section>
<?php 
} else {
    include BASE_PATH . "pages/login.php"; 
}
require_once BASE_PATH.'/includes/footer.php'; 
?>
