Sí, puedes actualizar los resultados de la votación en tiempo real usando **AJAX** sin necesidad de recargar la página. Aquí te explico cómo hacerlo paso a paso.

---

## **📌 Pasos para actualizar los resultados con AJAX**
1️⃣ **Crear un archivo PHP (`obtener_resultados.php`) que devuelva los datos de la votación en JSON.**  
2️⃣ **Hacer una llamada AJAX con `fetch()` cada X segundos o cuando se emita un voto.**  
3️⃣ **Actualizar la tabla de resultados dinámicamente sin refrescar la página.**

---

## **1️⃣ Crear el archivo `obtener_resultados.php`**
Este archivo consultará la base de datos y devolverá los datos en formato JSON.

📌 **Crea el archivo `obtener_resultados.php` en tu proyecto:**

```php
<?php
require_once '../config/database.php'; // Ajusta la ruta según tu estructura
require_once '../controllers/controlador_votos.php';

if (!isset($_GET['round_id'])) {
    echo json_encode(["error" => "No se ha especificado una ronda"]);
    exit();
}

$round_id = intval($_GET['round_id']);
$resultados = obtenerResultadosRonda($round_id);

echo json_encode($resultados);
?>
```

---

## **2️⃣ Modificar el HTML para que AJAX actualice la tabla**
Cambia la tabla para darle un `id` y permitir su actualización.

```html
<section class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5 p-3">
                <div class="card-body text-center">
                    <h2 class="card-title text-center">Estado de la votación</h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tema</th>
                                <th>Votos</th>
                            </tr>
                        </thead>
                        <tbody id="resultadosTabla">
                            <!-- Los resultados se actualizarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
```

---

## **3️⃣ Hacer una petición AJAX con `fetch()` para actualizar la tabla**
Este script consultará el archivo PHP cada 5 segundos y actualizará la tabla.

```html
<script>
document.addEventListener("DOMContentLoaded", function () {
    const roundId = <?= json_encode($ronda['id'] ?? null) ?>; // Obtiene la ronda activa desde PHP

    function actualizarResultados() {
        if (!roundId) return; // Si no hay ronda activa, no hacer nada

        fetch(`obtener_resultados.php?round_id=${roundId}`)
            .then(response => response.json())
            .then(data => {
                const tabla = document.getElementById("resultadosTabla");
                tabla.innerHTML = ""; // Limpiar la tabla antes de insertar nuevos datos

                if (data.length > 0) {
                    data.forEach(row => {
                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td>${row.topic}</td>
                            <td>${row.total_points}</td>
                        `;
                        tabla.appendChild(tr);
                    });
                } else {
                    tabla.innerHTML = "<tr><td colspan='2' class='text-center'>No hay votos registrados para esta ronda</td></tr>";
                }
            })
            .catch(error => console.error("Error al obtener los resultados:", error));
    }

    // Actualiza los resultados cada 5 segundos
    setInterval(actualizarResultados, 5000);

    // También actualizar cuando el usuario vote (puedes llamar a esta función después de la votación)
    actualizarResultados();
});
</script>
```

---

## **📌 Explicación**
✅ **`fetch()`** obtiene los datos de `obtener_resultados.php` y los convierte en JSON.  
✅ **`setInterval()`** ejecuta la función cada 5 segundos.  
✅ **Si hay nuevos votos, la tabla se actualiza automáticamente.**  

### **🚀 Ahora, los resultados de la votación se actualizan en tiempo real sin refrescar la página.**  
Pruébalo y dime si funciona como esperas. 🎯