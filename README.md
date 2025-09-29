# Gondwana API Assignment

This repository contains my submission for the Gondwana Collection coding challenge.

The task: build a **RESTful API in PHP** and a **simple frontend** to query and display unit rates.

---

## Project Structure
- **backend/**
  - **public/** → Web entrypoint (`index.php`)
  - **src/** → Application source code (controllers, services)
  - **tests/** → PHPUnit test suite
- **.github/workflows/** → CI pipeline with SonarCloud
- **.devcontainer/** → GitHub Codespaces setup

---

## Tech Stack
- PHP 8.2+
- [Slim Framework 4](https://www.slimframework.com/)
- [Slim PSR-7](https://github.com/slimphp/Slim-Psr7)
- [GuzzleHTTP](https://github.com/guzzle/guzzle)
- PHPUnit 11 (tests + coverage)
- Vanilla JS frontend
- GitHub Actions + SonarCloud for CI/CD & code quality

---

## Running the Backend

### In Codespaces
1. Open this repo in GitHub Codespaces.
2. Start the backend with:
   ```bash
   composer serve
   ```
This runs Slim at http://localhost:8080 with backend/public as the document root.
3. Codespaces will forward port 8080.
Open the provided URL, for example:

```cpp
https://<your-codespace-id>-8080.app.github.dev/
```
## Available Routes

- GET / → Health check
Returns:

```json
{ "status": "ok", "message": "API is running" }
```

- POST /rates → Calculate rates
Accepts JSON payload with fields like Unit Name, Arrival, Departure, Ages.

## Testing

```bash
Run PHPUnit with coverage:

composer test
```

All new code is covered by unit tests.
SonarQube gates: ✅ 100% coverage, maintainability checks passed.

## Frontend

The frontend (Vanilla JS + HTML & CSS) will be added under a frontend/ directory in a separate branch.
It will query the backend /rates endpoint and render results in a simple UI.
