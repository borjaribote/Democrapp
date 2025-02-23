<?php
require_once 'includes/header.php';
require_once BASE_PATH.'/functions/gestion_mensajes.php';   
require_once BASE_PATH.'/controllers/controlador_rondas.php';   
require_once BASE_PATH.'/controllers/controlador_temas.php';
require_once BASE_PATH.'/controllers/controlador_votos.php';


if (isset($_SESSION['user_id'])) { 
    $temas_clasificados = null;
    $ultimaVotacion = null;
    $ronda_activa = obtenerRondaActiva();
    $winner = obtenerGanador();
/* ********************************************************** */

    $nombres_fases = [
        'propuestas' => 'Ronda de Propuestas',
        'clasificatoria' => 'Ronda Clasificatoria',
        'final' => 'Ronda Final'
    ];
?> 
    <section  id="mainContent" class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2 class="text-center titulo-ronda">
                    <?php 
                    if (!empty($ronda_activa)) { 
                        $nombre_fase = $nombres_fases[$ronda_activa['stage']] ?? 'Fase Desconocida'; 
                        if ($ronda_activa['stage'] === "propuestas"){
                            echo "$nombre_fase</h2>";
                            include BASE_PATH . "pages/estado_ronda/propuestas.php";
                             ;
                        }else if ($ronda_activa['stage'] === "clasificatoria" || $ronda_activa['stage'] === "final"){
                            $votado = haVotadoEnRonda($_SESSION['user_id'], $ronda_activa['round_id']);
                            if ($votado) {
                                echo "Resultados de la votación</h2>"; 
                                include BASE_PATH . "pages/estado_ronda/resultados.php";         
                            } else {
                                echo "$nombre_fase</h2>";                           
                                include BASE_PATH . "pages/estado_ronda/votacion.php";
                            }
                        }                                                  
                    } else if (!empty($winner)) {          
                        include BASE_PATH . "pages/estado_ronda/winner.php";
                        include  BASE_PATH . "pages/estado_ronda/resultados.php";                  
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
