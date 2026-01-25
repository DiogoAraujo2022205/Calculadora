<?php
session_start();

// Receber dados
$matrizResultado = $_POST['matriz_resultado'] ?? [];
$formato = $_POST['formato'] ?? 'json';

// Converter JSON para array
if (is_string($matrizResultado)) {
    $matrizResultado = json_decode($matrizResultado, true);
}

// APENAS a matriz já criptografada
if ($formato === 'json') {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="mensagem_criptografada.json"');
    echo json_encode(['mensagem_criptografada' => $matrizResultado]);

} elseif ($formato === 'html') {
    // APENAS a matriz criptografada em HTML simples
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <title>Mensagem Criptografada</title>
        <style>
            table { border-collapse: collapse; margin: 20px; }
            td { border: 1px solid #000; padding: 10px; text-align: center; }
        </style>
    </head>
    <body>';

    $html .= '<h2>Mensagem Criptografada</h2>';
    $html .= '<table>';
    foreach ($matrizResultado as $linha) {
        $html .= '<tr>';
        foreach ($linha as $valor) {
            $html .= '<td>' . $valor . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';
    $html .= '</body></html>';

    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="mensagem_criptografada.html"');
    echo $html;
}

exit;

?>
