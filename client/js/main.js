import { getDomRefs, onReady } from "./utils.js";
import { addGuestInput, wireAddBtn } from "./guests.js";
import { renderRates } from "./render.js";
import { initFormHandler } from "./formHandler.js";

onReady(() => {
  const refs = getDomRefs();
  wireAddBtn(refs);
  addGuestInput(refs);
  initFormHandler(refs, renderRates);
});