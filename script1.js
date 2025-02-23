function converterTemperatura() {
    const celsius = parseFloat(document.getElementById("celsius").value);
    const fahrenheit = (celsius * 9/5) + 32;
    document.getElementById("resultado").textContent = `Temperatura em Fahrenheit: ${fahrenheit.toFixed(2)}°F`;
}
