
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento - Barbearia</title>
    <link rel="stylesheet" href="stylee.css">
<?php include 'conexao.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Agendamento - Barbearia</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
    <h1>Agende seu horário</h1>
    <form action="processoagendamento.php" method="POST">
        <input type="text" name="nome" placeholder="Seu nome" required>
        <input type="text" name="telefone" placeholder="Seu telefone" required>
        <select name="servico" required>
        <label for="estiloCorte">Estilo de Corte:</label>
            < name="servico_id" id="estiloCorte" required>
                <option value="">Selecione</option>
                <option value="1">Corte Degradê - R$35,00</option>
                <option value="2">Corte Social - R$30,00</option>
                <option value="3">Sobrancelha - R$5,00</option>
                <option value="4">Platinado - R$120,00</option>
                <option value="5">Pigmentação - R$60,00</option>
            
        </select>
        <label for="data">Data:</label>
            <input type="date" name="data" id="data" required>

            <label for="hora">Hora:</label>
            <input type="time" name="hora" id="hora" required>

            <button type="submit">Agendar</button>
        </form>
        <div id="horariosDisponiveis">
            <h2>Horários Disponíveis <span class="scissors">✂️</span></h2>
            <ul id="dias">
                <li>Segunda a Sexta: 9:00 - 18:00</li>
                <li>Sábado: 10:00 - 16:00</li>
                <li>Domingo: Fechado</li>
            </ul>
        </div>
    </div>
</body>
</html>