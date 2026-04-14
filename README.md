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

## Development Phases

### Phase 1: Core setup & infrastructure
- [x] Initialize Laravel project.
- [ ] Setup database migrations for `snapshots`, `tickers`, and `sentiment_scores`
- [ ] Configure `Guzzle` for API communication
- [ ] Setup Laravel scheduler to automate data fetching

### Phase 2: Data Acquisition
- [ ] Implement `StockStrategy` (Fetching S&P 500, Nasdaq, MAG-7 stocks)
- [ ] Implement `RedditStrategy` (Fetching r/wallstreetbets hot posts)
- [ ] Develop a simple Sentiment Analyzer service (Keyword matching: "bullish", "bearish", etc.)

### Phase 3: Business Logic
- [ ] **Individual Feature:** Develop the **Hype-Correlation-Index**.
  - An algo that calculates a score (0-100) based on mention frequency vs. price action
- [ ] Implement the Observer pattern for price alerts

### Phase 4:
- [ ] Build a dashboard overview using Blade components.
- [ ] Integrate **Chart.js** to visualize the correlation between Reddit mentions and stock price movement
- [ ] Ensure the UI supports viewing historical snapshots

### Phase 5:
- [ ] Create Class Diagrams for Strategy and Observer patterns
- [ ] Create Sequence Diagrams for the data-fetching cron job
- [ ] Create deployment diagram for the system
- [ ] Finialize the README for the portoflio submission

## Snapshot frequency
- **Stocks & WSB:** Every minute
