# Gondwana API Assignment

This repository contains my submission for the Gondwana Collection coding challenge.

The task: build a **RESTful API in PHP** and a **simple frontend** to query and display unit rates.

---

## Project Structure
- **backend/** -> PHP Slim-based API
- **frontend/** -> Vanilla JS + HTML & CSS UI
- **.github/workflows/** -> CI pipeline with SonarCloud
- **.devcontainer/** -> Codespaces setup

---

## Tech Stack
- PHP 8.2 + Slim Framework
- Composer for dependency management
- PHPUnit for testing
- Vanilla JS frontend
- GitHub Actions + SonarCloud for code quality

---

## Running in Codespaces
1. Open this repo in GitHub Codespaces.
2. Run:
   ```bash
   php -S 0.0.0.0:8080 -t backend/src
3. Open frontend/index.html to test the UI.

---
## Features
- Accepts payload with Unit Name, Arrival, Departure, Occupants, Ages
- Transforms payload into expected format
- Calls Gondwana API and returns rates
- Simple UI to send requests and display rates
