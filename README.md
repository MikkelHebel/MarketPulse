# MarketPulse — Market & Sentiment Tracker

## Project Overview
A web-based dashboard built with **Laravel** that tracks S&P 500, Nasdaq, and MAG-7 stock prices correlated with r/wallstreetbets sentiment analysis.

## Tech Stack
- **Framework:** Laravel
- **Database:** PostgreSQL
- **Cache / Sessions:** Redis
- **API Integrations:** Yahoo Finance for stock prices, Reddit API for r/wallstreetbets
- **Frontend:** Chart.js for data visualization

## Functional Requirements
1. The system shall automatically fetch stock prices for S&P 500, Nasdaq, and MAG-7 tickers every minute
2. The system shall automatically fetch hot posts from r/wallstreetbets every minute
3. The system shall persist stock price snapshots to the database with a timestamp
4. The system shall analyse Reddit posts for bullish/bearish sentiment per ticker and store scores
5. The system shall calculate a Hype-Correlation-Index (0–100) per ticker based on mention frequency vs. price action
6. The system shall trigger an alert when a ticker crosses an HCI threshold
7. The system shall display a dashboard with Chart.js visualizations correlating stock prices and sentiment scores
8. The system shall support viewing historical snapshots of price and sentiment data
9. Users shall be able to register and log in to the application
10. Authenticated users shall be able to search for any stock ticker and view a dedicated page with a price chart and sentiment data
11. Users shall be able to toggle individual tickers on/off in the chart with "Select All" and "Deselect All" controls. The filter state persists across polling updates
12. Authenticated users shall be able to configure per-ticker HCI alert thresholds (high and low). Notifications are delivered via frontend polling when thresholds are crossed
13. The dashboard shall display a table of the 25 most recently searched stock tickers (across all users) at the bottom of the page, linking back to their search result

## Design Patterns
**Strategy Pattern** — `app/Services/Contracts/DataSourceInterface.php`
Four concrete implementations (`StockStrategy`, `SingleStockStrategy`, `RedditStrategy`, `RedditScraperStrategy`) share a common interface and are registered via dependency injection in `MarketServiceProvider`. Callers depend only on the interface.

**Observer Pattern** — `app/Observers/SnapshotObserver.php`
Registered in `MarketServiceProvider`, it fires on every `Snapshot::created` Eloquent event. Calculates the HCI, checks user thresholds, and sends `HciAlertNotification` on state transitions only.

**Dependency Injection** — `app/Providers/MarketServiceProvider.php`
All services and strategies are bound in the service container and injected into controllers and commands via constructor injection, avoiding direct instantiation and keeping classes decoupled.

## How to Run
### Prerequisites
Docker and Docker Compose must be installed.

### Start the application
```bash
docker compose up -d
```

The entrypoint automatically installs dependencies, builds assets, and runs migrations on startup. The app is available at **http://localhost:8080**.
Add `--build` to rebuild the image after Dockerfile changes.

### Run tests
```bash
cd MarketPulse && php artisan test
```

## Known Issues
The three auth flow tests in `tests/Feature/AuthTest.php` fail with HTTP 419 when run inside Docker (`docker compose exec laravel php artisan test`) due to Docker environment variables overriding the test session configuration. All 30 tests pass when run locally with `php artisan test` from the `MarketPulse/` directory.

Performance testing is not implemented. The test suite covers correctness (unit, integration, and pattern verification) but does not measure throughput, latency under load, or scheduler reliability under concurrent requests.

## Hype-Correlation-Index (HCI)

The HCI is the individual feature of this project. It produces a score between 0 and 100 per ticker by combining two signals pulled from the database:

**Sentiment momentum** — the latest sentiment score for the ticker from `sentiment_scores` (already 0–100, where 50 is neutral, >50 is bullish, <50 is bearish).

**Price momentum** — derived from the last two price snapshots in `snapshots`:
```
% change = (current_price - previous_price) / previous_price * 100
price_momentum = min(100, max(0, 50 + (% change × 5)))
```

A price change of 0% maps to 50. A +10% move maps to 100, a −10% move maps to 0. Moves beyond ±10% are clamped to the extremes.

**Final score:**
```
HCI = (sentiment_momentum + price_momentum) / 2
```

The HCI is calculated per ticker after each data fetch and passed to the Observer, which triggers an alert if the score exceeds 75 (hype peak) or falls below 25 (panic/crash signal).

## Architecture Decisions
### Why polling endpoints are in `web.php` and not `api.php`
Laravel's `api.php` is stateless by default and expects token-based authentication (e.g. Sanctum or Passport). This project uses session-based authentication built on the `web` middleware stack (`Auth::attempt()`, cookies, CSRF). Placing the polling endpoints (`/chart/data`, `/notifications/poll`) in `web.php` means they automatically inherit session auth and the `auth` middleware — no extra token setup required. If a separate mobile app or SPA with token auth were added in the future, those routes would move to `api.php`.

### Reddit scraper fallback
`RedditStrategy` uses the OAuth API (requires credentials). If the API call fails (e.g. credentials not configured), `FetchData` catches the exception and falls back to `RedditScraperStrategy`, which hits the public JSON endpoint at `reddit.com` with a browser-like User-Agent. This keeps data flowing during development before API credentials are approved.

### Dashboard chart: % change, not absolute price
The dashboard chart normalises all tickers to a common baseline so tickers with very different price ranges (e.g. S&P 500 at ~7,500 vs NVDA at ~220) can be compared on the same axis. Each line starts at 0 % and shows relative movement over approximately the last 60 minutes.

### Why sentiment shows `--` for some tickers
`SentimentAnalyzer` only scans Reddit post titles for the seven MAG-7 stock symbols (`AAPL`, `MSFT`, `GOOGL`, `NVDA`, `AMZN`, `META`, `TSLA`). The two index tickers (`^GSPC`, `^IXIC`) are index symbols that Reddit users never reference by that name, so no sentiment score is ever written for them and they always display `--`. For MAG-7 tickers, a score is only persisted when at least one post in a given minute's batch mentions the ticker in its title; during quiet periods no record is written and `--` is shown until the next matching post appears.

## Testing
The test suite is organized into four categories:

**Pure unit test — `tests/Unit/SentimentAnalyzerTest.php`**
Tests `SentimentAnalyzer` in complete isolation: no database, no framework, no HTTP. The analyzer is a pure PHP class, so the tests instantiate it directly and assert on the return value. Covers neutral sentiment, bullish/bearish extremes, balanced scores, unknown tickers, and case-insensitivity.

**Integration test with DB — `tests/Feature/HypeCorrelationTest.php`**
Tests `HypeCorrelationService` against a real database. Verifies the HCI formula produces the correct scores, that it falls back to the sentiment score when fewer than two snapshots exist, and that it caps correctly at both 0 and 100.

**Design pattern verification — `tests/Feature/SnapshotObserverTest.php`**
Verifies the Observer pattern implementation. After seeding thresholds and a sentiment score, creating a `Snapshot` triggers the `SnapshotObserver`. The tests assert that `HciAlertNotification` is sent to the correct user depending on whether the HCI crosses the configured high/low threshold.

**Auth and route protection — `tests/Feature/AuthTest.php` + `tests/Feature/RouteTest.php`**
Verifies the authentication flow (register, login, logout) and that all routes work as intended — unauthenticated users are redirected to `/login` while logged-in users can access protected pages.

## Data Polling
| Source | Frequency |
|---|---|
| Stock prices + Reddit posts (backend) | Every 1 minute |
| Dashboard chart | Every 60 seconds |
| Notification toasts | Every 15 seconds |
| Search page chart | Every 5 seconds |
