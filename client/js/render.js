import { formatDateRange } from "./utils.js";

export function renderRates(refs, legs, totalCharge, bookingGroup, arrival, departure, roomsTopLevel) {
  const { resultsSection, grandTotalBar } = refs;

  resultsSection.innerHTML = `
    <h2>Available Rates</h2>
    <p class="section-desc">Choose from the best available options for your stay</p>
    <div id="rates-container"></div>
    <div id="grand-total" class="grand-total hidden"></div>
  `;

  const ratesContainer = resultsSection.querySelector("#rates-container");
  const totalBar = resultsSection.querySelector("#grand-total");

  if (!legs.length) {
    ratesContainer.innerHTML = `<p class="text-gray-500 col-span-full text-center">No rates available for this date range.</p>`;
    resultsSection.classList.remove("hidden");
    totalBar.classList.add("hidden");
    return;
  }

  const haveRooms = roomsTopLevel !== undefined && roomsTopLevel !== null;

  legs.forEach((leg) => {
    const unitName = (leg["Special Rate Description"] || "Standard Rate")
      .replace(/^\*+\s*/, "")
      .trim();

    const perNight = leg["Effective Average Daily Rate"] ?? 0;
    const perStay = leg["Total Charge"] ?? 0;
    const guestType = leg.Guests?.length ? leg.Guests[0]["Age Group"] : "Guest";

    let availabilityBadge = `<span class="badge-available">Check availability</span>`;
    if (haveRooms) {
      availabilityBadge =
        roomsTopLevel > 0
          ? `<span class="badge-available">✅ Available (${roomsTopLevel} rooms left)</span>`
          : `<span class="badge-unavailable">❌ Unavailable</span>`;
    }

    const card = document.createElement("div");
    card.className = "rate-card";
    card.innerHTML = `
      <div class="rate-card-header">
        <span class="rate-card-badge">${leg.Guests?.[0]?.Category || "STANDARD"}</span>
        <div>
          <h3 class="text-base sm:text-lg font-bold">${unitName}</h3>
          <p class="text-xs opacity-90">${guestType} Rate</p>
        </div>
      </div>
      <div class="rate-card-body">
        <div>
          <p class="text-3xl font-extrabold text-gondwana-dark leading-tight">
            N$${perNight} <span class="text-sm text-gray-500 font-medium">/night</span>
          </p>
          <p class="text-gray-600 text-sm">
            Total: <span class="font-semibold">N$${perStay}</span> 
            <span class="text-sm text-gray-500">/stay</span>
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
    ratesContainer.appendChild(card);
  });

  totalBar.innerHTML = `Grand Total: <span>N$${totalCharge}</span>`;
  totalBar.classList.remove("hidden");
  resultsSection.classList.remove("hidden");
}