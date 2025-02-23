<?php


/* Página: Index Estado: Resultados */
function obtenerUltimaRondaActiva() {
    global $conexion;

    $sql = "SELECT id, stage FROM rounds 
            WHERE status IN ('active', 'finished') 
            ORDER BY end_date DESC LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?: null;
}
?>