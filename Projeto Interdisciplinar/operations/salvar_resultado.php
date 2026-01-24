<?php
// salvar_resultado.php
session_start();

// BIBLIOTECA DE MATRIZES CODIFICADORAS
$matrizesCodificadoras = [
    // Matriz 1x1 (para matrizes 1x1)
    '1x1' => [
        [2]
    ],

    // Matriz 2x2 (para matrizes 2xN ou Nx2)
    '2x2' => [
        [3, 2],
        [1, 4]
    ],

    // Matriz 3x3 (para matrizes 3xN ou Nx3)
    '3x3' => [
        [1, 2, 1],
        [2, 3, 2],
        [1, 2, 2]
    ],

    // Matriz 4x4 (para matrizes 4xN ou Nx4)
    '4x4' => [
        [2, 1, 0, 1],
        [1, 3, 1, 0],
        [0, 1, 2, 1],
        [1, 0, 1, 2]
    ],

    // Matriz 5x5 (para matrizes 5xN ou Nx5)
    '5x5' => [
        [2, 1, 0, 0, 1],
        [1, 2, 1, 0, 0],
        [0, 1, 2, 1, 0],
        [0, 0, 1, 2, 1],
        [1, 0, 0, 1, 2]
    ],

    // Matriz 6x6 (para matrizes 6xN ou Nx6)
    '6x6' => [
        [2, 1, 0, 0, 0, 1],
        [1, 2, 1, 0, 0, 0],
        [0, 1, 2, 1, 0, 0],
        [0, 0, 1, 2, 1, 0],
        [0, 0, 0, 1, 2, 1],
        [1, 0, 0, 0, 1, 2]
    ]
];

// Receber dados
$matrizResultado = $_POST['matriz_resultado'] ?? [];
$formato = $_POST['formato'] ?? 'json';

// Converter JSON para array
if (is_string($matrizResultado)) {
    $matrizResultado = json_decode($matrizResultado, true);
}

// Determinar dimensões
$linhasMatriz = count($matrizResultado);
$colunasMatriz = count($matrizResultado[0]);

// Escolher matriz codificadora correta
$tamanho = min($linhasMatriz, 6);
$chaveCodificadora = $tamanho . 'x' . $tamanho;

// Garantir que temos a matriz
if (!isset($matrizesCodificadoras[$chaveCodificadora])) {
    for ($i = $tamanho; $i >= 1; $i--) {
        $chaveTeste = $i . 'x' . $i;
        if (isset($matrizesCodificadoras[$chaveTeste])) {
            $chaveCodificadora = $chaveTeste;
            break;
        }
    }
}

$matrizCodificadora = $matrizesCodificadoras[$chaveCodificadora];

// Função de multiplicação
function multiplicarMatrizes($A, $B)
{
    $linhasA = count($A);
    $colunasA = count($A[0]);
    $linhasB = count($B);
    $colunasB = count($B[0]);

    if ($colunasA != $linhasB)
        return false;

    $resultado = [];
    for ($i = 0; $i < $linhasA; $i++) {
        $resultado[$i] = [];
        for ($j = 0; $j < $colunasB; $j++) {
            $soma = 0;
            for ($k = 0; $k < $colunasA; $k++) {
                $soma += $A[$i][$k] * $B[$k][$j];
            }
            $resultado[$i][$j] = $soma;
        }
    }
    return $resultado;
}

// Criptografar
function criptografarMatriz($matriz, $codificadora)
{
    $linhasCod = count($codificadora);
    $colunasCod = count($codificadora[0]);
    $linhasMat = count($matriz);
    $colunasMat = count($matriz[0]);

    // Se for compatível
    if ($linhasMat == $linhasCod) {
        return multiplicarMatrizes($codificadora, $matriz);
    }
    // Se matriz resultado tem menos linhas
    elseif ($linhasMat < $linhasCod) {
        $matrizAjustada = $matriz;
        for ($i = $linhasMat; $i < $linhasCod; $i++) {
            $matrizAjustada[$i] = array_fill(0, $colunasMat, 0);
        }
        return multiplicarMatrizes($codificadora, $matrizAjustada);
    }
    // Se matriz resultado tem mais linhas
    else {
        $matrizAjustada = [];
        for ($i = 0; $i < $linhasCod; $i++) {
            $matrizAjustada[$i] = $matriz[$i];
        }
        return multiplicarMatrizes($codificadora, $matrizAjustada);
    }
}

// Criptografar
$matrizCriptografada = criptografarMatriz($matrizResultado, $matrizCodificadora);

// Gerar arquivo APENAS com a matriz criptografada
if ($formato === 'json') {
    // APENAS a matriz criptografada
    $dados = $matrizCriptografada;

    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="matriz_criptografada.json"');
    echo json_encode($dados);

} elseif ($formato === 'html') {
    // APENAS a matriz criptografada em HTML simples
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <title>Matriz Criptografada</title>
        <style>
            table { border-collapse: collapse; margin: 20px; }
            td { border: 1px solid #000; padding: 10px; text-align: center; }
        </style>
    </head>
    <body>';

    $html .= '<table>';
    foreach ($matrizCriptografada as $linha) {
        $html .= '<tr>';
        foreach ($linha as $valor) {
            $html .= '<td>' . $valor . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';
    $html .= '</body></html>';

    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="matriz_criptografada.html"');
    echo $html;
}

exit;
?>