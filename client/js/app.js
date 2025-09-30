const API_BASE =
  window.location.hostname.includes("github.dev")
    ? `https://${window.location.hostname.replace("github.dev", "app.github.dev")}`
    : "http://localhost:8080";

document.getElementById("rates-form").addEventListener("submit", async (e) => {
  e.preventDefault();

  const arrival = document.getElementById("arrival").value;
  const departure = document.getElementById("departure").value;
  const ages = document.getElementById("ages").value
    .split(",")
    .map((age) => parseInt(age.trim(), 10))
    .filter((age) => !isNaN(age));

  const payload = {
    Arrival: new Date(arrival).toLocaleDateString("en-GB"), // dd/mm/yyyy
    Departure: new Date(departure).toLocaleDateString("en-GB"),
    Ages: ages,
  };

  try {
    const response = await fetch(`${API_BASE}/rates`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    if (!response.ok) {
      throw new Error(`HTTP error ${response.status}`);
    }

    const data = await response.json();
    document.getElementById("response").textContent = JSON.stringify(data, null, 2);
  } catch (err) {
    document.getElementById("response").textContent = "❌ Error: " + err.message;
  }
});