<?php
session_start();
include_once '../includes/functions.php';

$erro = '';
$matriz = [];
$matrizInversa = [];
$determinante = null;
$dimensao = 2;

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['configurar'])) {
        // Configurar dimensão
        $dimensao = intval($_POST['dimensao'] ?? 2);

        if ($dimensao < 2 || $dimensao > 5) {
            $erro = "Esta calculadora suporta matriz inversa apenas para matrizes de 2×2 até 5×5.";
        }

        // Salvar na sessão (apenas dimensão)
        $_SESSION['dimensao'] = $dimensao;
    } elseif (isset($_POST['calcular'])) {
        // Calcular inversa
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

            if ($determinante == 0) {
                $erro = "A matriz não possui inversa porque seu determinante é zero (det = 0).";
            } else {
                // Calcular matriz inversa
                $matrizInversa = calcularInversa($matriz);

                if ($matrizInversa === false) {
                    $erro = "Não foi possível calcular a matriz inversa. Verifique se a matriz é quadrada e se o determinante não é zero.";
                }
                // NÃO precisa guardar na sessão - já tem $matrizInversa disponível!
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
    <title>Matriz Inversa</title>
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
                    <h1> Matriz Inversa (A⁻¹)</h1>
                </div>
                <a href="../index.php" class="back-btn">
                    Voltar ao Menu
                </a>
            </div>

            <div class="operation-instructions">
                <p><strong>Condição:</strong> A matriz inversa só existe se a matriz for quadrada e seu determinante for
                    diferente de zero.</p>
            </div>
        </header>

        <!-- Conteúdo Principal -->
        <main class="operation-main">
            <!-- Formulário de Configuração -->
            <section class="config-section">
                <div class="section-header">
                    <h2> Configuração</h2>
                    <p>Selecione a dimensão da matriz quadrada</p>
                </div>

                <?php if ($erro): ?>
                    <div class="error-message">
                        <?php echo $erro; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="config-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dimensao">
                                Dimensão da Matriz (n×n)
                            </label>
                            <select name="dimensao" id="dimensao" class="form-select">
                                <option value="2" <?php echo $dimensao == 2 ? 'selected' : ''; ?>>2×2</option>
                                <option value="3" <?php echo $dimensao == 3 ? 'selected' : ''; ?>>3×3</option>
                                <option value="4" <?php echo $dimensao == 4 ? 'selected' : ''; ?>>4×4</option>
                                <option value="5" <?php echo $dimensao == 5 ? 'selected' : ''; ?>>5×5</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="configurar" class="btn">
                            Configurar Matriz
                        </button>
                    </div>
                </form>
            </section>

            <!-- Entrada de Dados -->
            <?php if (isset($_POST['configurar']) || isset($_SESSION['dimensao'])): ?>
                <section class="input-section">
                    <div class="section-header">
                        <h2> Entrada de Dados</h2>
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
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="calcular" class="btn">
                                Calcular Matriz Inversa
                            </button>

                            <?php if (isset($_POST['configurar']) || isset($_SESSION['dimensao'])): ?>
                                <button type="button" onclick="preencherAleatorio()" class="btn">
                                    Preencher Aleatoriamente
                                </button>
                            <?php endif; ?>

                            <button type="reset" class="btn">
                                Limpar Campos
                            </button>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <!-- Resultados -->
            <?php if (!empty($matrizInversa)): ?>
                <section class="result-section">
                    <div class="section-header">
                        <h2> Resultado</h2>
                        <p>Matriz Inversa A⁻¹</p>
                    </div>

                    <div class="result-container">
                        <!-- Matriz Original -->
                        <div class="original-matrices">
                            <div class="result-box">
                                <h3> Matriz Original A</h3>
                                <?php echo exibirMatrizHTML($matriz); ?>
                            </div>

                            <div class="operator-result">
                                <div class="operator-icon">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>

                            <div class="result-box">
                                <h3>Matriz Inversa A⁻¹</h3>
                                <?php echo exibirMatrizHTML($matrizInversa); ?>
                            </div>
                        </div>

                    </div>


                    <div class="result-actions">
                        <form method="POST" action="salvar_resultado.php" target="_blank">
                            <input type="hidden" name="matriz_resultado" value='<?php echo json_encode($matrizInversa); ?>'>
                            <input type="hidden" name="formato" value="json">
                            <button type="submit" class="btn">
                                <i class="fas fa-save"></i> Salvar JSON Criptografado
                            </button>
                        </form>

                        <!-- Formulário para HTML -->
                        <form method="POST" action="salvar_resultado.php" target="_blank">
                            <input type="hidden" name="matriz_resultado" value='<?php echo json_encode($matrizInversa); ?>'>
                            <input type="hidden" name="formato" value="html">
                            <button type="submit" class="btn">
                                <i class="fas fa-file-alt"></i> Salvar HTML Criptografado
                            </button>
                        </form>

                        <!-- Opção de novo cálculo -->
                        <a href="?novo=1" class="btn">
                            Novo Cálculo
                        </a>
                    </div>
                </section>
            <?php endif; ?>
        </main>

        <!-- Rodapé da Operação -->
        <footer class="operation-footer">
            <div>
                <p>Matriz Inversa - Projeto Interdisciplinar 2025/2026</p>
            </div>
        </footer>
    </div>

    <script>
        // Função para preencher valores aleatórios
        function preencherAleatorio() {
            // Pegar todos os inputs da matriz
            const inputs = document.querySelectorAll('.matrix-input');

            // Preencher cada um com valores aleatórios
            inputs.forEach(input => {
                // Valor entre -10 e 10
                const valor = Math.floor(Math.random() * 21) - 10;
                input.value = valor;
            });

            alert('Matriz preenchida com valores aleatórios entre -10 e 10!');
        }


        // Focar no primeiro campo ao carregar
        document.addEventListener('DOMContentLoaded', function () {
            const primeiroCampo = document.querySelector('.matrix-input');
            if (primeiroCampo) {
                primeiroCampo.focus();
            }
        });
    </script>
</body>

</html>