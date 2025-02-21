<div class="container mt-5">
<h2 class="text-center mb-4">Vota por tus temas favoritos</h2>
<div id="puntuacion-static" class="d-flex row-wrap gap-4 text-center mb-4">
    <div class="medalla">
        <div class='token-static token-3 selected' data-value='3'>3</div>
        <strong>3 Puntos</strong>
    </div>
    <div class="medalla">
        <div class='token-static token-2 selected' data-value='2'>2</div>
        <strong>2 Puntos</strong>
    </div>
    <div class="medalla">
        <div class='token-static token-1 selected' data-value='1'>1</div>
        <strong>1 Punto</strong>
    </div>
</div>
    <form id="voteForm" action="guardar_voto.php" method="POST">
        <div class="row" id="topicsContainer">
            <?php
                $temas = obtenerTemasRondaActiva(); 
                foreach ($temas as $tema) {
                    ?><div class='col-md-4 mb-3'>
                            <div class='card text-center'>
                                <div class='card-body'>
                                        <div class='card-title'> 
                                            <div class="accordion accordion-flush" id="accordionFlush">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="flush-heading-<?php echo $tema['id'] ?>">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-<?php echo $tema['id'] ?>" aria-expanded="false" aria-controls="flush-collapse-<?php echo $tema['id'] ?>">
                                                        <strong><?php echo $tema['topic'] ?></strong></button>
                                                        <hr>
                                                    </h2>
                                                    <div id="flush-collapse-<?php echo $tema['id'] ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading-<?php echo $tema['id'] ?>" >
                                                        <div class="accordion-body"><p class="description">
                                                            <?php echo empty($tema['description']) ? '¯\_(ツ)_/¯ <br> <span>No hay descripción</span>' : $tema['description']; ?></p>                                                        
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php include BASE_PATH . 'includes/puntuacion.php'; ?>                         
                                </div>
                            </div>
                        </div>
                <?php    }
            ?>
        </div>
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success" disabled id="submitVote">Guardar Votación</button>
        </div>
    </form>
</div>
<script src="assets/js/votacion.js"></script>
                         