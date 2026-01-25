<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Matrizes - Projeto Interdisciplinar</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <!-- Cabeçalho -->
        <header class="main-header">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-calculator"></i>
                    <h1>Calculadora de Matrizes</h1>
                </div>
                <div class="project-info">
                    <p><strong>Projeto Interdisciplinar 2025/2026</strong></p>
                    <p>Matemática | Programação I | Tecnologias de Internet I</p>
                    <p>Introdução à Ciência dos Computadores</p>
                </div>
            </div>
        </header>
        <!-- Menu de Operações -->
        <main class="main-content">
            <h2 class="section-title">
                Operações Disponíveis
            </h2>

            <div class="operations-grid">
                <!-- Operação 1 -->
                <a href="operations/soma_subtracao.php" class="operation-card">
                    <div class="operation-content">
                        <h3>1. Soma e Subtração</h3>
                        <p>Soma ou subtração de matrizes de mesma dimensão</p>
                    </div>
                </a>

                <!-- Operação 2 -->
                <a href="operations/multiplicacao_escalar.php" class="operation-card">
                    <div class="operation-content">
                        <h3>2. Multiplicação por Escalar</h3>
                        <p>Multiplicação de uma matriz por um número real</p>
                    </div>
                </a>

                <!-- Operação 3 -->
                <a href="operations/multiplicacao_matrizes.php" class="operation-card">
                    <div class="operation-content">
                        <h3>3. Multiplicação de Matrizes</h3>
                        <p>Multiplicação entre duas matrizes compatíveis</p>
                    </div>
                </a>

                <!-- Operação 4 -->
                <a href="operations/determinante.php" class="operation-card">
                    <div class="operation-content">
                        <h3>4. Determinante</h3>
                        <p>Cálculo do determinante de matrizes quadradas</p>
                    </div>
                </a>

                <!-- Operação 5 -->
                <a href="operations/inversa.php" class="operation-card">
                    <div class="operation-content">
                        <h3>5. Matriz Inversa</h3>
                        <p>Cálculo da matriz inversa</p>
                    </div>
                </a>

            </div>
        </main>
        <main class="main-content">
            <div class="operations-grid">
                <!-- Operação 6 -->
                <a href="operations/criptografia.php" class="operation-card">
                    <div class="operation-content">
                        <h3>6. Criptografar</h3>
                        <p>Criptografar mensagens usando matrizes</p>
                    </div>
                </a>

                <!-- Operação 6 -->
                <a href="operations/criptografia.php" class="operation-card">
                    <div class="operation-content">
                        <h3>7. Descriptografar</h3>
                        <p>Descriptografar matrizes usando matrizes</p>
                    </div>
                </a>
            </div>
        </main>
        <!-- Rodapé -->
        <footer class="main-footer">
            <div class="footer-bottom">
                <p>Calculadora de Matrizes - Projeto Interdisciplinar 2025/2026</p>
            </div>
        </footer>
    </div>
</body>

</html>
