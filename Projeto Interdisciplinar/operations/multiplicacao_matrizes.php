<?php
session_start();
include_once '../includes/functions.php';

$resultado = '';
$erro = '';
$matrizA = [];
$matrizB = [];
$matrizResultado = [];
$linhasA = 2;
$colunasA = 2;
$linhasB = 2;
$colunasB = 2;

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['configurar'])) {
        // Configurar dimensões
        $linhasA = intval($_POST['linhasA'] ?? 2);
        $colunasA = intval($_POST['colunasA'] ?? 2);
        $linhasB = intval($_POST['linhasB'] ?? 2);
        $colunasB = intval($_POST['colunasB'] ?? 2);

        // Validar compatibilidade
        if ($colunasA != $linhasB) {
            $erro = "Erro: O número de colunas da Matriz A ($colunasA) deve ser igual ao número de linhas da Matriz B ($linhasB)";
        }

        if (
            $linhasA < 1 || $linhasA > 6 || $colunasA < 1 || $colunasA > 6 ||
            $linhasB < 1 || $linhasB > 6 || $colunasB < 1 || $colunasB > 6
        ) {
            $erro = "As dimensões devem estar entre 1x1 e 6x6 para melhor visualização.";
        }

        // Salvar nas variáveis de sessão
        $_SESSION['linhasA'] = $linhasA;
        $_SESSION['colunasA'] = $colunasA;
        $_SESSION['linhasB'] = $linhasB;
        $_SESSION['colunasB'] = $colunasB;
    } elseif (isset($_POST['calcular'])) {
        // Calcular operação
        $linhasA = $_SESSION['linhasA'] ?? 2;
        $colunasA = $_SESSION['colunasA'] ?? 2;
        $linhasB = $_SESSION['linhasB'] ?? 2;
        $colunasB = $_SESSION['colunasB'] ?? 2;

        // Criar matrizes a partir dos dados
        $matrizA = criarMatriz($_POST, $linhasA, $colunasA, 'matrizA');
        $matrizB = criarMatriz($_POST, $linhasB, $colunasB, 'matrizB');

        // Validar se todos os campos foram preenchidos
        $camposVazios = false;
        for ($i = 0; $i < $linhasA; $i++) {
            for ($j = 0; $j < $colunasA; $j++) {
                if (!isset($_POST["matrizA_{$i}_{$j}"]) || $_POST["matrizA_{$i}_{$j}"] === '') {
                    $camposVazios = true;
                    break 2;
                }
            }
        }
        for ($i = 0; $i < $linhasB; $i++) {
            for ($j = 0; $j < $colunasB; $j++) {
                if (!isset($_POST["matrizB_{$i}_{$j}"]) || $_POST["matrizB_{$i}_{$j}"] === '') {
                    $camposVazios = true;
                    break 2;
                }
            }
        }

        if ($camposVazios) {
            $erro = "Por favor, preencha todos os campos das matrizes.";
        } else {
            // Realizar operação
            $matrizResultado = multiplicarMatrizes($matrizA, $matrizB);

            if ($matrizResultado === false) {
                $erro = "Erro: As matrizes não são compatíveis para multiplicação. O número de colunas de A ($colunasA) deve ser igual ao número de linhas de B ($linhasB).";
                $matrizResultado = [];
            }
        }
    }
} else {
    // Valores padrão
    $linhasA = $_SESSION['linhasA'] ?? 2;
    $colunasA = $_SESSION['colunasA'] ?? 2;
    $linhasB = $_SESSION['linhasB'] ?? 2;
    $colunasB = $_SESSION['colunasB'] ?? 2;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multiplicação de Matrizes</title>
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
                    <h1>Multiplicação de Matrizes</h1>
                </div>

                <a href="../index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Voltar ao Menu
                </a>

            </div>

            <div class="operation-instructions">
                <p><i class="fas fa-exclamation-triangle"></i> <strong>Regra:</strong> A multiplicação A×B só é possível
                    se o número de colunas de A for igual ao número de linhas de B.</p>
            </div>
        </header>

        <!-- Conteúdo Principal -->
        <main class="operation-main">
            <!-- Formulário de Configuração -->
            <section class="config-section">
                <div class="section-header">
                    <h2> Configuração</h2>
                    <p>Defina as dimensões das matrizes A e B</p>
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
                            <label for="linhasA">
                                 Linhas de A
                            </label>
                            <input type="number" name="linhasA" id="linhasA" min="1" max="6"
                                value="<?php echo $linhasA; ?>" required class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="colunasA">
                                </i> Colunas de A
                            </label>
                            <input type="number" name="colunasA" id="colunasA" min="1" max="6"
                                value="<?php echo $colunasA; ?>" required class="form-input">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="linhasB">
                                Linhas de B
                            </label>
                            <input type="number" name="linhasB" id="linhasB" min="1" max="6"
                                value="<?php echo $linhasB; ?>" required class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="colunasB">
                                Colunas de B
                            </label>
                            <input type="number" name="colunasB" id="colunasB" min="1" max="6"
                                value="<?php echo $colunasB; ?>" required class="form-input">
                        </div>
                    </div>

                    <div class="compatibility-check">
                        <div
                            class="compatibility-result <?php echo ($colunasA == $linhasB) ? 'compativel' : 'incompativel'; ?>">
                            <i
                                class="fas fa-<?php echo ($colunasA == $linhasB) ? 'check-circle' : 'times-circle'; ?>"></i>
                            <span>
                                <?php if ($colunasA == $linhasB): ?>
                                    Compatível: <?php echo $colunasA; ?> colunas de A = <?php echo $linhasB; ?> linhas de B
                                <?php else: ?>
                                    Incompatível: <?php echo $colunasA; ?> colunas de A ≠ <?php echo $linhasB; ?> linhas de
                                    B
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="configurar" class="btn btn-primary">
                             Configurar Matrizes
                        </button>

                        <?php if (isset($_POST['configurar']) || (isset($_SESSION['linhasA']) && $colunasA == $linhasB)): ?>
                            <button type="button" onclick="preencherAleatorio()" class="btn btn-secondary">
                                 Preencher Aleatoriamente
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <!-- Entrada de Dados -->
            <?php if ((isset($_POST['configurar']) || isset($_SESSION['linhasA'])) && $colunasA == $linhasB): ?>
                <section class="input-section">
                    <div class="section-header">
                        <h2> Entrada de Dados</h2>
                    </div>

                    <form method="POST" class="matrix-input-form">
                        <!-- Dados ocultos para manter configuração -->
                        <input type="hidden" name="linhasA" value="<?php echo $linhasA; ?>">
                        <input type="hidden" name="colunasA" value="<?php echo $colunasA; ?>">
                        <input type="hidden" name="linhasB" value="<?php echo $linhasB; ?>">
                        <input type="hidden" name="colunasB" value="<?php echo $colunasB; ?>">

                        <div class="matrices-container">
                            <!-- Matriz A -->
                            <div class="matrix-box">
                                <div class="matrix-header">
                                    <h3> Matriz A</h3>
                                    <div class="matrix-dimensions"></div>
                                </div>
                                <div class="matrix-grid">
                                    <?php for ($i = 0; $i < $linhasA; $i++): ?>
                                        <div class="matrix-row">
                                            <?php for ($j = 0; $j < $colunasA; $j++):
                                                $nome = "matrizA_{$i}_{$j}";
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

                            <!-- Operador -->
                            <div class="operator-box">
                                <div class="operator">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>

                            <!-- Matriz B -->
                            <div class="matrix-box">
                                <div class="matrix-header">
                                    <h3> Matriz B</h3>
                                    <div class="matrix-dimensions"></div>
                                </div>
                                <div class="matrix-grid">
                                    <?php for ($i = 0; $i < $linhasB; $i++): ?>
                                        <div class="matrix-row">
                                            <?php for ($j = 0; $j < $colunasB; $j++):
                                                $nome = "matrizB_{$i}_{$j}";
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
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="calcular" class="btn btn-success btn-large">
                                Calcular Multiplicação
                            </button>
                            <button type="reset" class="btn btn-warning">
                                Limpar Campos
                            </button>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <!-- Resultados -->
            <?php if (!empty($matrizResultado)): ?>
                <section class="result-section">
                    <div class="section-header">
                        <h2> Resultado</h2>
                    </div>

                    <div class="result-container">
                        <!-- Matrizes Originais -->
                        <div class="original-matrices">
                            <div class="result-box">
                                <h3>Matriz A</h3>
                                <?php echo exibirMatrizHTML($matrizA); ?>
                                <div class="matrix-info">
                                </div>
                            </div>

                            <div class="operator-result">
                                <div class="operator-icon">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>

                            <div class="result-box">
                                <h3>Matriz B</h3>
                                <?php echo exibirMatrizHTML($matrizB); ?>
                                <div class="matrix-info">
                                </div>
                            </div>

                            <div class="operator-result">
                                <div class="operator-icon">
                                    <i class="fas fa-equals"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Matriz Resultado -->
                        <div class="final-result">
                            <div class="result-box highlight">
                                <h3>Matriz Resultado (A × B)</h3>
                                <?php echo exibirMatrizHTML($matrizResultado); ?>
                                <div class="result-info">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="result-actions">
                        <button onclick="salvarResultado()" class="btn btn-secondary">
                            Salvar Resultado
                        </button>
                        <a href="?novo=1" class="btn btn-primary">
                           Novo Cálculo
                        </a>
                    </div>
                </section>
            <?php endif; ?>
        </main>

        <!-- Rodapé da Operação -->
        <footer class="operation-footer">
            <div class="footer-copyright">
                <p>Multiplicação de Matrizes - Projeto Interdisciplinar 2025/2026</p>
            </div>
        </footer>
    </div>

    <script>
        // Função para preencher valores aleatórios
        function preencherAleatorio() {
            // Preencher Matriz A
            <?php for ($i = 0; $i < $linhasA; $i++): ?>
                <?php for ($j = 0; $j < $colunasA; $j++): ?>
                    document.getElementsByName('matrizA_<?php echo $i; ?>_<?php echo $j; ?>')[0].value = Math.floor(Math.random() * 10) + 1;
                <?php endfor; ?>
            <?php endfor; ?>

            // Preencher Matriz B
            <?php for ($i = 0; $i < $linhasB; $i++): ?>
                <?php for ($j = 0; $j < $colunasB; $j++): ?>
                    document.getElementsByName('matrizB_<?php echo $i; ?>_<?php echo $j; ?>')[0].value = Math.floor(Math.random() * 10) + 1;
                <?php endfor; ?>
            <?php endfor; ?>

            // Mostrar mensagem
            alert('Matrizes preenchidas com valores aleatórios entre 1 e 10!');
        }

        // Função para salvar resultado (simulação)
        function salvarResultado() {
            alert('Funcionalidade de salvar resultado será implementada na versão final!');
        }

        // Atualizar verificação de compatibilidade em tempo real
        document.getElementById('colunasA').addEventListener('input', verificarCompatibilidade);
        document.getElementById('linhasB').addEventListener('input', verificarCompatibilidade);

        function verificarCompatibilidade() {
            const colunasA = parseInt(document.getElementById('colunasA').value) || 0;
            const linhasB = parseInt(document.getElementById('linhasB').value) || 0;

            const compatDiv = document.querySelector('.compatibility-result');
            if (compatDiv) {
                if (colunasA === linhasB) {
                    compatDiv.className = 'compatibility-result compativel';
                    compatDiv.innerHTML = `<i class="fas fa-check-circle"></i>
                                         <span>Compatível: ${colunasA} colunas de A = ${linhasB} linhas de B</span>`;
                } else {
                    compatDiv.className = 'compatibility-result incompativel';
                    compatDiv.innerHTML = `<i class="fas fa-times-circle"></i>
                                         <span>Incompatível: ${colunasA} colunas de A ≠ ${linhasB} linhas de B</span>`;
                }
            }
        }
        
    </script>
    <style>
        .compatibility-check {
            margin: 20px 0;
        }

        .compatibility-result {
            padding: 12px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: bold;
        }

        .compatibility-result.compativel {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .compatibility-result.incompativel {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .multiplication-info {
            background-color: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            border-left: 4px solid #3498db;
        }

        .multiplication-info p {
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
    </style>
</body>


</html>
