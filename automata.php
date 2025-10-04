<?php
// Funciones existentes del simulador
function v2($n) {
    $count = 0;
    while ($n % 2 == 0) {
        $n = (int)($n / 2);
        $count++;
    }
    return $count;
}

function delta($n) {
    if ($n % 4 == 0) {
        return (int)($n / 2);
    } elseif ($n % 4 == 2) {
        return (int)((3 * $n / 2) + 1);
    } else {
        throw new Exception("n must be even");
    }
}

function get_state($n) {
    if ($n == 2 || $n == 4) {
        return "Cycle {2,4}";
    }
    $mod4 = $n % 4;
    $val2 = v2($n);
    if ($mod4 == 0) {
        if ($val2 > 2) {
            return "S0 (≡0 mod4, v2={$val2}>2)";
        } elseif ($val2 == 2) {
            return "Sv2=2 (contraction)";
        }
    } elseif ($mod4 == 2) {
        $mod8 = $n % 8;
        if ($mod8 == 2) {
            return "S2 (≡2 mod4, ≡2 mod8)";
        } elseif ($mod8 == 6) {
            return "S2 (≡2 mod4, ≡6 mod8)";
        }
    }
    return "Unknown";
}

function simulate_orbit($start_n, $max_steps = 100) {
    $orbit = [];
    $n = $start_n;
    $steps = 0;
    while ($n != 2 && $n != 4 && $steps < $max_steps) {
        $state = get_state($n);
        $next_n = delta($n);
        $operation = ($n % 4 == 0) ? "$n / 2" : "(3 * $n / 2) + 1";
        $orbit[] = "Step {$steps}: n={$n}, state={$state}, v2=" . v2($n) . ", operation={$operation} → {$next_n}";
        $n = $next_n;
        $steps++;
    }
    $state = get_state($n);
    $orbit[] = "Step {$steps}: n={$n}, state={$state}";
    return implode("\n", $orbit);
}

// Procesamiento del formulario
$results = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['start_n'])) {
    $start_n = filter_var($_POST['start_n'], FILTER_VALIDATE_INT);
    if ($start_n !== false && $start_n >= 4 && $start_n % 2 == 0) {
        $results = simulate_orbit($start_n);
    } else {
        $results = "Por favor, ingrese un número entero par mayor o igual a 4.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Convergencia de Transformación Iterada</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; }
        input[type="number"] { padding: 5px; width: 150px; }
        input[type="submit"] { padding: 5px 10px; }
        #results { margin-top: 20px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Convergencia de una transformación iterada sobre enteros pares</h1>
        <p><strong>Miguel Cerdá Bennassar</strong> - 3 de octubre de 2025</p>
        <p><a href="mailto:dosena@riodena.com">dosena@riodena.com</a></p>

        <h2>Índice</h2>
        <ol>
            <li>Introducción</li>
            <li>Definiciones y observaciones
                <ul><li>Clasificación módulo 8</li></ul>
            </li>
            <li>Bloques con v₂ = 2 y contracción</li>
            <li>Convergencia global</li>
            <li>Tiempo de entrada: cota rigurosa y lectura heurística</li>
            <li>Comentarios finales</li>
        </ol>

        <h2>Simulador Interactivo</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="start_n">Ingrese un número par ≥ 4 para simular la órbita:</label>
                <input type="number" id="start_n" name="start_n" min="4" step="2" required>
                <input type="submit" value="Simular">
            </div>
        </form>

        <div id="results">
            <?php echo $results; ?>
        </div>
    </div>
</body>
</html>
