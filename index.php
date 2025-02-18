<?php
require_once 'includes/header.php';
require_once BASE_PATH.'/functions/gestion_mensajes.php';   
require_once BASE_PATH.'/controllers/controlador_rondas.php';   
require_once BASE_PATH.'/controllers/controlador_temas.php';
require_once BASE_PATH.'/controllers/controlador_clasificados.php';
$ronda_activa = obtenerRondaActiva();
$winner =false;
if (isset($_SESSION['user_id'])) { ?> 

    <section class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2 class="text-center titulo-ronda"> 
                    <?php if (!empty($ronda_activa)) { 
                        switch ($ronda_activa['stage']) {
                                case 'proposals':
                                    echo "Ronda de propuestas </h2>";
                                    include BASE_PATH . "pages/rondas/propuestas.php"; 
                                    break;
                                case 'qualifying':
                                    echo "Ronda de Clasificación </h2>";
                                    echo "Clasificación";
                                    break;
                                case 'final':
                                    echo "Ronda de Clasificación final</h2>";
                                    echo "Final";
                                    break;
                                case 'tiebreaker':
                                    echo "Ronda de Desempate</h2>";
                                    echo "Desempate";
                                    break;
                                default:
                                    echo "Estado de ronda desconocido.";
                                    break;
                        }    
                    }else if ($winner){
                        echo "Tenemos tema ganador <i class='fa-solid fa-trophy'></i></h2>";
                        include BASE_PATH . "pages/rondas/propuestas.php"; 
                    } else {
                        echo "No hay una ronda activa en este momento.";
                    }?>
            </div>
        </div>
    </section>
<?php 
} else {
   
    include BASE_PATH . "pages/login.php"; 
   
}
require_once BASE_PATH.'/includes/footer.php'; ?>