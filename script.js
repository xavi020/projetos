// Carrega o áudio uma vez, já apontando pro arquivo certo
const furiadaAudio = new Audio("assets/audios/furiada.mp3");

function sendMessage() {
  const input = document.getElementById("userInput");
  const msg = input.value.trim();
  if (!msg) return;

  addMessage("Você", msg);
  botResponse(msg.toLowerCase());
  input.value = "";
}

function addMessage(sender, text) {
  const chat = document.getElementById("chat-box");
  chat.innerHTML += `<p><strong>${sender}:</strong> ${text}</p>`;
  chat.scrollTop = chat.scrollHeight;
}

function botResponse(msg) {
  if (msg.includes("jogo")) {
    addMessage("FURIA Bot", "Próximo jogo: 25/04 - FURIA vs NAVI às 15h (BLAST Premier)");
  } else if (msg.includes("furiada")) {
    addMessage("FURIA Bot", "🔥 É FURIADA PRA CIMA DELES 🔥");
    furiadaAudio.play(); // <- aqui toca o áudio corretamente
  } else if (msg.includes("história")) {
    addMessage("FURIA Bot", "A FURIA começou em 2017 e logo se tornou um dos maiores times de CS das Américas...");
  } else {
    addMessage("FURIA Bot", "Não entendi, mas tô na torcida com você! 🐆💥");
  }
}
