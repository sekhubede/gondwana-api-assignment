import { validateGuests } from "./guests.js";
import { validateDates } from "./validateDates.js";
import { API_BASE } from "./api.js";

export function initFormHandler(refs, renderRates) {
  refs.form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const submitBtn = refs.form.querySelector("button[type='submit']");

    try {
      const { arrival, departure } = validateDates(refs);
      const ages = validateGuests(refs);

      const payload = {
        Arrival: arrival.toLocaleDateString("en-GB"),
        Departure: departure.toLocaleDateString("en-GB"),
        Ages: ages,
      };

      submitBtn.disabled = true;
      submitBtn.innerHTML = `<span class="animate-spin">⏳</span> Loading...`;

      const response = await fetch(`${API_BASE}/rates`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      const result = await response.json();
      if (!result.success) throw new Error(result.message || "Unknown error");

      renderRates(
        refs,
        result.data?.Legs || [],
        result.data?.["Total Charge"] ?? 0,
        result.data?.["Booking Group ID"] ?? null,
        arrival,
        departure,
        result.data?.Rooms ?? 0
      );
    } catch (err) {
      refs.resultsSection.classList.remove("hidden");
      refs.cardsContainer.innerHTML = `<p class="alert-error">❌ ${err.message}</p>`;
      refs.grandTotalBar.classList.add("hidden");
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = `<span>⚡</span> Get Rates`;
    }
  });
}