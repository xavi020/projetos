let produtos = [];

document.getElementById("adicionarProduto").addEventListener("click", function() {
    const produto = prompt("Digite o nome do produto:");
    const preco = parseFloat(prompt("Digite o preço do produto:"));

    if (produto && !isNaN(preco)) {
        produtos.push({ nome: produto, preco: preco });
        atualizarCarrinho();
    } else {
        alert("Produto ou preço inválido!");
    }
});

function atualizarCarrinho() {
    const produtosDiv = document.getElementById("produtos");
    produtosDiv.innerHTML = "";
    produtos.forEach((p, index) => {
        produtosDiv.innerHTML += `<p>${p.nome}: R$ ${p.preco.toFixed(2)} <button onclick="removerProduto(${index})">Remover</button></p>`;
    });
}

function removerProduto(index) {
    produtos.splice(index, 1);
    atualizarCarrinho();
}

document.getElementById("finalizarCompra").addEventListener("click", function() {
    if (produtos.length === 0) {
        alert("Seu carrinho está vazio!");
        return;
    }

    let total = produtos.reduce((acc, p) => acc + p.preco, 0);
    const formaPagamento = prompt("Escolha a forma de pagamento:\n1 - Cartão de Crédito (sem desconto)\n2 - Cartão de Débito (6% de desconto)\n3 - PIX (10% de desconto)");

    switch (formaPagamento) {
        case "1":
            break;
        case "2":
            total *= 0.94;
            break;
        case "3":
            total *= 0.90;
            break;
        default:
            alert("Opção inválida. Nenhum desconto aplicado.");
            break;
    }

    document.getElementById("resultado").innerHTML = `Total da compra: R$ ${total.toFixed(2)}`;
});
