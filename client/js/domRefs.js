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