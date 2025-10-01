# Gondwana API Assignment

This repository contains my submission for the Gondwana Collection coding challenge.

The task: build a **RESTful API in PHP** and a **simple frontend** to query and display unit rates.

---

## Project Structure
- **backend/**
  - **public/** → Web entrypoint (`index.php`)
  - **src/** → Application source code (controllers, services)
  - **tests/** → PHPUnit test suite
- **client/** → Frontend (HTML, CSS, JS)
- **.github/workflows/** → CI pipeline with SonarCloud
- **.devcontainer/** → GitHub Codespaces setup

---

## Tech Stack
- PHP 8.2+
- [Slim Framework 4](https://www.slimframework.com/)
- [Slim PSR-7](https://github.com/slimphp/Slim-Psr7)
- [GuzzleHTTP](https://github.com/guzzle/guzzle)
- PHPUnit 11 (tests + coverage)
- Vanilla JS frontend with TailwindCSS
- GitHub Actions + SonarCloud for CI/CD & code quality
- Devcontainer + Codespaces for reproducible setup

---

## Running the Backend

### In Codespaces
1. Open this repo in GitHub Codespaces.  
2. Install dependencies:
   ```bash
   composer install
   ```
3. Start the backend:
   ```bash
   composer serve
   ```
   This runs Slim at http://localhost:8080 with `backend/public` as the document root.  
4. Open the provided Codespaces URL, for example (optional):
   ```
   https://<your-codespace-id>-8080.app.github.dev/
   ```

---

## Running the Frontend

### In Codespaces
1. Navigate to the `client/` folder:
   ```bash
   cd client
   npm install
   ```
2. Start the frontend:
   ```bash
   npm start
   ```
   This runs **live-server** on port 5500 and serves `index.html`.

3. **Expose Backend API (manual step)**  
   - Go to the **PORTS tab** in Codespaces  
   - Find port **8080**  
   - Right-click → **Make Public**  

4. Open the frontend in your browser (if step 2 did not open it automatically):  
   ```
   https://<your-codespace-id>-5500.app.github.dev/
   ```

### Gotchas
- **Empty Guest Ages:** The form will not submit if you add a guest without entering an age.  
- **Ports Reset:** If you restart the Codespace, ensure port `8080` is still set to **Public**.  
- **First Run:** On new Codespaces, the UI may open from the repo root — manually navigate into `/client` if needed.  
- **Date Validation:** The UI prevents selecting past dates or a departure date earlier than the arrival date.  

---

## Available Backend Routes

- **GET /** → Health check  
  Returns:
  ```json
  { "status": "ok", "message": "API is running" }
  ```

- **POST /rates** → Calculate rates  
  Accepts JSON payload with fields like Unit Type ID, Arrival, Departure, Ages.

---

## Testing

```bash
composer test
```

All new code is covered by unit tests.  
SonarQube gates: ✅ 100% coverage, maintainability checks passed.