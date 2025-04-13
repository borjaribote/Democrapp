<?php
require_once '../../includes/header.php';
accesoAutorizado("usuario");
require_once BASE_PATH.'/controllers/controlador_temas.php';

?>
<!-- FAse de propuestas -->
<?php
    $result = consultarDatosTemas($_SESSION['user_id']);
?>
<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <div class="card-body">
                    <h2 class="card-title text-center">Mis Temas</h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tema</th>
                                <th>Estado</th>
<!--                                 <th>Fase</th>
 -->                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($result) > 0): ?>
                            <?php foreach ($result as $row) {?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['topic']); ?></td>
<!--                                         <td><?php echo htmlspecialchars($row['estado_tema']); ?></td>
 -->                                        <?php
                                        if ($row['finalista'] === "true"){
                                            $fase = "Clasificado";
                                        }else if($row['descalificado'] === "true"){
                                            $fase = "No Clasificado";
                                        }else if ($row['ganador'] === "true"){
                                            $fase = "¡GANADOR!";
                                        }else{
                                           if($row['estado_tema'] === "Aprobado"){
                                            $fase = "Aprobado";
                                           }else{
                                            $fase = "Pendiente";
                                           }       
                                        }
                                        ?>
                                        <td><?php echo $fase?></td>
                                    </tr>
                                <?php } else: ?>
                                <tr>
                                    <td colspan="2" class="text-center">No has propuesto ningún tema aún.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end">
                <button data-bs-toggle="modal" data-bs-target="#openInfoModal" class="btn text-muted">
                    Más información          
                </button>
                </div>
                <p class="mt-3 text-muted text-right">
               </p>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="openInfoModal" tabindex="-1" aria-labelledby="openInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="openInfoModalLabel"><i class="fa-solid fa-triangle-exclamation"></i> Información</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <span>El campo estado indica en que parte del proceso se encuentra su tema, las opciones pueden ser las siguientes:
                </span>
                <ul>
                    <li>
                        Pendiente: El tema está pendiente de aprobación por el administrador.
                    </li>
                    <li>
                        Aprobado: El tema ha sido aprobado y está a la espera de entrar en una ronda de clasificación.
                    </li>
                    <li>
                        No clasificado: No ha pasado la ronda, el tema queda eliminado. 
                    </li>
                    <li>
                        Clasificado: El tema pasa a la ronda final.
                    </li>
                    <li>
                        Ganador: En fin, ya lo dice todo no? 
                    </li>
                </ul>
                <span class="text-muted">
                Si un tema que ingresaste no aparece, es posible que haya sido eliminado por considerarse similar a otro existente.
                </span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
    <?php
 require_once BASE_PATH.'/includes/footer.php'; ?>