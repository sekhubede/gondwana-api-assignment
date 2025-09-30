import { pluralize } from "./utils.js";
import { guestCount, incrementGuests, decrementGuests } from "./state.js";

function updateGuestLabel(refs) {
  refs.guestLabel.textContent = `Guest Ages (${pluralize(guestCount, "Guest", "Guests")})`;
}

export function addGuestInput(refs, age = "") {
  const wrapper = document.createElement("div");
  wrapper.className = "flex flex-col gap-1";
  wrapper.setAttribute("data-guest-row", "");

  const row = document.createElement("div");
  row.className = "flex items-center gap-2";

  const input = document.createElement("input");
  input.type = "number";
  input.min = "0";
  input.max = "120";
  input.value = age;
  input.placeholder = "Age";
  input.className = "input-guest";

  const unit = document.createElement("span");
  unit.textContent = "years";
  unit.className = "px-2 text-sm text-gondwana-dark";

  const removeBtn = document.createElement("button");
  removeBtn.type = "button";
  removeBtn.textContent = "−";
  removeBtn.className = "btn-secondary px-3 py-2 font-bold";
  removeBtn.addEventListener("click", () => {
    if (guestCount > 1) {
      wrapper.remove();
      decrementGuests();
      updateGuestLabel(refs);
    }
  });

  row.appendChild(input);
  row.appendChild(unit);
  row.appendChild(removeBtn);
  wrapper.appendChild(row);

  refs.guestContainer.appendChild(wrapper);

  incrementGuests();
  updateGuestLabel(refs);
}

export function wireAddBtn(refs) {
  refs.addGuestBtn.addEventListener("click", () => addGuestInput(refs));
}

export function validateGuests(refs) {
  const wrappers = refs.guestContainer.querySelectorAll("[data-guest-row]");
  const ages = [];
  let hasError = false;

  wrappers.forEach((wrapper) => {
    const input = wrapper.querySelector("input[type='number']");
    const existingError = wrapper.querySelector(".error-text");
    if (existingError) existingError.remove();

    input.classList.remove("input-error");

    if (!input.value.trim()) {
      input.classList.add("input-error");
      const errorMsg = document.createElement("div");
      errorMsg.className = "error-text";
      errorMsg.textContent = "Please enter an age.";
      wrapper.appendChild(errorMsg);
      hasError = true;
    } else {
      const age = parseInt(input.value, 10);
      if (Number.isNaN(age) || age < 0) {
        input.classList.add("input-error");
        const errorMsg = document.createElement("div");
        errorMsg.className = "error-text";
        errorMsg.textContent = "Age must be a valid positive number.";
        wrapper.appendChild(errorMsg);
        hasError = true;
      } else {
        ages.push(age);
      }
    }
  });

  if (hasError) {
    throw new Error("Please fix the highlighted guest ages.");
  }

  return ages;
}