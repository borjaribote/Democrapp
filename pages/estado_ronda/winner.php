<?php 
if (count($winner) == 1) {                        
    echo "<h2 class='card-title text-success'>¡Tenemos un tema ganador!</h2>";
    foreach ($winner as $topic) {
        echo "<div class='card mb-3'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>Tema</h5>";
        echo "<p class='card-text'><strong>".htmlspecialchars($topic['topic'])."</strong></p>";
        echo "<p class='card-text text-muted'>Total de votos: ".htmlspecialchars($topic['description'])."</p>";  
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<h2 class='card-title text-success'>¡Tenemos un empate de Ganadores de la ronda final!</h2>";
    foreach ($winner as $topic) {
        echo "<div class='card mb-3'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>Tema</h5>";
        echo "<p class='card-text'><strong>".htmlspecialchars($topic['topic'])."</strong></p>";

        if (!empty($topic['description'])) {
            echo "<h5 class='card-title'>Descipción</h5>";
            echo "<p class='card-text text-muted'>Total de votos: ".htmlspecialchars($topic['description'])."</p>";
        }        echo "</div>";
        echo "</div>";
    }
}
?>