export function pluralize(n, one, many) {
  return `${n} ${n === 1 ? one : many}`;
}

export function formatDateRange(arrival, departure) {
  const opts = { day: "numeric", month: "short", year: "numeric" };
  const start = new Date(arrival).toLocaleDateString("en-GB", opts);
  const end = new Date(departure).toLocaleDateString("en-GB", opts);
  return `${start} → ${end}`;
}