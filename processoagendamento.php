<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $servico = $_POST['servico'];
    $datahora = $_POST['datahora'];

    $sql = "INSERT INTO agendamentos (nome, telefone, servico, datahora)
            VALUES ('$nome', '$telefone', '$servico', '$datahora')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Agendamento realizado com sucesso!'); window.location.href='barbearia_do_corte.php';</script>";
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>