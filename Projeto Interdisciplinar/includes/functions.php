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
