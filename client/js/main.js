import { addGuestBtn } from "./domRefs.js";
import { addGuestInput } from "./guests.js";
import { initFormHandler } from "./formHandler.js";

// Boot
if (addGuestBtn) {
  addGuestBtn.addEventListener("click", () => addGuestInput());
}
addGuestInput();
initFormHandler();