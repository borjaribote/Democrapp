
<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <div class="card-body">
                    <h2 class="card-title text-center">Crear Nueva Ronda</h2>
                    <form action="<?= BASE_URL ?>controllers/controlador_rondas.php" method="POST">
                        <input type="hidden" name="action" value="insert">
                        <div class="mb-3">
                            <label class="form-label required">Tipo de Fase</label>
                            <select class="form-select" name="stage" aria-label="Default select example" required>
                                <option value="" disabled selected>Seleccione la fase</option>
                                <option value="propuestas">Propuestas</option>
                                <option value="clasificatoria">Clasificatoria</option>
                                <option value="final">Final</option>
                           </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Fecha de Inicio</label>
                            <input type="date" name="start_date" class="form-control" required id="start_date">
                        </div>
                        <div class="mb-3">
                                <label class="form-label required">Hora Inicio</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                        <div class="mb-3">
                            <label class="form-label required">Fecha de Fin</label>
                            <input type="date" name="end_date" class="form-control" required id="end_date">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Hora Fin</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>

                        <div id="lista-aprobados" style="display: none;">
                            <label class="form-label required">Selecciona los temas</label>
                            <select name="topics[]" class="form-control" multiple >
                                <?php foreach ($temasAprobados as $tema): ?>
                                    <option value="<?= $tema['id']; ?>"><?= htmlspecialchars($tema['topic']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Mantén presionada la tecla Ctrl (Cmd en Mac) para seleccionar varios temas.</small>
                        </div>                    
                        <div id="lista-finalistas" style="display: none;">
                            <label class="form-label required">Selecciona los temas</label>
                            <select name="topics[]" class="form-control" multiple >
                                <?php foreach ($temasFinalistas as $tema): ?>
                                    <option value="<?= $tema['id']; ?>"><?= htmlspecialchars($tema['topic']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Mantén presionada la tecla Ctrl (Cmd en Mac) para seleccionar varios temas.</small>
                        </div>                    
                        <button type="submit" class="btn btn-primary w-100 my-3">Crear Ronda</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>  
