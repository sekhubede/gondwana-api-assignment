import { API_BASE, postJson } from "./api.js";
import { form, cardsContainer, resultsSection, grandTotalBar } from "./domRefs.js";
import { validateGuests } from "./guests.js";
import { renderRates } from "./render.js";

export function initFormHandler() {
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const arrival = document.getElementById("arrival").value;
    const departure = document.getElementById("departure").value;
    const submitBtn = form.querySelector("button[type='submit']");

    try {
      const ages = validateGuests();
      const payload = {
        Arrival: new Date(arrival).toLocaleDateString("en-GB"),
        Departure: new Date(departure).toLocaleDateString("en-GB"),
        Ages: ages,
      };

      submitBtn.disabled = true;
      submitBtn.innerHTML = `<span class="animate-spin">⏳</span> Loading...`;

      const result = await postJson(`${API_BASE}/rates`, payload);
      if (!result.success) throw new Error(result.message || "Unknown error");

      renderRates(result.data?.Legs || [], result.data?.["Total Charge"] ?? 0,
        result.data?.["Booking Group ID"] ?? null, arrival, departure, result.data?.Rooms ?? 0);
    } catch (err) {
      cardsContainer.innerHTML = `<p class="text-red-600 text-center">❌ ${err.message}</p>`;
      resultsSection.classList.remove("hidden");
      grandTotalBar.classList.add("hidden");
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = `<span>⚡</span> Get Rates`;
    }
  });
}