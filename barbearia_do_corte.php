<?php
// Inclui o arquivo de conexão com o banco de dados
include 'conexao.php';

// --- NOVA LÓGICA: Obter feriados do banco de dados ---
$feriados = []; // Inicializa como array vazio
$sqlFeriados = "SELECT data FROM feriados";
$resultFeriados = $conn->query($sqlFeriados);

// ATENÇÃO: AQUI ESTAVA O ERRO DE DIGITAÇÃO. Corrigido para $resultFeriados
if ($resultFeriados && $resultFeriados->num_rows > 0) {
    while ($rowFeriado = $resultFeriados->fetch_assoc()) {
        $feriados[] = $rowFeriado['data']; // Adiciona a data do feriado ao array
    }
}
// ----------------------------------------------------

// Define a senha secreta para acessar a lista de agendamentos
$senhaSecreta = "123";
// Inicializa uma variável para controlar se o acesso foi liberado
$acessoLiberado = false;
// Inicializa uma variável para armazenar o resultado da consulta ao banco de dados
$result = null;

// Verifica se o método da requisição é POST (quando o formulário de senha é enviado)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém a senha digitada pelo usuário do campo 'senha' no formulário
    $senhaDigitada = $_POST['senha'];

    // Compara a senha digitada com a senha secreta
    if ($senhaDigitada === $senhaSecreta) {
        // Se as senhas coincidem, define a variável de acesso como true
        $acessoLiberado = true;
        // Define a consulta SQL para selecionar todos os registros da tabela 'agendamentos'
        $sql = "SELECT * FROM agendamentos";
        // Executa a consulta SQL no banco de dados e armazena o resultado na variável '$result'
        $result = $conn->query($sql);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento - Barbearia</title>
    <link rel="stylesheet" href="stylee.css">
    <script>
        // Array de feriados injetado pelo PHP
        const feriados = <?php echo json_encode($feriados); ?>;

        // Função JavaScript para selecionar um horário disponível
        function selecionarHorario(elemento) {
            // Remove a classe 'selecionado' de todos os elementos com a classe 'horario'
            document.querySelectorAll('.horario').forEach(el => el.classList.remove('selecionado'));

            // Adiciona a classe 'selecionado' ao elemento clicado para destacar o horário selecionado
            elemento.classList.add('selecionado');

            // Define o valor do campo oculto 'hora' com o valor do atributo 'data-hora' do elemento clicado
            document.getElementById('hora').value = elemento.dataset.hora;
        }

        // Função JavaScript para buscar os horários disponíveis para uma data selecionada
        function buscarHorarios() {
            // Obtém o valor da data selecionada no campo de input com o id 'data'
            var dataSelecionada = document.getElementById('data').value;

            // Se a data selecionada estiver vazia, a função retorna sem fazer nada
            if (dataSelecionada === "") return;

            // **VALIDAÇÃO: Verificar se é domingo ou feriado**
            // Adiciona 'T00:00:00' para evitar problemas de fuso horário ao criar o objeto Date
            var dataObj = new Date(dataSelecionada + "T00:00:00");
            var diaDaSemana = dataObj.getDay(); // 0 = Domingo, 1 = Segunda, ..., 6 = Sábado

            var ehFeriado = feriados.includes(dataSelecionada); // Verifica se a data está na lista de feriados

            if (diaDaSemana === 0 || ehFeriado) { // Se for domingo ou feriado
                let mensagem = '';
                if (diaDaSemana === 0) {
                    mensagem = 'A barbearia não funciona aos domingos.';
                } else if (ehFeriado) {
                    mensagem = 'Este dia é feriado e a barbearia não está aberta para agendamentos.';
                }
                document.getElementById('horarios-disponiveis').innerHTML = `<p class="aviso-dia-indisponivel">${mensagem}</p>`;
                document.getElementById('hora').value = ''; // Limpa o horário selecionado
                return; // Interrompe a função
            }
            // **FIM DA NOVA VALIDAÇÃO**

            // Cria um novo objeto XMLHttpRequest para fazer uma requisição assíncrona ao servidor
            var xhr = new XMLHttpRequest();

            // Define a função a ser executada quando o estado da requisição muda
            xhr.onreadystatechange = function () {
                // Verifica se a requisição foi concluída (readyState == 4) e se foi bem-sucedida (status == 200)
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Atualiza o conteúdo do div 'horarios-disponiveis' com a resposta do servidor
                    document.getElementById('horarios-disponiveis').innerHTML = xhr.responseText;
                }
            };

            // Abre uma requisição GET para o script 'buscar_horarios.php', passando a data selecionada como parâmetro
            xhr.open("GET", "buscar_horarios.php?data=" + dataSelecionada, true);

            // Envia a requisição
            xhr.send();
        }

        document.addEventListener('DOMContentLoaded', function () {
            var inputData = document.getElementById('data');
            inputData.addEventListener('change', function () {
                var selectedDate = new Date(this.value + "T00:00:00");
                var diaDaSemana = selectedDate.getDay();
                var ehFeriado = feriados.includes(this.value); // Verifica se a data está na lista de feriados

                if (diaDaSemana === 0 || ehFeriado) { // 0 = Domingo
                    let mensagem = '';
                    if (diaDaSemana === 0) {
                        mensagem = 'Não é possível agendar aos domingos. Por favor, escolha outro dia.';
                    } else if (ehFeriado) {
                        mensagem = 'Não é possível agendar em feriados. Por favor, escolha outro dia.';
                    }
                    alert(mensagem);
                    this.value = ''; // Limpa a data selecionada
                    document.getElementById('horarios-disponiveis').innerHTML = ''; // Limpa os horários
                    document.getElementById('hora').value = ''; // Limpa o horário selecionado
                } else {
                    buscarHorarios(); // Chama a função normal se não for domingo ou feriado
                }
            });

            // Opcional: Definir data mínima como hoje e desabilitar domingos no calendário nativo (nem todos os browsers suportam)
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            var minDate = yyyy + '-' + mm + '-' + dd;
            inputData.setAttribute('min', minDate);
        });
    </script>
</head>

<body>
    <div class="container">
        <h1>Agende seu Corte</h1>
        <form action="processoagendamento.php" method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" placeholder="Seu nome completo" required>

            <label for="telefone">Telefone:</label>
            <input type="tel" id="telefone" name="telefone" placeholder="XX XXXXXXXXX" required
                pattern="[0-9]{2} [0-9]{9}">
            <label for="servico">Serviço:</label>
            <select id="servico" name="servico" required>
                <option value="">Selecione um serviço</option>
                <option value="Corte Degradê - R$35,00">Corte Degradê - R$35,00</option>
                <option value="Corte Social - R$30,00">Corte Social - R$30,00</option>
                <option value="Barba - R$25,00">Barba - R$25,00</option>
                <option value="Platinado - R$120,00">Platinado - R$120,00</option>
                <option value="Combo Corte + Barba - R$50,00">Combo Corte + Barba - R$50,00</option>
            </select>

            <label for="data">Data:</label>
            <input type="date" id="data" name="data" required>
            <input type="hidden" id="hora" name="hora">

            <div id="horarios-disponiveis">
                <?php
                // Definições de horário
                $hora_inicio = strtotime("09:00");
                $hora_fim = strtotime("18:00");
                $intervalo = 40 * 60; // 40 minutos em segundos
                
                $data_atual = date("Y-m-d");

                // **NOVA VALIDAÇÃO PHP: Verificar se a data atual é domingo ou feriado**
                $timestamp_data_atual = strtotime($data_atual);
                $dia_da_semana_atual = date('w', $timestamp_data_atual); // 0 = Domingo, 1 = Segunda, ...
                $ehFeriadoAtual = in_array($data_atual, $feriados);

                if ($dia_da_semana_atual == 0 || $ehFeriadoAtual) { // Se for domingo (0) ou feriado
                    $mensagemAviso = '';
                    if ($dia_da_semana_atual == 0) {
                        $mensagemAviso = 'A barbearia não funciona aos domingos.';
                    } else if ($ehFeriadoAtual) {
                        $mensagemAviso = 'Este dia é feriado e a barbearia não está aberta para agendamentos.';
                    }
                    echo '<p class="aviso-dia-indisponivel">' . $mensagemAviso . '</p>';
                } else {
                    // Buscar horários agendados para a data atual
                    $horarios_agendados = array();
                    $sql = "SELECT datahora FROM agendamentos WHERE DATE(datahora) = '$data_atual'";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $horarios_agendados[] = date('H:i', strtotime($row['datahora']));
                        }
                    }

                    // Loop para gerar os horários disponíveis dentro do intervalo definido
                    for ($i = $hora_inicio; $i <= $hora_fim; $i += $intervalo) {
                        $horario_str = date("H:i", $i);
                        $classe = "horario";
                        if (in_array($horario_str, $horarios_agendados)) {
                            $classe .= " indisponivel";
                        }
                        echo "<div class='$classe' data-hora='$horario_str' onclick=\"selecionarHorario(this)\">$horario_str</div>";
                    }
                }
                ?>
            </div>

            <button type="submit">Agendar</button>
        </form>
    </div>
</body>

</html>