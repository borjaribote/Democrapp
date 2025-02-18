
<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <div class="card-body">
                    <h2 class="card-title text-center">Crear Nueva Ronda</h2>
                    <form action="<?= BASE_URL ?>controllers/controlador_rondas.php" method="POST">
                        <input type="hidden" name="action" value="insert">
                        <div class="mb-3">
                            <label class="form-label required">Título</label>
                            <input type="text" name="name" class="form-control" placeholder="Título con el que se mostrará la fase" required>
                        </div> 

                        <div class="mb-3">
                          <!--   
                            <input type="text" name="stage" class="form-control" placeholder="Ejemplo: Fase de Grupos, Cuartos de Final, Semifinales, Gran Final" required> -->
                            <label class="form-label required">Tipo de Fase</label>
                            <select class="form-select required" name="stage" aria-label="Default select example" required>
                                <option selected disabled>Seleccione la fase</option>
                                <option value="proposals">Propuestas</option>
                                <option value="qualifying">Clasificación</option>
                                <option value="final">Final</option>
                                <option value="tiebreaker">Desempate</option>
                            </select>
                        </div> 
                       
                        <div class="mb-3">
                            <label class="form-label required">Fecha de Inicio</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Fecha de Fin</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>

                        <div class="mb-3" id="topics-container" style="display: none;">
                            <label class="form-label required">Selecciona los temas</label>
                            <select name="topics[]" class="form-control" multiple >
                                <?php foreach ($temasAprobados as $tema): ?>
                                    <option value="<?= $tema['id']; ?>"><?= htmlspecialchars($tema['topic']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Mantén presionada la tecla Ctrl (Cmd en Mac) para seleccionar varios temas.</small>
                        </div>                    
                        <button type="submit" class="btn btn-primary w-100">Crear Ronda</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>  
