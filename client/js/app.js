// Detect API base (Codespaces or local)
const API_BASE = window.location.hostname.includes("github.dev")
  ? `https://${window.location.hostname.replace("-5500", "-8080")}`
  : "http://localhost:8080";

// Initialize with one guest
const guestContainer = document.getElementById("guest-ages");
let guestCount = 0;
addGuestInput();
updateGuestLabel();

// Add guest input
document.getElementById("add-guest").addEventListener("click", () => {
  addGuestInput();
  updateGuestLabel();
  updateRemoveButtons();
});

function addGuestInput(age = "") {
  const wrapper = document.createElement("div");
  wrapper.className = "flex items-center gap-2";

  // Input box with "years" label inside
  const inputWrapper = document.createElement("div");
  inputWrapper.className = "flex items-center border border-gray-300 rounded-md w-full";

  const input = document.createElement("input");
  input.type = "number";
  input.min = "0";
  input.value = age;
  input.placeholder = "Age";
  input.className =
    "flex-1 px-3 py-2 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500";

  const unit = document.createElement("span");
  unit.textContent = "years";
  unit.className = "px-2 text-gray-500 text-sm";

  inputWrapper.appendChild(input);
  inputWrapper.appendChild(unit);

  // Remove button
  const removeBtn = document.createElement("button");
  removeBtn.type = "button";
  removeBtn.textContent = "−";
  removeBtn.className =
    "px-3 py-2 bg-gray-100 border border-gray-300 rounded-md hover:bg-red-100 text-red-500 font-bold";

  removeBtn.onclick = () => {
    if (guestCount > 1) {
      wrapper.remove();
      guestCount--;
      updateGuestLabel();
      updateRemoveButtons();
    }
  };

  wrapper.appendChild(inputWrapper);
  wrapper.appendChild(removeBtn);
  guestContainer.appendChild(wrapper);
  guestCount++;

  updateRemoveButtons();
}

function updateGuestLabel() {
  document.getElementById("guest-label").textContent = `Guest Ages (${guestCount} Guest${guestCount > 1 ? "s" : ""})`;
}

function updateRemoveButtons() {
  const removeButtons = guestContainer.querySelectorAll("button");
  removeButtons.forEach((btn) => {
    btn.disabled = guestCount === 1;
    btn.className = guestCount === 1
      ? "px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-400 font-bold cursor-not-allowed"
      : "px-3 py-2 bg-gray-100 border border-gray-300 rounded-md hover:bg-red-100 text-red-500 font-bold";
  });
}

// Handle form submit
document.getElementById("rates-form").addEventListener("submit", async (e) => {
  e.preventDefault();

  const arrival = document.getElementById("arrival").value;
  const departure = document.getElementById("departure").value;
  const ages = Array.from(guestContainer.querySelectorAll("input"))
    .map((input) => parseInt(input.value, 10))
    .filter((age) => !isNaN(age));

  const payload = {
    Arrival: new Date(arrival).toLocaleDateString("en-GB"),
    Departure: new Date(departure).toLocaleDateString("en-GB"),
    Ages: ages,
  };

  try {
    const response = await fetch(`${API_BASE}/rates`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    if (!response.ok) throw new Error(`HTTP error ${response.status}`);

    const data = await response.json();
    renderRates(data.data.Legs || []);
  } catch (err) {
    document.getElementById("rates-container").innerHTML = `<p>❌ ${err.message}</p>`;
    document.getElementById("results").classList.remove("hidden");
  }
});

// Render rates
function renderRates(legs) {
  const container = document.getElementById("rates-container");
  container.innerHTML = "";

  if (!legs.length) {
    container.innerHTML = `<p class="text-gray-500">No rates available. Try different dates or guests.</p>`;
    document.getElementById("results").classList.remove("hidden");
    return;
  }

  legs.forEach((leg, idx) => {
    const card = document.createElement("div");
    card.className =
      "relative border rounded-lg p-4 shadow hover:shadow-md transition bg-white";

    // Decide card title
    const title =
      idx === 0 ? "Standard Rate" :
      idx === 1 ? "Premium Rate" :
      "Luxury Suite";

    // Add recommended badge only to Premium (idx === 1), centered
    const badge = idx === 1
      ? `<span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
           Recommended
         </span>`
      : "";

    card.innerHTML = `
      ${badge}
      <h3 class="text-lg font-semibold mb-2">${title}</h3>
      <p class="text-2xl font-bold text-blue-600 mb-2">N$${(
        leg["Total Charge"] / 1000
      ).toFixed(0)} <span class="text-sm text-gray-500">/night</span></p>
      <ul class="text-sm text-gray-600 mb-3">
        <li>Guests: ${leg.Guests.length}</li>
        <li>Category: ${leg.Guests[0]?.Category || "N/A"}</li>
      </ul>
      <button class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-md">Select Rate</button>
    `;
    container.appendChild(card);
  });

  document.getElementById("results").classList.remove("hidden");
}