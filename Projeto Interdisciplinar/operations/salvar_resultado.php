<?php
session_start();
include_once '../includes/functions.php';

// BIBLIOTECA DE MATRIZES CODIFICADORAS
$matrizesCodificadoras = [
    // Matriz 1x1 (para matrizes 1x1)
    '1x1' => [
        [2]
    ],

    // Matriz 2x2 (para matrizes 2xN ou Nx2)
    '2x2' => [
        [1, 2],
        [3, 5]
    ],

    // Matriz 3x3 (para matrizes 3xN ou Nx3)
    '3x3' => [
        [1, 2, 1],
        [2, 5, 2],
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
