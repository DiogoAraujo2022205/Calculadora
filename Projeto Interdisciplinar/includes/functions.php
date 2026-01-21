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
