
<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <div class="card-body">
                    <h2 class="card-title text-center">Proponer Tema</h2>
                    <form action="<?= BASE_URL ?>controllers/controlador_temas.php" method="post">
                        <input type="hidden" name="action" value="insert">
                        <div class="mb-3">
                            <label for="topic" class="form-label required">Tema</label>
                            <input type="text" id="topic" name="topic" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Proponer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>