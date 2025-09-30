export function validateDates(refs) {
  const arrivalInput = document.getElementById("arrival");
  const departureInput = document.getElementById("departure");

  [arrivalInput, departureInput].forEach((input) => {
    input.classList.remove("input-error");
    const existingError = input.parentElement.querySelector(".error-text");
    if (existingError) existingError.remove();
  });

  const today = new Date();
  today.setHours(0, 0, 0, 0);

  const arrival = new Date(arrivalInput.value);
  const departure = new Date(departureInput.value);

  let hasError = false;

  if (!arrivalInput.value) {
    showError(arrivalInput, "Arrival date is required.");
    hasError = true;
  } else if (arrival < today) {
    showError(arrivalInput, "Arrival date cannot be in the past.");
    hasError = true;
  }

  if (!departureInput.value) {
    showError(departureInput, "Departure date is required.");
    hasError = true;
  } else if (departure <= arrival) {
    showError(departureInput, "Departure must be after arrival.");
    hasError = true;
  }

  if (hasError) {
    throw new Error("Please fix the highlighted dates.");
  }

  return { arrival, departure };
}

function showError(input, message) {
  input.classList.add("input-error");
  const errorMsg = document.createElement("div");
  errorMsg.className = "error-text";
  errorMsg.textContent = message;
  input.parentElement.appendChild(errorMsg);
}