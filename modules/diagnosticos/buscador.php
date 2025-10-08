<?php
// Asegúrate de que esta ruta sea correcta
include '../../config/db.php';

// El contenido que se devuelve es HTML para inserción AJAX
header('Content-Type: text/html; charset=utf-8');

// --- 1. Obtener y Sanitizar el Término de Búsqueda ---
$searchTerm = isset($_POST['buscar']) ? $_POST['buscar'] : '';

// Limpiamos el término de búsqueda y lo preparamos para la cláusula LIKE
$searchTermLower = strtolower($searchTerm);
$searchWildcard = "%" . mysqli_real_escape_string($conex, $searchTermLower) . "%";

// --- 2. Consulta SQL usando CONCAT() y MySQLi ---
// NOTA: La búsqueda cubre ID, Matrícula y Nombre Completo
$query = "SELECT 
            id_beneficiario,
            matricula,
            CONCAT(nombre, ' ', apellido_paterno, ' ', IFNULL(apellido_materno, '')) AS nombre_completo
        FROM beneficiarios
        WHERE 
            CAST(id_beneficiario AS CHAR) LIKE ? 
            OR LOWER(CONCAT(nombre, ' ', apellido_paterno, ' ', IFNULL(apellido_materno, ''))) LIKE ?
            OR matricula LIKE ?
        LIMIT 10"; 

$stmt = mysqli_prepare($conex, $query);

// Preparamos los parámetros: 's' indica que todos son strings
mysqli_stmt_bind_param($stmt, "sss", $searchWildcard, $searchWildcard, $searchWildcard);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$numero = mysqli_num_rows($resultado);
?>

<div class="search-results-summary">
    Resultados encontrados (<?php echo $numero; ?>):
</div>

<div class="results-list-container">
<?php 
// Si se encontraron resultados, iteramos sobre ellos
if ($numero > 0) {
    while($row = mysqli_fetch_assoc($resultado)) { 
        // Formato: [ID] - [Nombre Completo] (Mat: [Matrícula])
        $display_text = htmlspecialchars($row["id_beneficiario"] . ' - ' . $row["nombre_completo"] . ' (Mat: ' . $row['matricula'] . ')');
        $beneficiario_id = $row["id_beneficiario"];
        
        // Usamos la clase 'result-item' para el JS y 'data-id' para la lógica de selección
        ?>
        <div class="result-item" data-id="<?php echo $beneficiario_id; ?>">
            <?php echo $display_text; ?>
        </div>
    <?php 
    } 
} else {
    // Si no hay resultados
    ?>
    <p class="result-placeholder no-results">No se encontraron beneficiarios que coincidan.</p>
    <?php
}
?>
</div>

<?php 
// Cierre de la conexión
mysqli_stmt_close($stmt);
mysqli_close($conex);
?>