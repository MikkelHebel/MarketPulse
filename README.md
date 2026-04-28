# Project Plan: MarketPulse - Market & Sentiment tracker (Portfolio Exam)

## Project Overview
A web-based dashboard for built with **Laravel** that tracks financial market data combined with r/wallstreetbets.

## Tech Stack
- **Framework:** Laravel
- **Database:** Postgres
- **API Integrations:** Yahoo Finance for stock prices, Reddit API for r/wallstreetbets
- **Frontend:** Chart.js for data visualization

## Design Patterns
To meet the exam requirements, the following pattern will be implemented:
1. **Strategy Pattern:** - `DataSourceInterface` to handle different API integrations
  - Enables easy expansion if more APIs (e.g. X/Twitter) are added later
2. **Observer Pattern:** - `MarketObserver` that triggers events when a hits a specific metric (Volatility threshold or sentiment peaks)
3. **Dependency Injection Pattern:** - `StockStrategy` & `RedditStrategy` both uses Dependency Injection. To create greater decoupling and get inversion of control and greater seperation of concerns.

## Functional Requirements

1. The system shall automatically fetch stock prices for S&P 500, Nasdaq, and MAG-7 tickers every minute
2. The system shall automatically fetch hot posts from r/wallstreetbets every minute
3. The system shall persist stock price snapshots to the database with a timestamp
4. The system shall analyse Reddit posts for bullish/bearish sentiment per ticker and store scores
5. The system shall calculate a Hype-Correlation-Index (0–100) per ticker based on mention frequency vs. price action
6. The system shall trigger an alert when a ticker crosses a volatility or sentiment threshold
7. The system shall display a dashboard with Chart.js visualizations correlating stock prices and sentiment scores
8. The system shall support viewing historical snapshots of price and sentiment data
9. Users shall be able to register and log in to the application
10. Authenticated users shall be able to search for a specific stock ticker and view its dedicated data page

## Development Phases

### Phase 1: Core setup & infrastructure
- [x] Initialize Laravel project
- [x] Setup database migrations for `snapshots`, `tickers`, and `sentiment_scores`
- [x] Configure `Guzzle` for API communication
- [x] Setup Laravel scheduler to automate data fetching

### Phase 2: Data Acquisition
- [x] Implement `StockStrategy` (Fetching S&P 500, Nasdaq, MAG-7 stocks)
- [x] Implement `RedditStrategy` (Fetching r/wallstreetbets hot posts)
- [x] Develop a simple Sentiment Analyzer service (Keyword matching: "bullish", "bearish", etc.)
- [x] Persist stock snapshots and sentiment scores to the database

### Phase 3: Business Logic
- [x] **Individual Feature:** Develop the **Hype-Correlation-Index**
  - An algo that calculates a score (0–100) based on mention frequency vs. price action
- [ ] Implement the Observer pattern for alerts when a ticker crosses a volatility or sentiment threshold

### Phase 4: Authentication
- [x] Implement user registration and login

### Phase 5: Frontend & Search
- [ ] Build a dashboard overview using Blade components
- [ ] Integrate **Chart.js** to visualize the correlation between Reddit mentions and stock price movement
- [ ] Ensure the UI supports viewing historical snapshots
- [ ] Implement ticker search page for authenticated users

### Phase 6: Testing
- [ ] Write feature tests for data fetching and persistence
- [ ] Write unit tests for `SentimentAnalyzer` and `Hype-Correlation-Index`

### Phase 7: Finalization
- [ ] Create Class Diagrams for Strategy and Observer patterns
- [ ] Create Sequence Diagrams for the data-fetching cron job
- [ ] Create deployment diagram for the system
- [ ] Finalize the README for the portfolio submission

## Hype-Correlation-Index (HCI)

The HCI is the individual feature of this project. It produces a score between 0 and 100 per ticker by combining two signals pulled from the database:
**Sentiment momentum** — the latest sentiment score for the ticker from `sentiment_scores` (already 0–100, where 50 is neutral, >50 is bullish, <50 is bearish).
**Price momentum** — derived from the last two price snapshots in `snapshots`:

```
% change     = (current_price - previous_price) / previous_price * 100
price_momentum = clamp(50 + (% change × 5), 0, 100)
```

A price change of 0% maps to 50. A +10% move maps to 100, a −10% move maps to 0. Moves beyond ±10% are clamped to the extremes.

**Final score:**
```
HCI = (sentiment_momentum + price_momentum) / 2
```

The HCI is calculated per ticker after each data fetch and passed to the Observer, which triggers an alert if the score exceeds 75 (hype peak) or falls below 25 (panic/crash signal).

## Snapshot frequency
- **Stocks & WSB:** Every minute
