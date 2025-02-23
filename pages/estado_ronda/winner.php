<?php 
if (count($winner) == 1) { 
    $topic = reset($winner); // Obtener el único ganador             
?>
    <h2 class="text-center text-dark">¡Tenemos un tema ganador!</h2>
    <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
            <h4 class="card-title text-dark"><?php echo htmlspecialchars($topic['topic']); ?></h4>
            <p class="card-text text-muted"><?php echo htmlspecialchars($topic['description'] ?? "Sin descripción disponible"); ?></p>
            <div class="alert alert-light border">
                <strong>Total de votos en la ronda final:</strong> <?php echo $topic['votos_final']; ?><br>
                <strong>Total de puntos en la ronda final:</strong> <?php echo $topic['puntos_final']; ?><br>
                <strong>Total de votos en todas las rondas:</strong> <?php echo $topic['votos_totales']; ?><br>
                <strong>Total de puntos en todas las rondas:</strong> <?php echo $topic['puntos_totales']; ?>
            </div>
        </div>
    </div>

<?php 
} else { 
?>
    <h2 class="text-center text-dark">¡Empate en la ronda final!</h2>
    <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
            <p class="card-text">Ambos temas han recibido exactamente los mismos puntos y votos en todas las rondas, resultando en un empate.</p>
            <div class="row">
                <?php foreach ($winner as $topic): ?>
                    <div class="col-md-6">
                        <div class="card border-secondary mb-3">
                            <div class="card-body">
                                <h5 class="card-title text-dark"><?php echo htmlspecialchars($topic['topic']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($topic['description'] ?? "Sin descripción disponible"); ?></p>
                                <div class="alert alert-light border">
                                    <strong>Votos en la ronda final:</strong> <?php echo $topic['votos_final']; ?><br>
                                    <strong>Puntos en la ronda final:</strong> <?php echo $topic['puntos_final']; ?><br>
                                    <strong>Votos totales en todas las rondas:</strong> <?php echo $topic['votos_totales']; ?><br>
                                    <strong>Puntos totales en todas las rondas:</strong> <?php echo $topic['puntos_totales']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="alert alert-light border text-center">
                <strong>¿Por qué hay dos ganadores?</strong><br>
                Ambos temas han recibido la misma cantidad de puntos y votos en la ronda final, así como en todas las rondas anteriores, lo que ha resultado en un empate técnico.
            </div>
        </div>
    </div>
<?php 
}
?>
