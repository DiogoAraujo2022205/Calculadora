<?php
session_start();
include_once '../includes/functions.php';

$resultado = '';
$erro = '';
$matrizA = [];
$matrizB = [];
$matrizResultado = [];
$operacao = 'soma';
$linhas = 2;
$colunas = 2;

//processar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //soma ou subtracao
    $operacao = $_POST['operacao'] ?? 'soma';

    if (isset($_POST['configurar'])) {
        //ler as dimensoes
        $linhas = intval($_POST['linhas'] ?? 2);
        $colunas = intval($_POST['colunas'] ?? 2);

        //se as linhas ou culunas nao estiverem entre 1 e 6, da erro.
        if ($linhas < 1 || $linhas > 6 || $colunas < 1 || $colunas > 6) {
            $erro = "As dimensões devem estar entre 1x1 e 6x6 para melhor visualização.";
        }

        //guardar nas variáveis de sessão
        $_SESSION['linhas'] = $linhas;
        $_SESSION['colunas'] = $colunas;
        $_SESSION['operacao'] = $operacao;
    } elseif (isset($_POST['calcular'])) {
        //trazer os valores da sessao para a operação
        $linhas = $_SESSION['linhas'] ?? 2;
        $colunas = $_SESSION['colunas'] ?? 2;
        $operacao = $_SESSION['operacao'] ?? 'soma';

        //criar ad matrizes a partir dos dados
        $matrizA = criarMatriz($_POST, $linhas, $colunas, 'matrizA');
        $matrizB = criarMatriz($_POST, $linhas, $colunas, 'matrizB');

        //validar se todos os campos foram preenchidos
        $camposVazios = false;
        for ($i = 0; $i < $linhas; $i++) {
            for ($j = 0; $j < $colunas; $j++) {
                if (
                    !isset($_POST["matrizA_{$i}_{$j}"]) || $_POST["matrizA_{$i}_{$j}"] === '' ||
                    !isset($_POST["matrizB_{$i}_{$j}"]) || $_POST["matrizB_{$i}_{$j}"] === ''
                ) {
                    $camposVazios = true;
                    break 2;
                }
            }
        }

        if ($camposVazios) {
            $erro = "Preencha todos os campos das matrizes.";
        } else {
            //executar a operação
            $matrizResultado = operarMatrizes($matrizA, $matrizB, $operacao);
        }
    }
} else {
    //valores padrão
    $linhas = $_SESSION['linhas'] ?? 2;
    $colunas = $_SESSION['colunas'] ?? 2;
    $operacao = $_SESSION['operacao'] ?? 'soma';
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soma e Subtração de Matrizes</title>
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
                    <h1>Soma e Subtração de Matrizes</h1>
                </div>
                <a href="../index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Voltar ao Menu
                </a>
            </div>

            <div class="operation-instructions">
                <p><strong>Importante:</strong> Só é possível somar ou subtrair matrizes com as mesmas dimensões.</p>
            </div>
        </header>

        <!-- Conteúdo Principal -->
        <main class="operation-main">
            <!-- Formulário de Configuração -->
            <section class="config-section">
                <div class="section-header">
                    <h2> Configuração</h2>
                    <p>Defina a operação e as dimensões das matrizes</p>
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
                            <label for="operacao">
                                Operação
                            </label>
                            <select name="operacao" id="operacao" class="form-select">
                                <option value="soma" <?php echo $operacao == 'soma' ? 'selected' : ''; ?>>Soma (A + B)
                                </option>
                                <option value="subtracao" <?php echo $operacao == 'subtracao' ? 'selected' : ''; ?>>
                                    Subtração (A - B)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="linhas">
                                Número de Linhas
                            </label>
                            <input type="number" name="linhas" id="linhas" min="1" max="6"
                                value="<?php echo $linhas; ?>" required class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="colunas">
                                Número de Colunas
                            </label>
                            <input type="number" name="colunas" id="colunas" min="1" max="6"
                                value="<?php echo $colunas; ?>" required class="form-input">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="configurar" class="btn btn-primary">
                            <i class="fas fa-check"></i> Configurar Matrizes
                        </button>
                    </div>
                </form>
            </section>

            <!-- Entrada de Dados -->
            <?php if (isset($_POST['configurar']) || isset($_SESSION['linhas'])): ?>
                <section class="input-section">
                    <div class="section-header">
                        <h2> Entrada de Dados</h2>
                    </div>

                    <form method="POST" class="matrix-input-form">
                        <!-- Dados ocultos para manter configuração -->
                        <input type="hidden" name="operacao" value="<?php echo $operacao; ?>">
                        <input type="hidden" name="linhas" value="<?php echo $linhas; ?>">
                        <input type="hidden" name="colunas" value="<?php echo $colunas; ?>">

                        <div class="matrices-container">
                            <!-- Matriz A -->
                            <div class="matrix-box">
                                <div class="matrix-header">
                                    <h3> Matriz A</h3>
                                </div>
                                <div class="matrix-grid">
                                    <?php for ($i = 0; $i < $linhas; $i++): ?>
                                        <div class="matrix-row">
                                            <?php for ($j = 0; $j < $colunas; $j++):
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
                                <div class="operator-icon">
                                    <?php if ($operacao == 'soma'): ?>
                                        <i class="fas fa-plus"></i>
                                    <?php else: ?>
                                        <i class="fas fa-minus"></i>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Matriz B -->
                            <div class="matrix-box">
                                <div class="matrix-header">
                                    <h3> Matriz B</h3>
                                </div>
                                <div class="matrix-grid">
                                    <?php for ($i = 0; $i < $linhas; $i++): ?>
                                        <div class="matrix-row">
                                            <?php for ($j = 0; $j < $colunas; $j++):
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
                            <button type="submit" name="calcular" class="btn">
                                <i class="fas fa-calculator"></i> Calcular <?php echo $operacao == 'soma' ? 'Soma' : 'Subtração'; ?>
                        </button>

                            <?php if (isset($_POST['configurar']) || isset($_SESSION['linhas'])): ?>
                                <button type="button" onclick="preencherAleatorio()" class="btn btn-secondary">
                                <i class="fas fa-dice"></i> Preencher Aleatoriamente
                            </button>
                            <?php endif; ?>

                             <button type="reset" class="btn btn-warning">
                            <i class="fas fa-undo"></i> Limpar Campos
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
                                <h3> Matriz A</h3>
                                <?php echo exibirMatrizHTML($matrizA); ?>
                            </div>

                            <div class="operator-icon">
                                <?php if ($operacao == 'soma'): ?>
                                    <div>
                                        <i class="fas fa-plus"></i>
                                    </div>
                                <?php else: ?>
                                    <div>
                                        <i class="fas fa-minus"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="result-box">
                                <h3> Matriz B</h3>
                                <?php echo exibirMatrizHTML($matrizB); ?>
                            </div>
                        </div>

                        <!-- Matriz Resultado -->
                        <div class="final-result">
                            <div class="result-box highlight">
                                <h3> Matriz Resultado</h3>
                                <?php echo exibirMatrizHTML($matrizResultado); ?>
                            </div>
                        </div>
                    </div>

                    <div class="result-actions">
                        <form method="POST" action="salvar_resultado.php" target="_blank">
                            <input type="hidden" name="matriz_resultado"
                                value='<?php echo json_encode($matrizResultado); ?>'>
                            <input type="hidden" name="formato" value="json">
                            <button type="submit" class="btn">
                                <i class="fas fa-save"></i> Salvar JSON Criptografado
                            </button>
                        </form>

                        <!-- Formulário para HTML -->
                        <form method="POST" action="salvar_resultado.php" target="_blank">
                            <input type="hidden" name="matriz_resultado"
                                value='<?php echo json_encode($matrizResultado); ?>'>
                            <input type="hidden" name="formato" value="html">
                            <button type="submit" class="btn">
                                <i class="fas fa-file-alt"></i> Salvar HTML Criptografado
                            </button>
                        </form>
                       <a href="?novo=1" class="btn btn-primary">
                        <i class="fas fa-redo"></i> Novo Cálculo
                    </a>
                    </div>
                </section>
            <?php endif; ?>
        </main>

        <!-- Rodapé da Operação -->
        <footer class="operation-footer">
            <div>
                <p>Soma e Subtração de Matrizes - Projeto Interdisciplinar 2025/2026</p>
            </div>
        </footer>
    </div>

    <script>
        // Função para preencher valores aleatórios
        function preencherAleatorio() {
            // Preencher Matriz A
            <?php for ($i = 0; $i < $linhas; $i++): ?>
                <?php for ($j = 0; $j < $colunas; $j++): ?>
                    document.getElementsByName('matrizA_<?php echo $i; ?>_<?php echo $j; ?>')[0].value = Math.floor(Math.random() * 20) - 10;
                <?php endfor; ?>
            <?php endfor; ?>

            // Preencher Matriz B
            <?php for ($i = 0; $i < $linhas; $i++): ?>
                <?php for ($j = 0; $j < $colunas; $j++): ?>
                    document.getElementsByName('matrizB_<?php echo $i; ?>_<?php echo $j; ?>')[0].value = Math.floor(Math.random() * 20) - 10;
                <?php endfor; ?>
            <?php endfor; ?>

            // Mostrar mensagem
            alert('Matrizes preenchidas com valores aleatórios entre -10 e 10!');
        }

    </script>
</body>

</html>
