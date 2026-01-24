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
 * Multiplica uma matriz por um escalar
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
        return false; // Matrizes incompatíveis
    }
    
    $resultado = [];
    
    for ($i = 0; $i < $linhasA; $i++) {
        $resultado[$i] = [];
        for ($j = 0; $j < $colunasB; $j++) {
            $soma = 0;
            for ($k = 0; $k < $colunasA; $k++) {
                $soma += $matrizA[$i][$k] * $matrizB[$k][$j];
            }
            $resultado[$i][$j] = $soma;
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

function criptografarMatriz($matriz, $matrizCodificadora) {
    // Para simplificar, vamos assumir que a matriz codificadora é 2x2
    // Ajustar a matriz se necessário para compatibilidade
    
    $linhasMatriz = count($matriz);
    $colunasMatriz = count($matriz[0]);
    
    // Se a matriz não for compatível com 2x2, ajustamos
    if ($linhasMatriz < 2) {
        // Adicionar linhas extras
        for ($i = $linhasMatriz; $i < 2; $i++) {
            $matriz[$i] = array_fill(0, $colunasMatriz, 0);
        }
        $linhasMatriz = 2;
    }
    
    if ($colunasMatriz < 2) {
        // Adicionar colunas extras
        for ($i = 0; $i < $linhasMatriz; $i++) {
            for ($j = count($matriz[$i]); $j < 2; $j++) {
                $matriz[$i][$j] = 0;
            }
        }
        $colunasMatriz = 2;
    }
    
    // Multiplicar matriz codificadora (2x2) × matriz ajustada
    $resultado = [];
    for ($i = 0; $i < 2; $i++) { // linhas da matriz codificadora
        $resultado[$i] = [];
        for ($j = 0; $j < $colunasMatriz; $j++) { // colunas da matriz original
            $soma = 0;
            for ($k = 0; $k < 2; $k++) { // colunas da codificadora = linhas da original
                $soma += $matrizCodificadora[$i][$k] * $matriz[$k][$j];
            }
            $resultado[$i][$j] = $soma;
        }
    }
    
    return $resultado;
}

/**
 * Calcula o cofator de um elemento da matriz
 */
function calcularCofator($matriz, $linha, $coluna) {
    $n = count($matriz);
    $submatriz = [];
    
    for ($i = 0; $i < $n; $i++) {
        if ($i == $linha) continue;
        $submatriz[] = [];
        for ($j = 0; $j < $n; $j++) {
            if ($j == $coluna) continue;
            $submatriz[count($submatriz)-1][] = $matriz[$i][$j];
        }
    }
    
    $sinal = (($linha + $coluna) % 2 == 0) ? 1 : -1;
    return $sinal * calcularDeterminante($submatriz);
}

/**
 * Calcula a matriz adjunta (transposta da matriz de cofatores)
 */
function calcularAdjunta($matriz) {
    $n = count($matriz);
    $adjunta = [];
    
    for ($i = 0; $i < $n; $i++) {
        $adjunta[$i] = [];
        for ($j = 0; $j < $n; $j++) {
            $adjunta[$i][$j] = calcularCofator($matriz, $j, $i); // Transposta
        }
    }
    
    return $adjunta;
}

/**
 * Calcula a matriz inversa para qualquer tamanho (até 5×5)
 */
function calcularInversa($matriz) {
    $n = count($matriz);
    
    // Verificar se é matriz quadrada
    if ($n != count($matriz[0])) {
        return false;
    }
    
    // Calcular determinante
    $det = calcularDeterminante($matriz);
    
    if ($det == 0) {
        return false; // Matriz não é inversível
    }
    
    // Caso 1×1
    if ($n == 1) {
        return [[1 / $det]];
    }
    
    // Caso 2×2 (fórmula direta)
    if ($n == 2) {
        return [
            [$matriz[1][1] / $det, -$matriz[0][1] / $det],
            [-$matriz[1][0] / $det, $matriz[0][0] / $det]
        ];
    }
    
    // Caso 3×3, 4×4, 5×5 (método da adjunta)
    $adjunta = calcularAdjunta($matriz);
    $inversa = [];
    
    for ($i = 0; $i < $n; $i++) {
        $inversa[$i] = [];
        for ($j = 0; $j < $n; $j++) {
            $inversa[$i][$j] = $adjunta[$i][$j] / $det;
        }
    }
    
    return $inversa;
}

// ============================================
// FUNÇÕES PARA CRIPTOGRAFIA
// ============================================

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
