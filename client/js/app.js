"use strict";

/* ----------------------------------------
   Env: detect API base (Codespaces or local)
----------------------------------------- */
const API_BASE = window.location.hostname.includes("github.dev")
  ? `https://${window.location.hostname.replace("-5500", "-8080")}`
  : "http://localhost:8080";

/* ----------------------------------------
   DOM refs
----------------------------------------- */
const form = document.getElementById("rates-form");
const resultsSection = document.getElementById("results");
const cardsContainer = document.getElementById("rates-container");
const grandTotalBar = document.getElementById("grand-total");
const getRatesBtn = document.getElementById("get-rates-btn");

const guestContainer = document.getElementById("guest-ages");
const guestLabel = document.getElementById("guest-label");
const addGuestBtn = document.getElementById("add-guest");

/* ----------------------------------------
   State
----------------------------------------- */
let guestCount = 0;

/* ----------------------------------------
   Utilities
----------------------------------------- */
function pluralize(n, one, many) {
  return `${n} ${n === 1 ? one : many}`;
}

function updateGuestLabel() {
  if (!guestLabel) return;
  guestLabel.textContent = `Guest Ages (${pluralize(guestCount, "Guest", "Guests")})`;
}

function updateRemoveButtons() {
  const removeButtons = guestContainer.querySelectorAll("[data-remove-guest]");
  removeButtons.forEach((btn) => {
    const disabled = guestCount === 1;
    btn.disabled = disabled;
    btn.className = disabled
      ? "px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-400 font-bold cursor-not-allowed"
      : "px-3 py-2 bg-gray-100 border border-gray-300 rounded-md hover:bg-red-100 text-red-500 font-bold";
  });
}

function createGuestRow(age = "") {
  const wrapper = document.createElement("div");
  wrapper.className = "flex items-center gap-2";
  wrapper.setAttribute("data-guest-row", "");

  const inputWrapper = document.createElement("label");
  inputWrapper.className =
    "flex items-center border border-gray-300 rounded-md w-full";
  inputWrapper.title = "Guest age in years";

  const input = document.createElement("input");
  input.type = "number";
  input.min = "0";
  input.max = "120";
  input.value = age;
  input.placeholder = "Age";
  input.className =
    "flex-1 px-3 py-2 rounded-l-md focus:outline-none focus:ring-2 focus:ring-gray-700";

  const unit = document.createElement("span");
  unit.textContent = "years";
  unit.className = "px-2 text-gray-500 text-sm";

  inputWrapper.appendChild(input);
  inputWrapper.appendChild(unit);

  const removeBtn = document.createElement("button");
  removeBtn.type = "button";
  removeBtn.setAttribute("data-remove-guest", "");
  removeBtn.textContent = "−";
  removeBtn.className =
    "px-3 py-2 bg-gray-100 border border-gray-300 rounded-md hover:bg-red-100 text-red-500 font-bold";
  removeBtn.addEventListener("click", () => {
    if (guestCount > 1) {
      wrapper.remove();
      guestCount--;
      updateGuestLabel();
      updateRemoveButtons();
    }
  });

  wrapper.appendChild(inputWrapper);
  wrapper.appendChild(removeBtn);
  return wrapper;
}

function addGuestInput(age = "") {
  const row = createGuestRow(age);
  guestContainer.appendChild(row);
  guestCount++;
  updateGuestLabel();
  updateRemoveButtons();
}

function getAges() {
  return Array.from(guestContainer.querySelectorAll('input[type="number"]'))
    .map((el) => parseInt(el.value, 10))
    .filter((n) => !Number.isNaN(n));
}

function formatDateRange(arrival, departure) {
  const opts = { day: "numeric", month: "short", year: "numeric" };
  const start = new Date(arrival).toLocaleDateString("en-GB", opts);
  const end = new Date(departure).toLocaleDateString("en-GB", opts);
  return `${start} → ${end}`;
}

/* ----------------------------------------
   Render: booking card style
----------------------------------------- */
function renderRates(legs, totalCharge, bookingGroup, arrival, departure, roomsTopLevel) {
  cardsContainer.innerHTML = "";

  if (!legs.length) {
    cardsContainer.innerHTML =
      `<p class="text-gray-500 col-span-full text-center">No rates available for this date range.</p>`;
    resultsSection.classList.remove("hidden");
    grandTotalBar.classList.add("hidden");
    return;
  }

  const haveRooms = roomsTopLevel !== undefined && roomsTopLevel !== null;

  legs.forEach((leg) => {
    const unitName = (leg["Special Rate Description"] || "Standard Rate")
      .replace(/^\*+\s*/, "")
      .trim();

    const perNight = leg["Effective Average Daily Rate"] ?? 0;
    const perStay = leg["Total Charge"] ?? 0;

    const guestType =
      leg.Guests && leg.Guests.length ? leg.Guests[0]["Age Group"] : "Guest";

    let availabilityBadge = `
      <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
        Check availability
      </span>`;
    if (haveRooms) {
      availabilityBadge =
        roomsTopLevel > 0
          ? `<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
               ✅ Available (${roomsTopLevel} rooms left)
             </span>`
          : `<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
               ❌ Unavailable
             </span>`;
    }

    const card = document.createElement("div");
    card.className =
      "flex flex-col rounded-2xl shadow-lg hover:shadow-xl transition overflow-hidden bg-white w-full max-w-sm";

    card.innerHTML = `
      <div class="bg-gray-800 text-white p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
          <div>
            <h3 class="text-base sm:text-lg font-bold">${unitName}</h3>
            <p class="text-xs opacity-90">${guestType} Rate</p>
          </div>
          <span class="inline-block px-2 py-0.5 text-xs sm:text-sm rounded-full bg-white/20">
            ${leg.Guests?.[0]?.Category || "STANDARD"}
          </span>
        </div>
      </div>

      <div class="p-6 flex flex-col gap-4 text-center">
        <div>
          <p class="text-3xl font-extrabold text-gray-800 leading-tight">
            N$${perNight} <span class="text-sm text-gray-500 font-medium">/night</span>
          </p>
          <p class="text-gray-600 text-sm">
            Total: <span class="font-semibold">N$${perStay}</span> <span class="text-sm text-gray-500">/stay</span>
          </p>
        </div>

        <div class="flex items-center justify-center gap-2 text-sm text-gray-700">
          <span>📅</span>
          <span>${formatDateRange(arrival, departure)}</span>
        </div>

        <div class="flex items-center justify-center">
          ${availabilityBadge}
        </div>
      </div>
    `;

    cardsContainer.appendChild(card);
  });

  grandTotalBar.innerHTML =
    `Grand Total: <span class="text-gray-900 font-bold">N$${totalCharge}</span>`;
  grandTotalBar.classList.remove("hidden");
  resultsSection.classList.remove("hidden");
}

/* ----------------------------------------
   Boot: wire events + initial guest row
----------------------------------------- */
if (addGuestBtn) {
  addGuestBtn.addEventListener("click", () => addGuestInput());
}
addGuestInput();

/* ----------------------------------------
   Submit handler (with loading state)
----------------------------------------- */
/* ----------------------------------------
   Submit handler
----------------------------------------- */
/* ----------------------------------------
   Submit handler with loading + error handling
----------------------------------------- */
form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const arrival = document.getElementById("arrival").value;
  const departure = document.getElementById("departure").value;
  const ages = getAges();
  const submitBtn = form.querySelector("button[type='submit']");

  const payload = {
    Arrival: new Date(arrival).toLocaleDateString("en-GB"),
    Departure: new Date(departure).toLocaleDateString("en-GB"),
    Ages: ages,
  };

  try {
    // 🔄 Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = `<span class="animate-spin">⏳</span> Loading...`;

    const response = await fetch(`${API_BASE}/rates`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    const result = await response.json();

    // ✅ Unified backend format
    if (!result.success) {
      throw new Error(result.message || "Unknown error occurred");
    }

    renderRates(
      result.data?.Legs || [],
      result.data?.["Total Charge"] ?? 0,
      result.data?.["Booking Group ID"] ?? null,
      arrival,
      departure,
      result.data?.Rooms ?? 0
    );
  } catch (err) {
    cardsContainer.innerHTML = `
      <p class="text-red-600 text-center">
        ❌ ${err.message}
      </p>
    `;
    resultsSection.classList.remove("hidden");
    grandTotalBar.classList.add("hidden");
  } finally {
    // 🔄 Reset button
    submitBtn.disabled = false;
    submitBtn.innerHTML = `<span>⚡</span> Get Rates`;
  }
});