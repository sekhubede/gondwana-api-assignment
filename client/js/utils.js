export function pluralize(n, one, many) {
  return `${n} ${n === 1 ? one : many}`;
}

export function formatDateRange(arrival, departure) {
  const opts = { day: "numeric", month: "short", year: "numeric" };
  const start = new Date(arrival).toLocaleDateString("en-GB", opts);
  const end = new Date(departure).toLocaleDateString("en-GB", opts);
  return `${start} → ${end}`;
}

export function getDomRefs() {
  return {
    form: document.getElementById("rates-form"),
    resultsSection: document.getElementById("results"),
    cardsContainer: document.getElementById("rates-container"),
    grandTotalBar: document.getElementById("grand-total"),
    getRatesBtn: document.getElementById("get-rates-btn"),
    guestContainer: document.getElementById("guest-ages"),
    guestLabel: document.getElementById("guest-label"),
    addGuestBtn: document.getElementById("add-guest"),
  };
}

export function onReady(cb) {
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", cb, { once: true });
  } else {
    queueMicrotask(cb);
  }
}

export function setDateConstraints(refs) {
  const today = new Date().toISOString().split("T")[0];
  refs.form.querySelector("#arrival").setAttribute("min", today);
  refs.form.querySelector("#departure").setAttribute("min", today);
}