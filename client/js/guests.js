import { guestContainer, guestLabel } from "./domRefs.js";
import { guestCount, incrementGuests, decrementGuests } from "./state.js";
import { pluralize } from "./utils.js";

export function updateGuestLabel() {
  if (!guestLabel) return;
  guestLabel.textContent = `Guest Ages (${pluralize(guestCount, "Guest", "Guests")})`;
}

export function updateRemoveButtons() {
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
  inputWrapper.className = "flex items-center border border-gray-300 rounded-md w-full";
  inputWrapper.title = "Guest age in years";

  const input = document.createElement("input");
  input.type = "number";
  input.min = "0";
  input.max = "120";
  input.value = age;
  input.placeholder = "Age";
  input.className = "flex-1 px-3 py-2 rounded-l-md focus:outline-none focus:ring-2 focus:ring-gray-700";

  const unit = document.createElement("span");
  unit.textContent = "years";
  unit.className = "px-2 text-gray-500 text-sm";

  inputWrapper.appendChild(input);
  inputWrapper.appendChild(unit);

  const removeBtn = document.createElement("button");
  removeBtn.type = "button";
  removeBtn.setAttribute("data-remove-guest", "");
  removeBtn.textContent = "−";
  removeBtn.className = "px-3 py-2 bg-gray-100 border border-gray-300 rounded-md hover:bg-red-100 text-red-500 font-bold";
  removeBtn.addEventListener("click", () => {
    if (guestCount > 1) {
      wrapper.remove();
      decrementGuests();
      updateGuestLabel();
      updateRemoveButtons();
    }
  });

  wrapper.appendChild(inputWrapper);
  wrapper.appendChild(removeBtn);
  return wrapper;
}

export function addGuestInput(age = "") {
  const row = createGuestRow(age);
  guestContainer.appendChild(row);
  incrementGuests();
  updateGuestLabel();
  updateRemoveButtons();
}

export function validateGuests() {
  const inputs = guestContainer.querySelectorAll('input[type="number"]');
  const ages = [];
  for (const input of inputs) {
    if (!input.value.trim()) throw new Error("All guest ages must be filled in.");
    const age = parseInt(input.value, 10);
    if (Number.isNaN(age) || age < 0) {
      throw new Error("Guest ages must be valid positive numbers.");
    }
    ages.push(age);
  }
  return ages;
}