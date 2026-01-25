<?php
session_start();
include_once '../includes/functions.php';

$resultado = '';
$erro = '';
$matriz = [];
$determinante = null;
$dimensao = 2;

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['configurar'])) {
        // Configurar dimensão
        $dimensao = intval($_POST['dimensao'] ?? 2);

        if ($dimensao < 1 || $dimensao > 5) {
            $erro = "Esta calculadora suporta determinantes apenas para matrizes 1x1, 2x2, 3x3, 4x4 e 5x5.";
        }

        // Salvar na sessão
        $_SESSION['dimensao'] = $dimensao;
    } elseif (isset($_POST['calcular'])) {
        // Calcular determinante
        $dimensao = $_SESSION['dimensao'] ?? 2;

        // Criar matriz a partir dos dados
        $matriz = criarMatriz($_POST, $dimensao, $dimensao, 'matriz');

        // Validar se todos os campos foram preenchidos
        $camposVazios = false;
        for ($i = 0; $i < $dimensao; $i++) {
            for ($j = 0; $j < $dimensao; $j++) {
                if (!isset($_POST["matriz_{$i}_{$j}"]) || $_POST["matriz_{$i}_{$j}"] === '') {
                    $camposVazios = true;
                    break 2;
                }
            }
        }

        if ($camposVazios) {
            $erro = "Por favor, preencha todos os campos da matriz.";
        } else {
            // Calcular determinante
            $determinante = calcularDeterminante($matriz);

            if (is_string($determinante) && strpos($determinante, 'Erro') !== false) {
                $erro = $determinante;
                $determinante = null;
            }
        }
    }
} else {
    // Valores padrão
    $dimensao = $_SESSION['dimensao'] ?? 2;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Determinante de Matriz</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/operations.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <!-- Cabeçalho -->
        <header class="operation-header">
            <div class="header-top">
                <div class="operation-title">
                    <h1>Determinante de Matriz</h1>
                </div>
                <a href="../index.php" class="back-btn">
                   <i class="fas fa-arrow-left"></i> Voltar ao Menu
                </a>
            </div>

            <div class="operation-instructions">
                <strong>Importante:</strong> Só existe determinante para matrizes quadradas (n×n).</p>
            </div>
        </header>

        <!-- Conteúdo Principal -->
        <main class="operation-main">
            <!-- Formulário de Configuração -->
            <section class="config-section">
                <div class="section-header">
                    <h2>Configuração</h2>
                </div>

                <?php if ($erro): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $erro; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="config-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dimensao">
                                Dimensão da Matriz (n×n)
                            </label>
                            <select name="dimensao" id="dimensao" class="form-select" onchange="atualizarFormula()">
                                <option value="1" <?php echo $dimensao == 1 ? 'selected' : ''; ?>>1×1</option>
                                <option value="2" <?php echo $dimensao == 2 ? 'selected' : ''; ?>>2×2</option>
                                <option value="3" <?php echo $dimensao == 3 ? 'selected' : ''; ?>>3×3</option>
                                <option value="4" <?php echo $dimensao == 4 ? 'selected' : ''; ?>>4×4</option>
                                <option value="5" <?php echo $dimensao == 5 ? 'selected' : ''; ?>>5×5</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="configurar" class="btn btn-primary">
                             <i class="fas fa-check"></i>Configurar Matriz
                        </button>
                    </div>
                </form>
            </section>

            <!-- Entrada de Dados -->
            <?php if (isset($_POST['configurar']) || isset($_SESSION['dimensao'])): ?>
                <section class="input-section">
                    <div class="section-header">
                        <h2>Entrada de Dados</h2>
                    </div>

                    <form method="POST" class="matrix-input-form">
                        <!-- Dados ocultos para manter configuração -->
                        <input type="hidden" name="dimensao" value="<?php echo $dimensao; ?>">

                        <div class="matrices-container">
                            <div class="matrix-box">
                                <div class="matrix-header">
                                    <h3> Matriz A</h3>
                                </div>
                                <div class="matrix-grid">
                                    <?php for ($i = 0; $i < $dimensao; $i++): ?>
                                        <div class="matrix-row">
                                            <?php for ($j = 0; $j < $dimensao; $j++):
                                                $nome = "matriz_{$i}_{$j}";
                                                $valor = isset($_POST[$nome]) ? $_POST[$nome] : '';
                                                ?>
                                                <div class="matrix-cell">
                                                    <input type="number" step="any" name="<?php echo $nome; ?>"
                                                        value="<?php echo $valor; ?>" placeholder="0" class="matrix-input">
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <!-- Operador Determinante -->
                            <div class="operator-box">
                                <div class="operator">
                                    <span>det(A)</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="calcular" class="btn btn-success btn-large">
                                <i class="fas fa-calculator"></i>Calcular Determinante
                            </button>

                            <?php if (isset($_POST['configurar']) || isset($_SESSION['dimensao'])): ?>
                                <button type="button" onclick="preencherAleatorio()" class="btn btn-secondary">
                                    <i class="fas fa-dice"></i>Preencher Aleatoriamente
                                </button>
                            <?php endif; ?>

                            <button type="reset" class="btn btn-warning">
                                <i class="fas fa-undo"></i>Limpar Campos
                            </button>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <!-- Resultados -->
            <?php if ($determinante !== null): ?>
                <section class="result-section">
                    <div class="section-header">
                        <h2>Resultado</h2>
                    </div>

                    <div class="result-container">
                        <!-- Matriz Original -->
                        <div class="original-matrices">
                            <div class="result-box">
                                <h3>Matriz A</h3>
                                <?php echo exibirMatrizHTML($matriz); ?>
                            </div>

                            <div class="operator-result">
                                <div class="operator-icon">
                                    <i class="fas fa-equals"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Resultado do Determinante -->
                        <div class="final-result">
                            <div class="result-box highlight">
                                <h3> Determinante det(A)</h3>
                                <div class="determinante-result">
                                    <div class="determinante-value">
                                        <span class="det-symbol">det(A) =</span>
                                        <span class="det-number"><?php echo number_format($determinante, 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="result-actions">
                        <!-- <button onclick="salvarResultado()" class="btn btn-secondary">
                         Salvar Resultado
                    </button> -->
                        <a href="?novo=1" class="btn btn-primary">
                            <i class="fas fa-redo"></i>Novo Cálculo
                        </a>
                    </div>
                </section>
            <?php endif; ?>
        </main>

        <!-- Rodapé da Operação -->
        <footer class="operation-footer">
            <div class="footer-copyright">
                <p>Determinante de Matriz - Projeto Interdisciplinar 2025/2026</p>
            </div>
        </footer>
    </div>

    <script>
        // Função para preencher valores aleatórios
        function preencherAleatorio() {
            <?php for ($i = 0; $i < $dimensao; $i++): ?>
                <?php for ($j = 0; $j < $dimensao; $j++): ?>
                    document.getElementsByName('matriz_<?php echo $i; ?>_<?php echo $j; ?>')[0].value = Math.floor(Math.random() * 20) - 10;
                <?php endfor; ?>
            <?php endfor; ?>

            // Mostrar mensagem
            alert('Matriz preenchida com valores aleatórios entre -10 e 10!');
        }
        // Função para salvar resultado (simulação)
        function salvarResultado() {
            alert('Funcionalidade de salvar resultado será implementada na versão final!');
        }

    </script>
    <style>
        .determinante-result {
            text-align: center;
            padding: 20px;
        }

        .determinante-value {
            margin: 20px 0;
            font-size: 1.5rem;
        }

        .det-symbol {
            font-weight: bold;
            color: #2c3e50;
            margin-right: 15px;
        }

        .det-number {
            font-size: 2rem;
            font-weight: bold;
            color: #329147;
            font-family: 'Courier New', monospace;
        }
    </style>
</body>

</html>
