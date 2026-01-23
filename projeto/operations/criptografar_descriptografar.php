<?php
session_start();
include_once '../includes/functions.php';

// MATRIZ FIXA 2x2 - Não pode ser alterada
$matrizCodificadora = [[1, 2], [3, 5]];
$matrizDescodificadora = [[5, -2], [-3, 1]];
$dimensaoCodificadora = 2;
$tabela = getTabelaCriptografia();

$resultado = '';
$erro = '';
$mensagemOriginal = '';
$mensagemCriptografada = '';
$matrizMensagem = [];
$matrizResultado = [];
$matrizDescriptografada = [];

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['calcular'])) {
        $mensagemOriginal = trim($_POST['mensagem'] ?? '');

        if (empty($mensagemOriginal)) {
            $erro = "Por favor, digite uma mensagem para criptografar.";
        } else {
            // Converter mensagem para matriz numérica
            $matrizMensagem = textoParaMatriz($mensagemOriginal, $dimensaoCodificadora);

            // Criptografar usando a matriz fixa
            $matrizResultado = criptografarMensagem($mensagemOriginal, $matrizCodificadora);
            $matrizDescriptografada = descriptografarMensagem($matrizResultado, $matrizDescodificadora);
            if ($matrizResultado !== false) {
                // Converter matriz criptografada para string
                $mensagemCriptografada = matrizParaTexto($matrizResultado);
            } else {
                $erro = "Erro ao criptografar a mensagem.";
            }

        }
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
        <!-- Cabeçalho -->
        <header class="operation-header">
            <div class="header-top">
                <div class="operation-title">
                    <h1>Criptografar com Matriz Fixa</h1>
                </div>
                <a href="../index.php" class="back-btn">
                    Voltar ao Menu
                </a>
            </div>
        </header>

        <!-- Conteúdo Principal -->
        <main class="operation-main">
            <?php if ($erro): ?>
                <div class="error-message">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>

            <!-- Matriz Fixa -->
            <section class="config-section">
                <div class="section-header">
                    <h2> Matriz Codificadora </h2>
                </div>

                <div class="matriz-fixa-display">
                    <div class="matrix-grid-small">
                        <div class="matrix-row">
                            <div class="matrix-cell-fixed">1</div>
                            <div class="matrix-cell-fixed">2</div>
                        </div>
                        <div class="matrix-row">
                            <div class="matrix-cell-fixed">3</div>
                            <div class="matrix-cell-fixed">5</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tabela de Conversão -->
            <section class="tabela-section">
                <div class="section-header">
                    <h2> Tabela de Conversão</h2>
                    <p>Tabela para converter letras em números</p>
                </div>

                <div class="tabela-container">
                    <div class="tabela-box">
                        <h3> Letra → Número</h3>
                        <div class="tabela-grid">
                            <?php
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
                    </div>
                </div>
            </section>

            <!-- Entrada de Dados -->
            <section class="input-section">
                <div class="section-header">
                    <h2> Criptografar Mensagem</h2>
                </div>

                <form method="POST" class="mensagem-form">
                    <div class="mensagem-container">
                        <div class="form-group">
                            <textarea name="mensagem" id="mensagem" rows="4" class="form-textarea"
                                placeholder="Digite sua mensagem aqui (apenas letras maiúsculas, pontos, vírgulas e espaços)..."
                                oninput="this.value = this.value.toUpperCase()"><?php echo htmlspecialchars($mensagemOriginal); ?></textarea>
                            <!-- Tornar minusculas em maiúsculas -->
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="calcular" class="btn btn-success btn-large">
                                Criptografar e Descriptografar Mensagem
                            </button>
                            <button type="button" onclick="carregarExemplo()" class="btn btn-secondary">
                                Carregar Exemplo
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            <!-- Resultados -->
            <?php if (!empty($mensagemCriptografada) && !empty($matrizResultado)): ?>
                <section class="result-section">
                    <div class="section-header">
                        <h2> Resultado:</h2>
                    </div>

                    <div class="result-container">
                        <div class="resultado-simples">
                            <!-- Passo 1: Conversão para Matriz Numérica -->
                            <div class="passo-box">
                                <h3> Passo 1: Conversão para Matriz</h3>
                                <div class="passo-content">
                                    <?php
                                    // Converter texto para números
                                    $mensagemUpper = strtoupper($mensagemOriginal);
                                    $numeros = [];
                                    for ($i = 0; $i < strlen($mensagemUpper); $i++) {
                                        $char = $mensagemUpper[$i];
                                        $numeros[] = $tabela[$char] ?? 29; // 29 = espaço se não encontrado
                                    }

                                    // Adicionar espaços se necessário para múltiplo de 2
                                    while (count($numeros) % 2 != 0) {
                                        $numeros[] = 29;
                                    }
                                    ?>

                                    <div class="conversao-texto">
                                        <p><strong>Texto:</strong> <?php echo implode(' ', str_split($mensagemUpper)); ?>
                                        </p>
                                        <p><strong>Números:</strong>
                                            <?php foreach ($numeros as $index => $num): ?>
                                                <span class="numero-item"><?php echo sprintf('%2d', $num); ?></span>
                                            <?php endforeach; ?>
                                        </p>
                                    </div>

                                    <div class="matriz-numerica">
                                        <h4> Matriz da Mensagem :</h4>
                                        <div class="matrix-grid">
                                            <?php for ($i = 0; $i < 2; $i++): ?>
                                                <div class="matrix-row">
                                                    <?php for ($j = 0; $j < count($numeros) / 2; $j++): ?>
                                                        <div class="matrix-cell">
                                                            <?php echo $matrizMensagem[$i][$j] ?? 0; ?>
                                                        </div>
                                                    <?php endfor; ?>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Passo 2: Multiplicação das Matrizes -->
                            <div class="passo-box">
                                <h3> Passo 2: Mensagem Criptografada Final</h3>
                                <div class="passo-content">
                                    <div class="multiplicacao-box">
                                        <div class="matrizes-operacao">
                                            <div class="matriz-item">
                                                <h4> Matriz Codificadora</h4>
                                                <div class="matrix-grid">
                                                    <div class="matrix-row">
                                                        <div class="matrix-cell">1</div>
                                                        <div class="matrix-cell">2</div>
                                                    </div>
                                                    <div class="matrix-row">
                                                        <div class="matrix-cell">3</div>
                                                        <div class="matrix-cell">5</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="operador">
                                                <span>×</span>
                                            </div>

                                            <div class="matriz-item">
                                                <h4> Matriz Mensagem</h4>
                                                <div class="matrix-grid">
                                                    <?php for ($i = 0; $i < 2; $i++): ?>
                                                        <div class="matrix-row">
                                                            <?php for ($j = 0; $j < count($numeros) / 2; $j++): ?>
                                                                <div class="matrix-cell">
                                                                    <?php echo $matrizMensagem[$i][$j] ?? 0; ?>
                                                                </div>
                                                            <?php endfor; ?>
                                                        </div>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>

                                            <div class="operador">
                                                <span>=</span>
                                            </div>

                                            <div class="matriz-item">
                                                <h4> Matriz Criptografada</h4>
                                                <div class="matrix-grid">
                                                    <?php if (!empty($matrizResultado)): ?>
                                                        <?php for ($i = 0; $i < 2; $i++): ?>
                                                            <div class="matrix-row">
                                                                <?php for ($j = 0; $j < count($numeros) / 2; $j++): ?>
                                                                    <div class="matrix-cell result">
                                                                        <?php echo $matrizResultado[$i][$j] ?? 0; ?>
                                                                    </div>
                                                                <?php endfor; ?>
                                                            </div>
                                                        <?php endfor; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Passo 3: Descriptografia - Multiplicação pela Matriz Inversa -->
                            <div class="passo-box">
                                <h3> Passo 3: Descriptografia (C⁻¹ × E = M)</h3>
                                <div class="passo-content">
                                    <?php
                                    // Verificar se temos os dados necessários
                                    if (!empty($matrizDescriptografada)) {
                                        $nColunas = count($numeros) / 2;
                                        ?>

                                        <div class="multiplicacao-box">
                                            <div class="matrizes-operacao">
                                                <!-- Matriz Inversa C⁻¹ -->
                                                <div class="matriz-item">
                                                    <h4> Matriz Inversa</h4>
                                                    <div class="matrix-grid">
                                                        <div class="matrix-row">
                                                            <div class="matrix-cell inversa">
                                                                <?php echo $matrizDescodificadora[0][0]; ?>
                                                            </div>
                                                            <div class="matrix-cell inversa">
                                                                <?php echo $matrizDescodificadora[0][1]; ?>
                                                            </div>
                                                        </div>
                                                        <div class="matrix-row">
                                                            <div class="matrix-cell inversa">
                                                                <?php echo $matrizDescodificadora[1][0]; ?>
                                                            </div>
                                                            <div class="matrix-cell inversa">
                                                                <?php echo $matrizDescodificadora[1][1]; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="operador">
                                                    <span>×</span>
                                                </div>

                                                <!-- Matriz Criptografada E -->
                                                <div class="matriz-item">
                                                    <h4> Matriz Criptografada</h4>
                                                    <div class="matrix-grid">
                                                        <?php for ($i = 0; $i < 2; $i++): ?>
                                                            <div class="matrix-row">
                                                                <?php for ($j = 0; $j < $nColunas; $j++): ?>
                                                                    <div class="matrix-cell criptografada">
                                                                        <?php echo $matrizResultado[$i][$j]; ?>
                                                                    </div>
                                                                <?php endfor; ?>
                                                            </div>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>

                                                <div class="operador">
                                                    <span>=</span>
                                                </div>

                                                <!-- Matriz Descriptografada M -->
                                                <div class="matriz-item">
                                                    <h4> Matriz Descriptografada</h4>
                                                    <div class="matrix-grid">
                                                        <?php for ($i = 0; $i < 2; $i++): ?>
                                                            <div class="matrix-row">
                                                                <?php for ($j = 0; $j < $nColunas; $j++): ?>
                                                                    <div class="matrix-cell descriptografada">
                                                                        <?php echo $matrizDescriptografada[$i][$j] * -1; ?>
                                                                    </div>
                                                                <?php endfor; ?>
                                                            </div>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="resultado-numeros">
                                                <h4>Números Descriptografados:</h4>
                                                <div class="numeros-container">
                                                    <?php
                                                    // Extrair números da matriz descriptografada LINHA POR LINHA
                                                    $numerosDescriptografados = [];

                                                    // Primeiro toda a linha 0
                                                    for ($j = 0; $j < $nColunas; $j++) {
                                                        $numerosDescriptografados[] = $matrizDescriptografada[0][$j] * -1;
                                                    }
                                                    // Depois toda a linha 1
                                                    for ($j = 0; $j < $nColunas; $j++) {
                                                        $numerosDescriptografados[] = $matrizDescriptografada[1][$j] * -1;
                                                    }

                                                    foreach ($numerosDescriptografados as $num):
                                                        ?>
                                                        <span class="numero-descriptografado">
                                                            <?php echo $num; ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="resultado-texto">
                                                <h4>Texto Descriptografado:</h4>
                                                <div class="texto-descriptografado">
                                                    <?php
                                                    // Converter números de volta para texto
                                                    $textoDescriptografado = '';
                                                    foreach ($numerosDescriptografados as $num) {
                                                        // Ajustar números >26 ou <1 para o intervalo 1-26
                                                        while ($num > 26) {
                                                            $num -= 26;
                                                        }
                                                        while ($num < 1) {
                                                            $num += 26;
                                                        }

                                                        // Encontrar letra correspondente ao número
                                                        $letra = array_search($num, $tabela);
                                                        $textoDescriptografado .= ($letra !== false) ? $letra : ' ';
                                                    }
                                                    echo htmlspecialchars($textoDescriptografado);
                                                    ?>
                                                </div>
                                            </div>

                                        <?php } else { ?>
                                            <div class="erro-calc">
                                                <p>Não foi possível calcular a matriz descriptografada.</p>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                </section>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function carregarExemplo() {
            document.getElementById('mensagem').value = 'OS NUMEROS GOVERNAM O MUNDO.';
        }
    </script>
</body>

</html>