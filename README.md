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
   ```
3. Once running, Codespaces will forward port 8080.
   Open the provided URL, e.g.:
   ```cpp
   https://<your-codespace-id>-8080.app.github.dev/
   ```
4. Test the Hello World route:
   ```cpp
   https://<your-codespace-id>-8080.app.github.dev/
   ```
   -> should return **Hello, world!**

---
## Current Features
- Basic Slim 4 backend with Hello World endpoint

## Upcoming Features
- Accepts payload with Unit Name, Arrival, Departure, Occupants, Ages
- Transforms payload into expected format
- Calls Gondwana API and returns rates
- Simple UI to send requests and display rates
