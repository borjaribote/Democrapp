<h2>Vota por tus temas favoritos</h2>
<form action="guardar_voto.php" method="POST">
    <label>Tema 1:</label>
    <select name="tema_3puntos">
        <option value="1">Tema A</option>
        <option value="2">Tema B</option>
        <option value="3">Tema C</option>
    </select>
    
    <label>Tema 2:</label>
    <select name="tema_2puntos">
        <option value="1">Tema A</option>
        <option value="2">Tema B</option>
        <option value="3">Tema C</option>
    </select>
    
    <label>Tema 3:</label>
    <select name="tema_1punto">
        <option value="1">Tema A</option>
        <option value="2">Tema B</option>
        <option value="3">Tema C</option>
    </select>
    
    <button type="submit">Guardar Votación</button>
</form>
