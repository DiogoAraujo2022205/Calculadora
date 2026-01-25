<?php
session_start();
include_once '../includes/functions.php';

$erro = '';
$mensagemDescriptografada = '';
$matrizDescriptografadaInput = '';
$matrizDescriptografadora = [[-5, 2], [3, -1]]; // Inversa CORRETA de [[1,2],[3,5]]
$matrizCriptografada = [];
$matrizResultado = [];
$dimensaoCodificadora = 2;
$tabela = getTabelaCriptografia();
$eMensagem = false; // Flag para identificar se é uma mensagem

// Processar upload de arquivos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['configurar'])) {
        $dimensaoCodificadora = intval($_POST['dimensao'] ?? 2);

        if ($dimensaoCodificadora < 2 || $dimensaoCodificadora > 3) {
            $erro = "A dimensão da matriz descodificadora deve ser 2 ou 3.";
        }

        $_SESSION['dimensao'] = $dimensaoCodificadora;

        // Definir matrizes descodificadoras padrão (inversas das codificadoras)
        if ($dimensaoCodificadora == 2) {
            $matrizDescriptografadora = [[-5, 2], [3, -1]]; // Inversa CORRETA de [[1,2],[3,5]]
        } else {
            // Inversa de [[1,2,1],[2,3,2],[1,2,2]]
            $matrizDescriptografadora = [[2, -2, 1], [-2, 1, 0], [1, 0, -1]];
        }

        for ($i = 0; $i < $dimensaoCodificadora; $i++) {
            for ($j = 0; $j < $dimensaoCodificadora; $j++) {
                $nome = "descodificadora_{$i}_{$j}";
                if (isset($_POST[$nome])) {
                    $matrizDescriptografadora[$i][$j] = intval($_POST[$nome]);
                }
            }
        }
    } elseif (isset($_POST['descriptografar'])) {
        $dimensaoCodificadora = $_SESSION['dimensao'] ?? 2;

        // Obter matriz descodificadora
        $matrizDescriptografadora = [];
        for ($i = 0; $i < $dimensaoCodificadora; $i++) {
            $matrizDescriptografadora[$i] = [];
            for ($j = 0; $j < $dimensaoCodificadora; $j++) {
                $nome = "descodificadora_{$i}_{$j}";
                $matrizDescriptografadora[$i][$j] = isset($_POST[$nome]) ? intval($_POST[$nome]) : 0;
            }
        }

        // Verificar se a matriz é inversível
        $det = calcularDeterminante($matrizDescriptografadora);
        if ($det == 0) {
            $erro = "A matriz descodificadora não é inversível (determinante = 0).";
        } else {
            // Processar entrada da matriz criptografada
            $matrizDescriptografadaInput = trim($_POST['mensagem_criptografada'] ?? '');

            if (empty($matrizDescriptografadaInput)) {
                $erro = "Por favor, forneça uma matriz criptografada.";
            } else {
                // Tentar interpretar a entrada como diferentes formatos
                $matrizCriptografada = interpretarEntradaMatriz($matrizDescriptografadaInput, $eMensagem);

                if ($matrizCriptografada === false) {
                    $erro = "Formato de matriz inválido. Use JSON, HTML ou formato textual.";
                } else {
                    // Verificar dimensões da matriz criptografada
                    $numLinhas = count($matrizCriptografada);
                    if ($numLinhas != $dimensaoCodificadora) {
                        $erro = "A matriz criptografada deve ter $dimensaoCodificadora linhas (tem $numLinhas).";
                    } else {
                        // Descriptografar multiplicando pela matriz descodificadora
                        $matrizResultado = multiplicarMatrizes($matrizDescriptografadora, $matrizCriptografada);

                        if ($matrizResultado !== false) {
                            if ($eMensagem) {
                                // É uma mensagem: converter números em letras
                                $numColunas = count($matrizResultado[0]);
                                $numeros = [];

                                for ($j = 0; $j < $numColunas; $j++) {
                                    for ($i = 0; $i < $dimensaoCodificadora; $i++) {
                                        if (isset($matrizResultado[$i][$j])) {
                                            $valor = $matrizResultado[$i][$j];

                                            // Arredondar
                                            $valorArredondado = round($valor);

                                            // Ajustar para o intervalo 1-30
                                            if ($valorArredondado < 1)
                                                $valorArredondado = 1;
                                            if ($valorArredondado > 30)
                                                $valorArredondado = 30;

                                            $numeros[] = $valorArredondado;
                                        }
                                    }
                                }

                                // Converter números em texto
                                $tabelaInvertida = array_flip($tabela);
                                $texto = '';
                                foreach ($numeros as $numero) {
                                    if (isset($tabelaInvertida[$numero])) {
                                        $texto .= $tabelaInvertida[$numero];
                                    } else {
                                        $texto .= '?';
                                    }
                                }

                                $mensagemDescriptografada = $texto;
                            }
                            // Se não for mensagem, apenas mostra a matriz resultante
                        } else {
                            $erro = "Erro ao descriptografar.";
                        }
                    }
                }
            }
        }
    } elseif (isset($_FILES['arquivo_upload']) && $_FILES['arquivo_upload']['error'] == 0) {
        // Processar upload de arquivo
        $conteudoArquivo = file_get_contents($_FILES['arquivo_upload']['tmp_name']);
        $matrizCriptografada = interpretarEntradaMatriz($conteudoArquivo, $eMensagem);

        if ($matrizCriptografada !== false) {
            $matrizDescriptografadaInput = json_encode($matrizCriptografada);
        } else {
            $erro = "Arquivo inválido. O arquivo deve conter uma matriz no formato JSON ou HTML.";
        }
    }
} else {
    $dimensaoCodificadora = $_SESSION['dimensao'] ?? 2;
    if ($dimensaoCodificadora == 3) {
        $matrizDescriptografadora = [[2, -2, 1], [-2, 1, 0], [1, 0, -1]];
    }
}

/**
 * Interpreta diferentes formatos de entrada para matriz
 * Define $eMensagem = true se for uma mensagem criptografada
 */
function interpretarEntradaMatriz($input, &$eMensagem = false)
{
    $input = trim($input);
    $eMensagem = false;

    // Verificar se é uma mensagem criptografada:
    // 1. JSON com chave "mensagem_criptografada"
    if (strpos($input, '{') === 0) {
        $dados = json_decode($input, true);
        if ($dados !== null && isset($dados['mensagem_criptografada'])) {
            $eMensagem = true;
            return $dados['mensagem_criptografada'];
        }
    }

    // 2. HTML com título "Mensagem Criptografada" ou h2 com esse texto
    if (stripos($input, 'Mensagem Criptografada') !== false) {
        $eMensagem = true;
    }

    // 3. Tentar como JSON puro (matriz simples)
    if (preg_match('/^\[\[.*\]\]$/', $input)) {
        $matriz = json_decode($input, true);
        if ($matriz !== null && is_array($matriz) && is_array($matriz[0])) {
            // Se for matriz simples, não é mensagem
            $eMensagem = false;
            return $matriz;
        }
    }

    // 4. Tentar como HTML simples (sem menção a mensagem)
    if (strpos($input, '<!DOCTYPE html') !== false || strpos($input, '<table') !== false) {
        return extrairMatrizDeHTML($input);
    }

    // 5. Tentar como JSON genérico
    if (strpos($input, '{') === 0) {
        $dados = json_decode($input, true);
        if ($dados !== null && isset($dados[0]) && is_array($dados[0])) {
            return $dados;
        }
    }

    return false;
}

/**
 * Extrai matriz de tabela HTML
 */
function extrairMatrizDeHTML($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $tables = $dom->getElementsByTagName('table');
    if ($tables->length == 0) {
        return false;
    }

    $matriz = [];
    $table = $tables->item(0);
    $rows = $table->getElementsByTagName('tr');

    foreach ($rows as $rowIndex => $row) {
        $matriz[$rowIndex] = [];
        $cells = $row->getElementsByTagName('td');

        foreach ($cells as $cellIndex => $cell) {
            $valor = trim($cell->nodeValue);
            if (is_numeric($valor)) {
                $matriz[$rowIndex][$cellIndex] = intval($valor);
            }
        }
    }

    return $matriz;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descriptografia com Matrizes</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/operations.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <header class="operation-header">
            <div class="header-top">
                <div class="operation-title">
                    <h1>Descriptografia com Matrizes</h1>
                </div>
                <a href="../index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>Voltar ao Menu</a>
            </div>

            <div class="operation-instructions">
                <p><strong>Método:</strong> Use a matriz descodificadora (inversa da codificadora) para descriptografar.
                </p>
            </div>
        </header>

        <main class="operation-main">
            <section class="config-section">
                <div class="section-header">
                    <h2>Configuração</h2>
                    <p>Configure a matriz descodificadora</p>
                </div>

                <?php if ($erro): ?>
                    <div class="error-message"><?php echo $erro; ?></div>
                <?php endif; ?>

                <form method="POST" class="config-form" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dimensao">Dimensão da Matriz Descodificadora</label>
                            <select name="dimensao" id="dimensao" class="form-select">
                                <option value="2" <?php echo $dimensaoCodificadora == 2 ? 'selected' : ''; ?>>2×2</option>
                                <option value="3" <?php echo $dimensaoCodificadora == 3 ? 'selected' : ''; ?>>3×3</option>
                            </select>
                        </div>
                    </div>

                    <div class="matrix-box">
                        <div class="matrix-header">
                            <h3>Matriz Descodificadora</h3>
                            <p>Defina os valores da matriz (deve ser a inversa da matriz codificadora)</p>
                        </div>
                        <div class="matrix-grid">
                            <?php for ($i = 0; $i < $dimensaoCodificadora; $i++): ?>
                                <div class="matrix-row">
                                    <?php for ($j = 0; $j < $dimensaoCodificadora; $j++):
                                        $nome = "descodificadora_{$i}_{$j}";
                                        $valor = isset($_POST[$nome]) ? $_POST[$nome] : $matrizDescriptografadora[$i][$j];
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
                        $det = calcularDeterminante($matrizDescriptografadora);
                        $inversivel = ($det != 0);
                        ?>
                        <div class="matrix-info">
                            <p>Determinante: <?php echo $det; ?>
                                (<?php echo $inversivel ? 'Matriz inversível' : 'Matriz não inversível'; ?>)</p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="configurar" class="btn"> <i class="fas fa-check"></i> Configurar Matriz Descodificadora</button>
                        <button type="button" onclick="preencherInversa()" class="btn"><i class="fas fa-dice"></i>Preencher com Inversa
                            Padrão</button>
                    </div>
                </form>
            </section>

            <?php if (isset($_POST['configurar']) || isset($_SESSION['dimensao'])): ?>
                <section class="input-section">
                    <div class="section-header">
                        <h2>Entrada de Dados</h2>
                        <p>Insira a matriz criptografada ou carregue um arquivo</p>
                    </div>

                    <form method="POST" class="matrix-input-form" enctype="multipart/form-data">
                        <input type="hidden" name="dimensao" value="<?php echo $dimensaoCodificadora; ?>">
                        <?php for ($i = 0; $i < $dimensaoCodificadora; $i++): ?>
                            <?php for ($j = 0; $j < $dimensaoCodificadora; $j++):
                                $nome = "descodificadora_{$i}_{$j}";
                                $valor = isset($_POST[$nome]) ? $_POST[$nome] : $matrizDescriptografadora[$i][$j];
                                ?>
                                <input type="hidden" name="<?php echo $nome; ?>" value="<?php echo $valor; ?>">
                            <?php endfor; ?>
                        <?php endfor; ?>

                        <div class="matrices-container">
                            <div class="matrix-box">
                                <div class="matrix-header">
                                    <h3>Matriz Criptografada</h3>
                                    <p>Insira no formato JSON, HTML ou texto</p>
                                </div>

                                <div class="upload-section">
                                    <div class="form-group">
                                        <label for="arquivo_upload">Carregar arquivo:</label>
                                        <input type="file" name="arquivo_upload" id="arquivo_upload"
                                            accept=".json,.html,.txt" class="file-input">
                                        <small>Formatos aceitos: JSON, HTML, TXT</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="mensagem_criptografada">Ou cole a matriz aqui:</label>
                                        <textarea name="mensagem_criptografada" id="mensagem_criptografada" rows="6"
                                            placeholder="Exemplos de formato:
1. Matriz simples: [[180,300],[484,790]]
2. Mensagem (JSON): {'mensagem_criptografada':[[53,...],[140,...]]}
3. Mensagem (HTML): &lt;h2&gt;Mensagem Criptografada&lt;/h2&gt;&lt;table&gt;...">    <?php echo htmlspecialchars($matrizDescriptografadaInput); ?></textarea>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="descriptografar" class="btn"><i class="fas fa-lock-open"></i>Descriptografar</button>
                            <button type="button" onclick="carregarExemploMensagem()" class="btn"><i class="fas fa-file-import"></i>Exemplo Mensagem</button>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <?php if (!empty($matrizResultado)): ?>
                <section class="result-section">
                    <div class="section-header">
                        <h2>Resultado da Descriptografia</h2>
                        <p><?php echo $eMensagem ? 'Mensagem descriptografada' : 'Matriz descriptografada'; ?></p>
                    </div>

                    <div class="result-container">
                        <div class="original-matrices">
                            <div class="result-box">
                                <h3>Matriz Descodificadora D⁻¹</h3>
                                <?php echo exibirMatrizHTML($matrizDescriptografadora); ?>
                                <div class="info-box">
                                    <p><strong>Determinante:</strong> <?php echo $det; ?></p>
                                </div>
                            </div>

                            <div class="operator-icon">
                                ×
                            </div>

                            <div class="result-box">
                                <h3>Matriz Criptografada</h3>
                                <?php echo exibirMatrizHTML($matrizCriptografada); ?>
                                <div class="info-box tipo-info">
                                    <?php if ($eMensagem): ?>
                                        <p><i class="fas fa-envelope"></i> <strong>Tipo:</strong> Mensagem</p>
                                    <?php else: ?>
                                        <p><i class="fas fa-table"></i> <strong>Tipo:</strong> Matriz</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="operator-icon">
                                =
                            </div>

                            <div class="result-box highlight">
                                <h3>Resultado</h3>
                                <?php echo exibirMatrizHTML($matrizResultado); ?>
                            </div>
                        </div>

                        <?php if ($eMensagem && !empty($mensagemDescriptografada)): ?>
                            <div class="original-matrices">
                                <div class="result-box full-width">
                                    <h3>Conversão para Texto</h3>
                                    <div class="conversao-container">
                                        <?php
                                        $numColunas = count($matrizResultado[0]);
                                        $numeros = [];

                                        for ($j = 0; $j < $numColunas; $j++) {
                                            for ($i = 0; $i < $dimensaoCodificadora; $i++) {
                                                if (isset($matrizResultado[$i][$j])) {
                                                    $valor = $matrizResultado[$i][$j];
                                                    $valorArredondado = round($valor);
                                                    if ($valorArredondado < 1)
                                                        $valorArredondado = 1;
                                                    if ($valorArredondado > 30)
                                                        $valorArredondado = 30;
                                                    $numeros[] = $valorArredondado;
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="numeros-section">
                                            <h4>Números convertidos:</h4>
                                            <div class="numeros-grid">
                                                <?php foreach ($numeros as $numero): ?>
                                                    <span class="numero-item"><?php echo sprintf('%2d', $numero); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="mensagem-section">
                                            <h4>Mensagem descriptografada:</h4>
                                            <div class="message-box">
                                                <p class="mensagem-resultado">
                                                    <?php echo htmlspecialchars($mensagemDescriptografada); ?></p>
                                                <div class="message-info">
                                                    <p><i class="fas fa-font"></i>
                                                        <?php echo strlen($mensagemDescriptografada); ?> caracteres</p>
                                                    <p><i class="fas fa-hashtag"></i> <?php echo count($numeros); ?> números</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php elseif (!$eMensagem): ?>
                            <div class="original-matrices">
                                <div class="result-box full-width">
                                    <h3>Matriz Descriptografada</h3>
                                    <div class="matriz-final">
                                        <?php echo exibirMatrizHTML($matrizResultado); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="result-actions">
                        <a href="?novo=1" class="btn">
                            <i class="fas fa-redo"></i>Nova Descriptografia
                        </a>
                    </div>
                </section>
            <?php endif; ?>
        </main>

        <footer class="operation-footer">
            <div>
                <p>Descriptografia com Matrizes - Projeto Interdisciplinar 2025/2026</p>
            </div>
        </footer>
    </div>

    <script>
        function carregarExemploMensagem() {
            const exemplo = '{"mensagem_criptografada":[[53,57,47,41,53,43,59,41,16,71,73,55,22,69],[140,157,128,105,140,122,155,105,47,184,190,144,62,180]]}';
            document.getElementById('mensagem_criptografada').value = exemplo;
            alert('Exemplo de MENSAGEM criptografada carregado!');
        }

        function preencherInversa() {
            const dimensao = document.getElementById('dimensao').value;

            if (dimensao == '2') {
                // Inversa de [[1,2],[3,5]]
                document.querySelector('[name="descodificadora_0_0"]').value = -5;
                document.querySelector('[name="descodificadora_0_1"]').value = 2;
                document.querySelector('[name="descodificadora_1_0"]').value = 3;
                document.querySelector('[name="descodificadora_1_1"]').value = -1;
            } else {
                // Inversa de [[1,2,1],[2,3,2],[1,2,2]]
                document.querySelector('[name="descodificadora_0_0"]').value = 2;
                document.querySelector('[name="descodificadora_0_1"]').value = -2;
                document.querySelector('[name="descodificadora_0_2"]').value = 1;
                document.querySelector('[name="descodificadora_1_0"]').value = -2;
                document.querySelector('[name="descodificadora_1_1"]').value = 1;
                document.querySelector('[name="descodificadora_1_2"]').value = 0;
                document.querySelector('[name="descodificadora_2_0"]').value = 1;
                document.querySelector('[name="descodificadora_2_1"]').value = 0;
                document.querySelector('[name="descodificadora_2_2"]').value = -1;
            }

            alert('Matriz descodificadora padrão carregada!');
        }

        document.getElementById('arquivo_upload')?.addEventListener('change', function (e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('mensagem_criptografada').value = e.target.result;
                };
                reader.readAsText(file);
            }
        });
    </script>
</body>

</html>