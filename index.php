<?php
require_once 'includes/header.php';
require_once BASE_PATH.'/functions/gestion_mensajes.php';   
require_once BASE_PATH.'/controllers/controlador_rondas.php';   
require_once BASE_PATH.'/controllers/controlador_temas.php';
require_once BASE_PATH.'/controllers/controlador_clasificados.php';
require_once BASE_PATH.'/controllers/controlador_votos.php';


if (isset($_SESSION['user_id'])) { 
    $ronda_activa = obtenerRondaActiva();
    $winner = false;
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
                        $nombre_fase = $nombres_fases[$ronda_activa['stage']] ?? 'Fase Desconocida';                     
                        echo "$nombre_fase</h2>";
                            $nombre_fase = $nombres_fases[$ronda_activa['stage']] ?? 'Fase Desconocida';
                        $votado = haVotadoEnRonda($_SESSION['user_id'], $ronda_activa['round_id']);
                        if ($votado) {
                            $ruta = BASE_PATH . "pages/estado_ronda/resultados.php";
                        } else {
                            $ruta = BASE_PATH . "pages/estado_ronda/" . $ronda_activa['stage'] . ".php";
                            if (file_exists($ruta)) {
                               
                                include $ruta;
                            } else {
  
                                echo "<p class='alert alert-warning text-center'>No se encontró la página de la ronda.</p>";
                            }
                        }
                    } else if ($winner) {
                        echo "Tenemos tema ganador <i class='fa-solid fa-trophy'></i></h2>";
                        include BASE_PATH . "pages/rondas/propuestas.php"; 
                    } else {
                        echo "No hay una ronda activa en este momento.";
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
