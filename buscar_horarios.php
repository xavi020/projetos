<?php
include 'conexao.php';

if (!isset($_GET['data'])) {
    echo "Data não informada!";
    exit;
}

$data_selecionada = $_GET['data'];

// --- NOVA LÓGICA: Obter feriados do banco de dados ---
$feriados = [];
$sqlFeriados = "SELECT data FROM feriados";
$resultFeriados = $conn->query($sqlFeriados);

// ATENÇÃO: AQUI ESTAVA O ERRO DE DIGITAÇÃO. Corrigido para $resultFeriados
if ($resultFeriados && $resultFeriados->num_rows > 0) {
    while ($rowFeriado = $resultFeriados->fetch_assoc()) {
        $feriados[] = $rowFeriado['data'];
    }
}
// ----------------------------------------------------

// **VALIDAÇÃO: Verificar se a data selecionada é domingo ou feriado**
$timestamp_data_selecionada = strtotime($data_selecionada);
$dia_da_semana = date('w', $timestamp_data_selecionada); // 0 = Domingo, 1 = Segunda, ...
$ehFeriado = in_array($data_selecionada, $feriados); // Verifica se a data está na lista de feriados

if ($dia_da_semana == 0 || $ehFeriado) { // Se for domingo ou feriado
    $mensagemAviso = '';
    if ($dia_da_semana == 0) {
        $mensagemAviso = 'A barbearia não funciona aos domingos.';
    } else if ($ehFeriado) {
        $mensagemAviso = 'Este dia é feriado e a barbearia não está aberta para agendamentos.';
    }
    echo '<p class="aviso-dia-indisponivel">' . $mensagemAviso . '</p>';
    exit; // Interrompe a execução para não gerar horários
}
// **FIM DA NOVA VALIDAÇÃO**


$hora_inicio = strtotime("09:00");
$hora_fim = strtotime("18:00");
$intervalo = 40 * 60; // 40 minutos

// Buscar horários agendados
$horarios_agendados = array();
$sql = "SELECT datahora FROM agendamentos WHERE DATE(datahora) = '$data_selecionada'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $horarios_agendados[] = date('H:i', strtotime($row['datahora']));
    }
}

// Gerar os horários disponíveis
for ($i = $hora_inicio; $i <= $hora_fim; $i += $intervalo) {
    $horario_str = date("H:i", $i);
    $classe = "horario";
    if (in_array($horario_str, $horarios_agendados)) {
        $classe .= " indisponivel";
    }
    echo "<div class='$classe' data-hora='$horario_str' onclick=\"selecionarHorario(this)\">$horario_str</div>";
}
?>