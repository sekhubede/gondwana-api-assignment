import { cardsContainer, grandTotalBar, resultsSection } from "./domRefs.js";
import { formatDateRange } from "./utils.js";

export function renderRates(legs, totalCharge, bookingGroup, arrival, departure, roomsTopLevel) {
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
