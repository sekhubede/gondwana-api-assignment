export const API_BASE = window.location.hostname.includes("github.dev")
  ? `https://${window.location.hostname.replace("-5500", "-8080")}`
  : "http://localhost:8080";