<?php

// ============================================
// FUNÇÕES BÁSICAS DE MANIPULAÇÃO DE MATRIZES
// ============================================

/**
 * Cria uma matriz a partir dos dados do formulário
 */
function criarMatriz($dados, $linhas, $colunas, $prefixo = 'matriz') {
    $matriz = [];
    for ($i = 0; $i < $linhas; $i++) {
        $matriz[$i] = [];
        for ($j = 0; $j < $colunas; $j++) {
            $nome_campo = $prefixo . "_" . $i . "_" . $j;
            $matriz[$i][$j] = isset($dados[$nome_campo]) ? floatval($dados[$nome_campo]) : 0;
        }
    }
    return $matriz;
}

/**
 * Exibe uma matriz em formato HTML estilizado (para páginas de operações)
 */
function exibirMatrizHTML($matriz, $titulo = null) {
    if (empty($matriz)) return "";
    
    $html = "";
    if ($titulo) {
        $html .= "<h4>$titulo</h4>";
    }
    
    $html .= "<table class='matrix-result-table'>";
    
    foreach ($matriz as $linha) {
        $html .= "<tr>";
        foreach ($linha as $valor) {
            $html .= "<td>" . number_format($valor, 2) . "</td>";
        }
        $html .= "</tr>";
    }
    
    $html .= "</table>";
    return $html;
}

/**
 * Gera campos de entrada para uma matriz com estilo melhorado
 */
function gerarCamposMatrizEstilizado($linhas, $colunas, $prefixo = 'matriz') {
    $html = "<div class='matrix-grid'>";
    
    for ($i = 0; $i < $linhas; $i++) {
        $html .= "<div class='matrix-row'>";
        for ($j = 0; $j < $colunas; $j++) {
            $nome = $prefixo . "_" . $i . "_" . $j;
            $html .= "<div class='matrix-cell'>";
            $html .= "<input type='number' step='any' name='$nome' placeholder='0' class='matrix-input'>";
            $html .= "<div class='cell-label'>" . strtoupper($prefixo) . "[" . ($i+1) . "," . ($j+1) . "]</div>";
            $html .= "</div>";
        }
        $html .= "</div>";
    }
    
    $html .= "</div>";
    return $html;
}

// ============================================
// FUNÇÕES DE OPERAÇÕES MATEMÁTICAS
// ============================================


/**
 * Soma ou subtrai duas matrizes
 */
function operarMatrizes($matrizA, $matrizB, $operacao = 'soma') {
    $linhas = count($matrizA);
    $colunas = count($matrizA[0]);
    $resultado = [];
    
    for ($i = 0; $i < $linhas; $i++) {
        $resultado[$i] = [];
        for ($j = 0; $j < $colunas; $j++) {
            if ($operacao == 'soma') {
                $resultado[$i][$j] = $matrizA[$i][$j] + $matrizB[$i][$j];
            } else {
                $resultado[$i][$j] = $matrizA[$i][$j] - $matrizB[$i][$j];
            }
        }
    }
    
    return $resultado;
}

/**
 * Multiplica uma matriz por escalar 
 */
function multiplicarPorEscalar($matriz, $escalar) {
    $linhas = count($matriz);
    $colunas = count($matriz[0]);
    $resultado = [];

    for ($i = 0; $i < $linhas; $i++) {
        $resultado[$i] = [];
        for ($j = 0; $j < $colunas; $j++) {
            $resultado[$i][$j] = $matriz[$i][$j] * $escalar;
        }
    }

    return $resultado;
}
/**
 * Multiplica duas matrizes
 */
function multiplicarMatrizes($matrizA, $matrizB) {
    $linhasA = count($matrizA);
    $colunasA = count($matrizA[0]);
    $linhasB = count($matrizB);
    $colunasB = count($matrizB[0]);
    
    if ($colunasA != $linhasB) {
        return false; // Dimensões incompatíveis
    }
    
    $resultado = array_fill(0, $linhasA, array_fill(0, $colunasB, 0));
    
    for ($i = 0; $i < $linhasA; $i++) {
        for ($j = 0; $j < $colunasB; $j++) {
            for ($k = 0; $k < $colunasA; $k++) {
                $resultado[$i][$j] += $matrizA[$i][$k] * $matrizB[$k][$j];
            }
        }
    }
    
    return $resultado;
}


function calcularDeterminante($matriz) {
    $n = count($matriz);
    
    // Verificar se é matriz quadrada
    if ($n != count($matriz[0])) {
        return "Erro: A matriz não é quadrada";
    }
    
    if ($n == 1) {
        return $matriz[0][0];
    }
    elseif ($n == 2) {
        return $matriz[0][0] * $matriz[1][1] - $matriz[0][1] * $matriz[1][0];
    }
    elseif ($n == 3) {
        return $matriz[0][0] * ($matriz[1][1] * $matriz[2][2] - $matriz[1][2] * $matriz[2][1])
             - $matriz[0][1] * ($matriz[1][0] * $matriz[2][2] - $matriz[1][2] * $matriz[2][0])
             + $matriz[0][2] * ($matriz[1][0] * $matriz[2][1] - $matriz[1][1] * $matriz[2][0]);
    }
    elseif ($n == 4) {
        return $matriz[0][0] * ($matriz[1][1] * ($matriz[2][2] * $matriz[3][3] - $matriz[2][3] * $matriz[3][2]) - $matriz[1][2] * ($matriz[2][1] * $matriz[3][3] - $matriz[2][3] * $matriz[3][1]) + $matriz[1][3] * ($matriz[2][1] * $matriz[3][2] - $matriz[2][2] * $matriz[3][1]))
             - $matriz[0][1] * ($matriz[1][0] * ($matriz[2][2] * $matriz[3][3] - $matriz[2][3] * $matriz[3][2]) - $matriz[1][2] * ($matriz[2][0] * $matriz[3][3] - $matriz[2][3] * $matriz[3][0]) + $matriz[1][3] * ($matriz[2][0] * $matriz[3][2] - $matriz[2][2] * $matriz[3][0]))
             + $matriz[0][2] * ($matriz[1][0] * ($matriz[2][1] * $matriz[3][3] - $matriz[2][3] * $matriz[3][1]) - $matriz[1][1] * ($matriz[2][0] * $matriz[3][3] - $matriz[2][3] * $matriz[3][0]) + $matriz[1][3] * ($matriz[2][0] * $matriz[3][1] - $matriz[2][1] * $matriz[3][0]))
             - $matriz[0][3] * ($matriz[1][0] * ($matriz[2][1] * $matriz[3][2] - $matriz[2][2] * $matriz[3][1]) - $matriz[1][1] * ($matriz[2][0] * $matriz[3][2] - $matriz[2][2] * $matriz[3][0]) + $matriz[1][2] * ($matriz[2][0] * $matriz[3][1] - $matriz[2][1] * $matriz[3][0]));
    }
    elseif ($n == 5) {
        return $matriz[0][0] * ($matriz[1][1] * ($matriz[2][2] * ($matriz[3][3] * $matriz[4][4] - $matriz[3][4] * $matriz[4][3]) - $matriz[2][3] * ($matriz[3][2] * $matriz[4][4] - $matriz[3][4] * $matriz[4][2]) + $matriz[2][4] * ($matriz[3][2] * $matriz[4][3] - $matriz[3][3] * $matriz[4][2])) - $matriz[1][2] * ($matriz[2][1] * ($matriz[3][3] * $matriz[4][4] - $matriz[3][4] * $matriz[4][3]) - $matriz[2][3] * ($matriz[3][1] * $matriz[4][4] - $matriz[3][4] * $matriz[4][1]) + $matriz[2][4] * ($matriz[3][1] * $matriz[4][3] - $matriz[3][3] * $matriz[4][1])) + $matriz[1][3] * ($matriz[2][1] * ($matriz[3][2] * $matriz[4][4] - $matriz[3][4] * $matriz[4][2]) - $matriz[2][2] * ($matriz[3][1] * $matriz[4][4] - $matriz[3][4] * $matriz[4][1]) + $matriz[2][4] * ($matriz[3][1] * $matriz[4][2] - $matriz[3][2] * $matriz[4][1])) - $matriz[1][4] * ($matriz[2][1] * ($matriz[3][2] * $matriz[4][3] - $matriz[3][3] * $matriz[4][2]) - $matriz[2][2] * ($matriz[3][1] * $matriz[4][3] - $matriz[3][3] * $matriz[4][1]) + $matriz[2][3] * ($matriz[3][1] * $matriz[4][2] - $matriz[3][2] * $matriz[4][1])))
             - $matriz[0][1] * ($matriz[1][0] * ($matriz[2][2] * ($matriz[3][3] * $matriz[4][4] - $matriz[3][4] * $matriz[4][3]) - $matriz[2][3] * ($matriz[3][2] * $matriz[4][4] - $matriz[3][4] * $matriz[4][2]) + $matriz[2][4] * ($matriz[3][2] * $matriz[4][3] - $matriz[3][3] * $matriz[4][2])) - $matriz[1][2] * ($matriz[2][0] * ($matriz[3][3] * $matriz[4][4] - $matriz[3][4] * $matriz[4][3]) - $matriz[2][3] * ($matriz[3][0] * $matriz[4][4] - $matriz[3][4] * $matriz[4][0]) + $matriz[2][4] * ($matriz[3][0] * $matriz[4][3] - $matriz[3][3] * $matriz[4][0])) + $matriz[1][3] * ($matriz[2][0] * ($matriz[3][2] * $matriz[4][4] - $matriz[3][4] * $matriz[4][2]) - $matriz[2][2] * ($matriz[3][0] * $matriz[4][4] - $matriz[3][4] * $matriz[4][0]) + $matriz[2][4] * ($matriz[3][0] * $matriz[4][2] - $matriz[3][2] * $matriz[4][0])) - $matriz[1][4] * ($matriz[2][0] * ($matriz[3][2] * $matriz[4][3] - $matriz[3][3] * $matriz[4][2]) - $matriz[2][2] * ($matriz[3][0] * $matriz[4][3] - $matriz[3][3] * $matriz[4][0]) + $matriz[2][3] * ($matriz[3][0] * $matriz[4][2] - $matriz[3][2] * $matriz[4][0])))
             + $matriz[0][2] * ($matriz[1][0] * ($matriz[2][1] * ($matriz[3][3] * $matriz[4][4] - $matriz[3][4] * $matriz[4][3]) - $matriz[2][3] * ($matriz[3][1] * $matriz[4][4] - $matriz[3][4] * $matriz[4][1]) + $matriz[2][4] * ($matriz[3][1] * $matriz[4][3] - $matriz[3][3] * $matriz[4][1])) - $matriz[1][1] * ($matriz[2][0] * ($matriz[3][3] * $matriz[4][4] - $matriz[3][4] * $matriz[4][3]) - $matriz[2][3] * ($matriz[3][0] * $matriz[4][4] - $matriz[3][4] * $matriz[4][0]) + $matriz[2][4] * ($matriz[3][0] * $matriz[4][3] - $matriz[3][3] * $matriz[4][0])) + $matriz[1][3] * ($matriz[2][0] * ($matriz[3][1] * $matriz[4][4] - $matriz[3][4] * $matriz[4][1]) - $matriz[2][1] * ($matriz[3][0] * $matriz[4][4] - $matriz[3][4] * $matriz[4][0]) + $matriz[2][4] * ($matriz[3][0] * $matriz[4][1] - $matriz[3][1] * $matriz[4][0])) - $matriz[1][4] * ($matriz[2][0] * ($matriz[3][1] * $matriz[4][3] - $matriz[3][3] * $matriz[4][1]) - $matriz[2][1] * ($matriz[3][0] * $matriz[4][3] - $matriz[3][3] * $matriz[4][0]) + $matriz[2][3] * ($matriz[3][0] * $matriz[4][1] - $matriz[3][1] * $matriz[4][0])))
             - $matriz[0][3] * ($matriz[1][0] * ($matriz[2][1] * ($matriz[3][2] * $matriz[4][4] - $matriz[3][4] * $matriz[4][2]) - $matriz[2][2] * ($matriz[3][1] * $matriz[4][4] - $matriz[3][4] * $matriz[4][1]) + $matriz[2][4] * ($matriz[3][1] * $matriz[4][2] - $matriz[3][2] * $matriz[4][1])) - $matriz[1][1] * ($matriz[2][0] * ($matriz[3][2] * $matriz[4][4] - $matriz[3][4] * $matriz[4][2]) - $matriz[2][2] * ($matriz[3][0] * $matriz[4][4] - $matriz[3][4] * $matriz[4][0]) + $matriz[2][4] * ($matriz[3][0] * $matriz[4][2] - $matriz[3][2] * $matriz[4][0])) + $matriz[1][2] * ($matriz[2][0] * ($matriz[3][1] * $matriz[4][4] - $matriz[3][4] * $matriz[4][1]) - $matriz[2][1] * ($matriz[3][0] * $matriz[4][4] - $matriz[3][4] * $matriz[4][0]) + $matriz[2][4] * ($matriz[3][0] * $matriz[4][1] - $matriz[3][1] * $matriz[4][0])) - $matriz[1][4] * ($matriz[2][0] * ($matriz[3][1] * $matriz[4][2] - $matriz[3][2] * $matriz[4][1]) - $matriz[2][1] * ($matriz[3][0] * $matriz[4][2] - $matriz[3][2] * $matriz[4][0]) + $matriz[2][2] * ($matriz[3][0] * $matriz[4][1] - $matriz[3][1] * $matriz[4][0])))
             + $matriz[0][4] * ($matriz[1][0] * ($matriz[2][1] * ($matriz[3][2] * $matriz[4][3] - $matriz[3][3] * $matriz[4][2]) - $matriz[2][2] * ($matriz[3][1] * $matriz[4][3] - $matriz[3][3] * $matriz[4][1]) + $matriz[2][3] * ($matriz[3][1] * $matriz[4][2] - $matriz[3][2] * $matriz[4][1])) - $matriz[1][1] * ($matriz[2][0] * ($matriz[3][2] * $matriz[4][3] - $matriz[3][3] * $matriz[4][2]) - $matriz[2][2] * ($matriz[3][0] * $matriz[4][3] - $matriz[3][3] * $matriz[4][0]) + $matriz[2][3] * ($matriz[3][0] * $matriz[4][2] - $matriz[3][2] * $matriz[4][0])) + $matriz[1][2] * ($matriz[2][0] * ($matriz[3][1] * $matriz[4][3] - $matriz[3][3] * $matriz[4][1]) - $matriz[2][1] * ($matriz[3][0] * $matriz[4][3] - $matriz[3][3] * $matriz[4][0]) + $matriz[2][3] * ($matriz[3][0] * $matriz[4][1] - $matriz[3][1] * $matriz[4][0])) - $matriz[1][3] * ($matriz[2][0] * ($matriz[3][1] * $matriz[4][2] - $matriz[3][2] * $matriz[4][1]) - $matriz[2][1] * ($matriz[3][0] * $matriz[4][2] - $matriz[3][2] * $matriz[4][0]) + $matriz[2][2] * ($matriz[3][0] * $matriz[4][1] - $matriz[3][1] * $matriz[4][0])));
    }
    
    return "Implementação apenas para matrizes até 5x5";
}

/**
 * Tabela de conversão para criptografia
 */
function getTabelaCriptografia() {
    return [
        'A' => 1,  'B' => 2,  'C' => 3,  'D' => 4,  'E' => 5,
        'F' => 6,  'G' => 7,  'H' => 8,  'I' => 9,  'J' => 10,
        'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15,
        'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20,
        'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25,
        'Z' => 26, '.' => 27, ',' => 28, '_' => 29, '¬' => 30,
        ' ' => 29  // Espaço também é 29
    ];
}

/**
 * Converte texto para matriz numérica
 */
function textoParaMatriz($texto, $linhas = 2) {
    $tabela = getTabelaCriptografia();
    $texto = strtoupper($texto);
    $numeros = [];
    
    // Converter cada caractere para número
    for ($i = 0; $i < strlen($texto); $i++) {
        $char = $texto[$i];
        $numeros[] = isset($tabela[$char]) ? $tabela[$char] : 29; // Usa espaço (_) se não encontrado
    }
    
    // Ajustar para múltiplo de $linhas
    while (count($numeros) % $linhas != 0) {
        $numeros[] = 29; // Adiciona espaços
    }
    
    // Criar matriz
    $colunas = count($numeros) / $linhas;
    $matriz = [];
    
    for ($i = 0; $i < $linhas; $i++) {
        $matriz[$i] = [];
        for ($j = 0; $j < $colunas; $j++) {
            $matriz[$i][$j] = $numeros[$i * $colunas + $j];
        }
    }
    
    return $matriz;
}

/**
 * Converte matriz numérica para texto
 */
function matrizParaTexto($matriz) {
    $tabela = array_flip(getTabelaCriptografia());
    $texto = '';
    
    $linhas = count($matriz);
    $colunas = count($matriz[0]);
    
    for ($j = 0; $j < $colunas; $j++) {
        for ($i = 0; $i < $linhas; $i++) {
            $numero = round($matriz[$i][$j]);
            $texto .= isset($tabela[$numero]) ? $tabela[$numero] : '';
        }
    }
    
    return $texto;
}

/**
 * Criptografa uma mensagem usando uma matriz codificadora
 */
function criptografarMensagem($mensagem, $matrizCodificadora) {
    $matrizMensagem = textoParaMatriz($mensagem, count($matrizCodificadora));
    
    if (!$matrizMensagem) {
        return false;
    }
    
    $resultado = multiplicarMatrizes($matrizCodificadora, $matrizMensagem);
    return $resultado;
}

function descriptografarMensagem($matrizCriptografada, $matrizDescodificadora) {
    return multiplicarMatrizes($matrizDescodificadora, $matrizCriptografada);
}