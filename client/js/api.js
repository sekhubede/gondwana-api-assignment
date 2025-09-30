// Detect API base (Codespaces or local)
export const API_BASE = window.location.hostname.includes("github.dev")
  ? `https://${window.location.hostname.replace("-5500", "-8080")}`
  : "http://localhost:8080";

// Generic fetch wrapper
export async function postJson(url, data) {
  const res = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  });
  return res.json();
}