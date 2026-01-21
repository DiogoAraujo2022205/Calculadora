<?php
session_start();
include_once '../includes/functions.php';

$resultado = '';
$erro = '';
$matriz = [];
$matrizResultado = [];
$escalar = 2;
$linhas = 2;
$colunas = 2;

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['configurar'])) {
        // Configurar dimensões e escalar
        $escalar = floatval($_POST['escalar'] ?? 2);
        $linhas = intval($_POST['linhas'] ?? 2);
        $colunas = intval($_POST['colunas'] ?? 2);
        
        if ($linhas < 1 || $linhas > 6 || $colunas < 1 || $colunas > 6) {
            $erro = "As dimensões devem estar entre 1x1 e 6x6 para melhor visualização.";
        }
        
        // Salvar nas variáveis de sessão
        $_SESSION['escalar'] = $escalar;
        $_SESSION['linhas'] = $linhas;
        $_SESSION['colunas'] = $colunas;
    }
    elseif (isset($_POST['calcular'])) {
        // Calcular operação
        $escalar = $_SESSION['escalar'] ?? 2;
        $linhas = $_SESSION['linhas'] ?? 2;
        $colunas = $_SESSION['colunas'] ?? 2;
        
        // Criar matriz a partir dos dados
        $matriz = criarMatriz($_POST, $linhas, $colunas, 'matriz');
        
        // Validar se todos os campos foram preenchidos
        $camposVazios = false;
        for ($i = 0; $i < $linhas; $i++) {
            for ($j = 0; $j < $colunas; $j++) {
                if (!isset($_POST["matriz_{$i}_{$j}"]) || $_POST["matriz_{$i}_{$j}"] === '') {
                    $camposVazios = true;
                    break 2;
                }
            }
        }
        
        if ($camposVazios) {
            $erro = "Por favor, preencha todos os campos da matriz.";
        } else {
            // Realizar operação
            $matrizResultado = multiplicarPorEscalar($matriz, $escalar);
        }
    }
} else {
    // Valores padrão
    $escalar = $_SESSION['escalar'] ?? 2;
    $linhas = $_SESSION['linhas'] ?? 2;
    $colunas = $_SESSION['colunas'] ?? 2;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multiplicação por Escalar</title>
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
                    <h1>Multiplicação por Escalar</h1>
                </div>
                <a href="../index.php" class="back-btn">
                    Voltar ao Menu
                </a>
            </div>    
            
            <div class="operation-instructions">
                <p><strong>Fórmula:</strong> Cada elemento da matriz é multiplicado pelo escalar: A[i,j] × k</p>
            </div>
        </header>

        <!-- Conteúdo Principal -->
        <main class="operation-main">
            <!-- Formulário de Configuração -->
            <section class="config-section">
                <div class="section-header">
                    <h2> Configuração</h2>
                    <p>Defina o escalar e as dimensões da matriz</p>
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
                            <label for="escalar">
                                Valor do Escalar
                            </label>
                            <input type="number" step="any" name="escalar" id="escalar" 
                                   value="<?php echo $escalar; ?>" required class="form-input"
                                   placeholder="Ex: 2, 3.5, -1">
                        </div>
                        
                        <div class="form-group">
                            <label for="linhas">
                                 Número de Linhas
                            </label>
                            <input type="number" name="linhas" id="linhas" 
                                   min="1" max="6" value="<?php echo $linhas; ?>" required
                                   class="form-input">
                        </div>
                        
                        <div class="form-group">
                            <label for="colunas">
                                 Número de Colunas
                            </label>
                            <input type="number" name="colunas" id="colunas" 
                                   min="1" max="6" value="<?php echo $colunas; ?>" required
                                   class="form-input">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="configurar" class="btn">
                        Configurar Matriz
                        </button>
                        
                        <?php if (isset($_POST['configurar']) || isset($_SESSION['linhas'])): ?>
                            <button type="button" onclick="preencherAleatorio()" class="btn">
                              Preencher Aleatoriamente
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <!-- Entrada de Dados -->
            <?php if (isset($_POST['configurar']) || isset($_SESSION['linhas'])): ?>
            <section class="input-section">
                <div class="section-header">
                    <h2>Entrada de Dados</h2>
                </div>
                
                <form method="POST" class="matrix-input-form">
                    <!-- Dados ocultos para manter configuração -->
                    <input type="hidden" name="escalar" value="<?php echo $escalar; ?>">
                    <input type="hidden" name="linhas" value="<?php echo $linhas; ?>">
                    <input type="hidden" name="colunas" value="<?php echo $colunas; ?>">
                    
                    <div class="matrices-container">
                     
                        
                        <!-- Matriz -->
                        <div class="matrix-box">
                            <div class="matrix-header">
                                <h3>Matriz Original</h3>
                            </div>
                            <div class="matrix-grid">
                                <?php for ($i = 0; $i < $linhas; $i++): ?>
                                    <div class="matrix-row">
                                        <?php for ($j = 0; $j < $colunas; $j++): 
                                            $nome = "matriz_{$i}_{$j}";
                                            $valor = isset($_POST[$nome]) ? $_POST[$nome] : '';
                                        ?>
                                            <div class="matrix-cell">
                                                <input type="number" step="any" 
                                                       name="<?php echo $nome; ?>" 
                                                       value="<?php echo $valor; ?>"
                                                       placeholder="0"
                                                       class="matrix-input">
                                               
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                  
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="calcular" class="btn">
                             Calcular Multiplicação
                        </button>
                        <button type="reset" class="btn">
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
                    <!-- Matriz Original e Resultado -->
                    <div class="original-matrices">
                        <div class="result-box">
                            <h3> Matriz Original</h3>
                            <?php echo exibirMatrizHTML($matriz); ?>

                        </div>
                        
                        <div class="operator-result">
                            <div class="operator-icon">
                                <i class="fas fa-times"></i>
                            </div>
                            <div class="operator-info">
                                <p>Escalar  <h3><?php echo $escalar; ?></h3></p>
    
                            </div>
                        </div>
                    
                    </div>
                    
                    <!-- Matriz Resultado -->
                    <div class="final-result">
                        <div class="result-box highlight">
                            <h3>Matriz Resultado</h3>
                            <?php echo exibirMatrizHTML($matrizResultado); ?>
                        </div>
                    </div>
                </div>
                
                <div class="result-actions">
                    <button onclick="salvarResultado()" class="btn">
                    Salvar Resultado
                    </button>
                    <a href="?novo=1" class="btn">
                    Novo Cálculo
                    </a>
                </div>
            </section>
            <?php endif; ?>
        </main>

        <!-- Rodapé da Operação -->
        <footer class="operation-footer">
            <div class="footer-copyright">
                <p>Multiplicação por Escalar - Projeto Interdisciplinar 2025/2026</p>
            </div>
        </footer>
    </div>

    <script>
        // Função para preencher valores aleatórios
        function preencherAleatorio() {
            <?php for ($i = 0; $i < $linhas; $i++): ?>
                <?php for ($j = 0; $j < $colunas; $j++): ?>
                    document.getElementsByName('matriz_<?php echo $i; ?>_<?php echo $j; ?>')[0].value = Math.floor(Math.random() * 20) - 10;
                <?php endfor; ?>
            <?php endfor; ?>
            
            // Mostrar mensagem
            alert('Matriz preenchida com valores aleatórios entre -10 e 10!');
        }
        
        // Focar no primeiro campo ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            const primeiroCampo = document.querySelector('.matrix-input');
            if (primeiroCampo) {
                primeiroCampo.focus();
            }
        });
        
    </script>
</body>
</html>