<?php

// Inclui o arquivo de conexão com o banco de dados
include 'conexao.php';

/**
 * Verifica se um horário está disponível para agendamento, considerando um intervalo de tempo.
 *
 * @param mysqli $conn A conexão com o banco de dados.
 * @param string $datahora_str A string representando a data e hora a verificar (no formato YYYY-MM-DD HH:MM).
 * @param int $intervalo O intervalo em minutos a ser considerado para verificar a disponibilidade.
 * @return bool Retorna true se o horário estiver disponível (nenhum agendamento conflitando dentro do intervalo), false caso contrário.
 */
function horarioDisponivel($conn, $datahora_str, $intervalo)
{
    try {
        // Tenta criar um objeto DateTime a partir da string de data e hora fornecida
        $datahora = new DateTime($datahora_str);
    } catch (Exception $e) {
        // Se ocorrer um erro ao criar o objeto DateTime (formato inválido), registra o erro e retorna false
        error_log("Erro ao criar DateTime: " . $e->getMessage() . " - Data/Hora: " . $datahora_str);
        return false; // Ou lançar uma exceção, dependendo do seu tratamento de erros
    }
    // Clona o objeto DateTime inicial e adiciona o intervalo para obter o horário de fim do agendamento
    $datahora_fim = (clone $datahora)->modify('+' . $intervalo . ' minutes');

    // Define a consulta SQL para verificar se existe algum agendamento que se sobrepõe ao horário desejado (incluindo o intervalo)
    // A condição verifica se algum agendamento existente começa antes do fim do novo agendamento E termina depois do início do novo agendamento
    $sql = "SELECT COUNT(*) AS count FROM agendamentos WHERE
            (datahora < '" . $datahora_fim->format('Y-m-d H:i:s') . "' AND
             ADDTIME(datahora, SEC_TO_TIME($intervalo * 60)) > '" . $datahora->format('Y-m-d H:i:s') . "')";

    $result = $conn->query($sql); // Executa a consulta
    if ($result) {
        $row = $result->fetch_assoc(); // Obtém a linha de resultado
        return $row['count'] == 0; // Se a contagem for 0, o horário está disponível (true), caso contrário (false)
    }
    return false; // Retorna false em caso de erro na consulta
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = htmlspecialchars(trim($_POST['nome']));
    $telefone = htmlspecialchars(trim($_POST['telefone']));
    $servico = htmlspecialchars(trim($_POST['servico']));
    $data = htmlspecialchars(trim($_POST['data']));
    $hora = htmlspecialchars(trim($_POST['hora']));

    // --- NOVA LÓGICA: Obter feriados do banco de dados para validação ---
    $feriados = [];
    $sqlFeriados = "SELECT data FROM feriados";
    $resultFeriados = $conn->query($sqlFeriados);

    // ATENÇÃO: AQUI ESTAVA O ERRO DE DIGITAÇÃO. Corrigido para $resultFeriados
    if ($resultFeriados && $resultFeriados->num_rows > 0) {
        while ($rowFeriado = $resultFeriados->fetch_assoc()) {
            $feriados[] = $rowFeriado['data'];
        }
    }
    // ------------------------------------------------------------------

    // **VALIDAÇÃO: Impedir agendamento no domingo ou feriado**
    try {
        $data_obj = new DateTime($data);
        $dia_da_semana = (int) $data_obj->format('w'); // 0 (para domingo) através de 6 (para sábado)
        $ehFeriado = in_array($data, $feriados); // Verifica se a data está na lista de feriados

        if ($dia_da_semana === 0 || $ehFeriado) { // Se for domingo ou feriado
            $mensagemAlerta = '';
            if ($dia_da_semana === 0) {
                $mensagemAlerta = 'Não é possível agendar aos domingos. Por favor, escolha outro dia.';
            } else if ($ehFeriado) {
                $mensagemAlerta = 'Não é possível agendar em feriados. Por favor, escolha outro dia.';
            }
            echo "<script>alert('$mensagemAlerta'); window.location.href='barbearia_do_corte.php?data=$data&nome=$nome&telefone=$telefone&servico=$servico';</script>";
            exit; // Interrompe o script
        }
    } catch (Exception $e) {
        // Erro ao processar a data, logar e retornar erro
        error_log("Erro ao validar dia da semana/feriado: " . $e->getMessage() . " - Data: " . $data);
        echo "<script>alert('Erro ao processar a data. Por favor, tente novamente.'); window.location.href='barbearia_do_corte.php?data=$data&nome=$nome&telefone=$telefone&servico=$servico';</script>";
        exit;
    }
    // **FIM DA NOVA VALIDAÇÃO**

    $datahora_str = $data . " " . $hora;
    $intervalo_minutos = 40; // 40 minutos

    // Tenta criar um objeto DateTime a partir da string de data e hora fornecida
    try {
        $datahora = new DateTime($datahora_str);
    } catch (Exception $e) {
        // Se ocorrer um erro ao formatar a data e hora, registra o erro, exibe um alerta e redireciona o usuário de volta ao formulário
        error_log("Erro ao formatar DateTime: " . $e->getMessage() . " - Data/Hora: " . $datahora_str);
        echo "<script>alert('Erro ao processar a data/hora. Por favor, tente novamente.'); window.location.href='barbearia_do_corte.php?data=$data&nome=$nome&telefone=$telefone&servico=$servico';</script>";
        exit; // Importante: interromper a execução para evitar inserção incorreta no banco de dados
    }

    // Verifica a disponibilidade do horário
    if (!horarioDisponivel($conn, $datahora_str, $intervalo_minutos)) {
        echo "<script>alert('O horário selecionado não está mais disponível ou se sobrepõe a outro agendamento. Por favor, escolha outro horário.'); window.location.href='barbearia_do_corte.php?data=$data&nome=$nome&telefone=$telefone&servico=$servico';</script>";
        exit;
    }

    // Formata a data e hora para o formato aceito pelo MySQL (YYYY-MM-DD HH:MM:SS)
    $datahora = $datahora->format('Y-m-d H:i:s');

    // Define a consulta SQL para inserir os dados do agendamento na tabela 'agendamentos'
    $sql = "INSERT INTO agendamentos (nome, telefone, servico, datahora)
                VALUES ('$nome', '$telefone', '$servico', '$datahora')";

    // Executa a consulta SQL de inserção
    // Se a inserção for bem-sucedida, exibe um alerta de sucesso e redireciona o usuário de volta ao formulário,
    // mantendo a data selecionada para facilitar novos agendamentos na mesma data
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Agendamento realizado com sucesso!'); window.location.href='barbearia_do_corte.php?data=$data';</script>";
    } else {
        // Se ocorrer um erro durante a inserção, exibe uma mensagem de erro detalhada
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
}

// Fecha a conexão com o banco de dados
$conn->close();
?>