<?php
session_start();
include_once '../includes/functions.php';

$erro = '';
$mensagemOriginal = '';
$mensagemCriptografada = '';
$matrizCodificadora = [[1, 2], [3, 5]];
$matrizMensagem = [];
$matrizResultado = [];
$dimensaoCodificadora = 2;
$tabela = getTabelaCriptografia();

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['configurar'])) {
        $dimensaoCodificadora = intval($_POST['dimensao'] ?? 2);

        if ($dimensaoCodificadora < 2 || $dimensaoCodificadora > 3) {
            $erro = "A dimensão da matriz codificadora deve ser 2 ou 3.";
        }

        $_SESSION['dimensao'] = $dimensaoCodificadora;

        if ($dimensaoCodificadora == 2) {
            $matrizCodificadora = [[1, 2], [3, 5]];
        } else {
            $matrizCodificadora = [[1, 2, 1], [2, 3, 2], [1, 2, 2]];
        }

        for ($i = 0; $i < $dimensaoCodificadora; $i++) {
            for ($j = 0; $j < $dimensaoCodificadora; $j++) {
                $nome = "codificadora_{$i}_{$j}";
                if (isset($_POST[$nome])) {
                    $matrizCodificadora[$i][$j] = intval($_POST[$nome]);
                }
            }
        }
    } elseif (isset($_POST['calcular'])) {
        $dimensaoCodificadora = $_SESSION['dimensao'] ?? 2;
        $mensagemOriginal = trim($_POST['mensagem'] ?? '');

        if (empty($mensagemOriginal)) {
            $erro = "Por favor, digite uma mensagem para criptografar.";
        } else {
            $matrizCodificadora = [];
            for ($i = 0; $i < $dimensaoCodificadora; $i++) {
                $matrizCodificadora[$i] = [];
                for ($j = 0; $j < $dimensaoCodificadora; $j++) {
                    $nome = "codificadora_{$i}_{$j}";
                    $matrizCodificadora[$i][$j] = isset($_POST[$nome]) ? intval($_POST[$nome]) : 0;
                }
            }

            $det = calcularDeterminante($matrizCodificadora);
            if ($det == 0) {
                $erro = "A matriz codificadora não é inversível (determinante = 0).";
            } else {
                $mensagemUpper = strtoupper($mensagemOriginal);
                $numeros = [];
                for ($i = 0; $i < strlen($mensagemUpper); $i++) {
                    $char = $mensagemUpper[$i];
                    $numeros[] = isset($tabela[$char]) ? $tabela[$char] : 29;
                }

                if (count($numeros) % $dimensaoCodificadora != 0) {
                    $numeros[] = 29;
                }

                $matrizMensagem = [];
                $numColunas = ceil(count($numeros) / $dimensaoCodificadora);

                for ($i = 0; $i < $dimensaoCodificadora; $i++) {
                    $matrizMensagem[$i] = [];
                    for ($j = 0; $j < $numColunas; $j++) {
                        $index = $j * $dimensaoCodificadora + $i;
                        $matrizMensagem[$i][$j] = isset($numeros[$index]) ? $numeros[$index] : 29;
                    }
                }

                $matrizResultado = multiplicarMatrizes($matrizCodificadora, $matrizMensagem);

                if ($matrizResultado !== false) {
                    $mensagemCriptografada = matrizParaTexto($matrizResultado);
                } else {
                    $erro = "Erro ao criptografar a mensagem.";
                }
            }
        }
    }
} else {
    $dimensaoCodificadora = $_SESSION['dimensao'] ?? 2;
    if ($dimensaoCodificadora == 3) {
        $matrizCodificadora = [[1, 2, 1], [2, 3, 2], [1, 2, 2]];
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criptografia com Matrizes</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/operations.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
    <div class="container">
        <header class="operation-header">
            <div class="header-top">
                <div class="operation-title">
                    <h1>Criptografia com Matrizes</h1>
                </div>
                <a href="../index.php" class="back-btn">Voltar ao Menu</a>
            </div>

            <div class="operation-instructions">
                <p><strong>Método:</strong> Converta letras em números, multiplique pela matriz codificadora para
                    criptografar.</p>
            </div>
        </header>

        <main class="operation-main">
            <section class="config-section">
                <div class="section-header">
                    <h2>Configuração</h2>
                    <p>Configure a matriz codificadora</p>
                </div>

                <?php if ($erro): ?>
                    <div class="error-message"><?php echo $erro; ?></div>
                <?php endif; ?>

                <form method="POST" class="config-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dimensao">Dimensão da Matriz Codificadora</label>
                            <select name="dimensao" id="dimensao" class="form-select">
                                <option value="2" <?php echo $dimensaoCodificadora == 2 ? 'selected' : ''; ?>>2×2</option>
                                <option value="3" <?php echo $dimensaoCodificadora == 3 ? 'selected' : ''; ?>>3×3</option>
                            </select>
                        </div>
                    </div>

                    <div class="matrix-box">
                        <div class="matrix-header">
                            <h3>Matriz Codificadora</h3>
                            <p>Defina os valores da matriz</p>
                        </div>
                        <div class="matrix-grid">
                            <?php for ($i = 0; $i < $dimensaoCodificadora; $i++): ?>
                                <div class="matrix-row">
                                    <?php for ($j = 0; $j < $dimensaoCodificadora; $j++):
                                        $nome = "codificadora_{$i}_{$j}";
                                        $valor = isset($_POST[$nome]) ? $_POST[$nome] : $matrizCodificadora[$i][$j];
                                        ?>
                                        <div class="matrix-cell">
                                            <input type="number" step="1" name="<?php echo $nome; ?>"
                                                value="<?php echo $valor; ?>" placeholder="0" class="matrix-input" required>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <?php
                        $det = calcularDeterminante($matrizCodificadora);
                        $inversivel = ($det != 0);
                        ?>
                        <div class="matrix-info">
                            <p>Determinante: <?php echo $det; ?>
                                (<?php echo $inversivel ? 'Matriz inversível' : 'Matriz não inversível'; ?>)</p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="configurar" class="btn">Configurar Matriz Codificadora</button>
                        <button type="button" onclick="preencherAleatorio()" class="btn">Preencher
                            Aleatoriamente</button>
                    </div>
                </form>
            </section>

            <section class="tabela-section">
                <div class="section-header">
                    <h2>Tabela de Conversão</h2>
                    <p>Tabela para converter letras em números (e vice-versa)</p>
                </div>

                <div class="tabela-container">
                    <div class="tabela-box">
                        <h3>Letra → Número</h3>
                        <div class="tabela-grid">
                            <?php
                            $tabela = getTabelaCriptografia();
                            $count = 0;
                            foreach ($tabela as $letra => $numero):
                                if ($count % 10 == 0)
                                    echo '<div class="tabela-row">';
                                ?>
                                <div class="tabela-item">
                                    <span class="tabela-letra"><?php echo htmlspecialchars($letra); ?></span>
                                    <span class="tabela-seta">→</span>
                                    <span class="tabela-numero"><?php echo $numero; ?></span>
                                </div>
                                <?php
                                $count++;
                                if ($count % 10 == 0)
                                    echo '</div>';
                            endforeach;
                            if ($count % 10 != 0)
                                echo '</div>';
                            ?>
                        </div>
                        <div class="tabela-nota">
                            <p><strong>Espaço</strong> (ou '_') = 29 | <strong>.</strong> = 27 | <strong>,</strong> = 28
                                | <strong>¬</strong> = 30 (parágrafo)</p>
                        </div>
                    </div>
                </div>
            </section>

            <?php if (isset($_POST['configurar']) || isset($_SESSION['dimensao'])): ?>
                <section class="input-section">
                    <div class="section-header">
                        <h2>Entrada de Dados</h2>
                        <p>Digite a mensagem que deseja criptografar</p>
                    </div>

                    <form method="POST" class="matrix-input-form">
                        <input type="hidden" name="dimensao" value="<?php echo $dimensaoCodificadora; ?>">
                        <?php for ($i = 0; $i < $dimensaoCodificadora; $i++): ?>
                            <?php for ($j = 0; $j < $dimensaoCodificadora; $j++):
                                $nome = "codificadora_{$i}_{$j}";
                                $valor = isset($_POST[$nome]) ? $_POST[$nome] : $matrizCodificadora[$i][$j];
                                ?>
                                <input type="hidden" name="<?php echo $nome; ?>" value="<?php echo $valor; ?>">
                            <?php endfor; ?>
                        <?php endfor; ?>

                        <div class="matrices-container">
                            <div class="matrix-box">
                                <div class="matrix-header">
                                    <h3>Mensagem para Criptografar</h3>
                                </div>
                                <div class="mensagem-input">
                                    <textarea name="mensagem" id="mensagem" rows="4"
                                        placeholder="Digite sua mensagem aqui (apenas letras maiúsculas, pontos, vírgulas e espaços)..."><?php echo htmlspecialchars($mensagemOriginal); ?></textarea>
                                    <div class="exemplo-mensagem">
                                        <p><strong>Exemplo:</strong> "OS NUMEROS GOVERNAM O MUNDO."</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="calcular" class="btn">Criptografar Mensagem</button>
                            <button type="button" onclick="carregarExemplo()" class="btn">Carregar Exemplo</button>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <?php if (!empty($matrizResultado)): ?>
                <section class="result-section">
                    <div class="section-header">
                        <h2>Resultado da Criptografia</h2>
                        <p>Mensagem criptografada usando matrizes</p>
                    </div>

                    <div class="result-container">
                        <div class="original-matrices">
                            <div class="result-box">
                                <h3>Mensagem Original</h3>
                                <div class="message-box">
                                    <p><?php echo htmlspecialchars($mensagemOriginal); ?></p>
                                    <div class="message-info">
                                        <p><?php echo strlen($mensagemOriginal); ?> caracteres</p>
                                    </div>
                                </div>
                            </div>

                            <div class="operator-icon">
                                →
                            </div>

                            <div class="result-box highlight">
                                <h3>Conversão para Números</h3>
                                <div class="conversao-texto">
                                    <?php
                                    $mensagemUpper = strtoupper($mensagemOriginal);
                                    $numeros = [];
                                    for ($i = 0; $i < strlen($mensagemUpper); $i++) {
                                        $char = $mensagemUpper[$i];
                                        $numeros[] = isset($tabela[$char]) ? $tabela[$char] : 29;
                                    }

                                    if (count($numeros) % $dimensaoCodificadora != 0) {
                                        $numeros[] = 29;
                                    }
                                    ?>
                                    <p><strong>Texto:</strong> <?php echo implode(' ', str_split($mensagemUpper)); ?></p>
                                    <p><strong>Números:</strong>
                                        <?php
                                        foreach ($numeros as $index => $numero) {
                                            echo sprintf('%2d', $numero) . ' ';
                                        }
                                        ?>
                                    </p>
                                    <?php if (strlen($mensagemUpper) % $dimensaoCodificadora != 0): ?>
                                        <p class="info-adicao">Foi adicionado um espaço (29) no final</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="original-matrices">
                            <div class="result-box">
                                <h3>Matriz Codificadora C</h3>
                                <?php echo exibirMatrizHTML($matrizCodificadora); ?>
                            </div>

                            <div class="operator-icon">
                                ×
                            </div>

                            <div class="result-box">
                                <h3>Matriz Mensagem M</h3>
                                <?php echo exibirMatrizHTML($matrizMensagem); ?>
                            </div>

                            <div class="operator-icon">
                                =
                            </div>

                            <div class="result-box highlight">
                                <h3>Matriz Criptografada E</h3>
                                <?php echo exibirMatrizHTML($matrizResultado); ?>
                            </div>
                        </div>
                    </div>

                    <div class="result-actions">
                        <form method="POST" action="salvar_criptografia.php" target="_blank">
                            <input type="hidden" name="matriz_resultado"
                                value='<?php echo json_encode($matrizResultado); ?>'>
                            <input type="hidden" name="formato" value="json">
                            <button type="submit" class="btn">
                                <i class="fas fa-save"></i> Salvar JSON Criptografado
                            </button>
                        </form>

                        <!-- Formulário para HTML -->
                        <form method="POST" action="salvar_criptografia.php" target="_blank">
                            <input type="hidden" name="matriz_resultado"
                                value='<?php echo json_encode($matrizResultado); ?>'>
                            <input type="hidden" name="formato" value="html">
                            <button type="submit" class="btn">
                                <i class="fas fa-file-alt"></i> Salvar HTML Criptografado
                            </button>
                        </form>

                        <a href="?novo=1" class="btn">
                            Nova Criptografia
                        </a>

                    </div>
                </section>
            <?php endif; ?>
        </main>

        <footer class="operation-footer">
            <div>
                <p>Criptografia com Matrizes - Projeto Interdisciplinar 2025/2026</p>
            </div>
        </footer>
    </div>

    <script>
        function carregarExemplo() {
            document.getElementById('mensagem').value = 'OS NUMEROS GOVERNAM O MUNDO.';
            alert('Exemplo carregado! Clique em "Criptografar Mensagem".');
        }

        function preencherAleatorio() {
            const inputs = document.querySelectorAll('.matrix-input');
            inputs.forEach(input => {
                const valor = Math.floor(Math.random() * 10) + 1;
                input.value = valor;
            });
            alert('Matriz preenchida com valores aleatórios!');
        }

        document.getElementById('mensagem')?.addEventListener('input', function (e) {
            this.value = this.value.toUpperCase();
        });

        document.addEventListener('DOMContentLoaded', function () {
            const primeiroCampo = document.querySelector('.matrix-input');
            if (primeiroCampo) {
                primeiroCampo.focus();
            }
        });
    </script>

    <style>
        .tabela-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .tabela-container {
            margin-top: 20px;
        }

        .tabela-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #9c27b0;
        }

        .tabela-grid {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .tabela-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 5px;
        }

        .tabela-item {
            display: flex;
            align-items: center;
            gap: 5px;
            background: white;
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
            min-width: 60px;
        }

        .tabela-letra {
            font-weight: bold;
            color: #7b1fa2;
            width: 20px;
            text-align: center;
        }

        .tabela-seta {
            color: #9e9e9e;
        }

        .tabela-numero {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #2c3e50;
            width: 20px;
            text-align: center;
        }

        .tabela-nota {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 0.9rem;
        }

        textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
        }

        .exemplo-mensagem {
            background-color: #e3f2fd;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #2196f3;
        }

        .conversao-texto {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .info-adicao {
            color: #ff9800;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        .message-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 10px 0;
        }

        .message-box.encrypted {
            border-left: 4px solid #e74c3c;
        }

        .message-info {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
    </style>
</body>

</html>