## O'Chorri — Browser RTS Engine

> A throwback multiplayer hex conquest saga built with nothing but raw PHP, MySQL, and stubbornness.

O'Chorri is a persistent-strategy sandbox where players spin up a civilization, carve out sectors on a living hex map, and fight for supremacy with asynchronous battles, tech research, and message-driven diplomacy. The repo captures the entire stack—SQL schema, PHP controllers, AJAX endpoints, front-end widgets, cron workers and admin scripts to reset the world.

---

## Why You'll Love (Or Fear) This Codebase

- **Hex-centric world map** — `public/views/map/mapView.php` + `public/js/map.js` render draggable, zoomable hexes populated via `map_request.php`, so every click talks to live sector data.

- **Persistent economy** — Resource balances tick every refresh inside `map_request.php`, pulling player state from cached `StaticData`.

- **War room detail boxes** — AJAX controllers inside `public/controllers/detailBox/` stream building queues, troop movements, productions, and battle logs.

- **Realtime(ish) automation** — `public/controllers/batch/cronned.php` plus the bash loop in `public/batch/script.sh` chew through queued builds, tech, training, and fights.

- **All-Spanish UI** — Views (e.g. `public/views/common/top.php`) and term tables keep the experience localized.

- **No frameworks** — Just ADODB (`public/models/DAO/adodb5`) and a home-grown MVC-ish structure you can bend to your will.

---

## Stack + Tooling

| Layer        | Details |
|--------------|---------|
| Language     | PHP 5.x style (short tags, `var` properties, manual constructors). Enable `short_open_tag=On`. |
| Front-end    | jQuery 1.x, custom JS (`public/js/*`), DataTables 1.8, jsTree for tech/resource displays, vanilla CSS/PHP-based styles. |
| Persistence  | MySQL via ADODB; connection tuned in `public/models/DAO/config.php`. |
| Assets       | Static sprites, icons, avatars, and flags in `public/img`. |
| Background   | Cron-style loop executing `public/controllers/batch/cronned.php`. |

---

## High-Level Flow

1. **Login**  
   `public/controllers/index/indexController.php` authenticates users, loads `StaticData` (`controllers/StaticData/initStaticData.php`), hydrates the `Player` object (`getSessionPlayer.php`), and drops them into `main/mainController.php`.

2. **Main HUD**  
   `mainController.php` bootstraps shared chrome (`views/common/*`), then renders the map module inside `#main_container`.

3. **Dynamic Map**  
   - `map.js` drives zoom/move, calling `controllers/map/map_request.php`.  
   - The controller rebuilds sector objects, recalculates resource balances, persists last map view, and spits out HTML via `views/map/mapView.php`.

4. **Detail Boxes & Actions**  
   Context popups (buildings, units, movement, battles, production) live under `controllers/detailBox/` and `views/detailBox/`. Each endpoint returns markup plus metadata (progress bars, timers) for JS to animate.

5. **Other Modules**  
   - **Technologies** — `controllers/technologies/` orchestrate research queues and planned percentages.  
   - **Messages** — `controllers/messages/` + `views/message/messageView.php` handle inbox, compose, and deletes.  
   - **Ranking** — `controllers/ranking/ranking_request.php` aggregates points across sectors, divisions, and builds.

6. **Automation Loop**  
   `controllers/batch/cronned.php` inspects the `Batch` table and delegates to specialized updaters (buildings, division movements, battles, tech links, training queues). `public/batch/script.sh` is the suggested forever-loop runner.

---

## Repo Tour

| Path | Purpose |
|------|---------|
| `public/controllers/` | Feature-specific controllers (map, detail boxes, messages, ranking, register, technologies, batch tasks). |
| `public/models/` | Domain classes (Player, Sector, Unit, etc.) each paired with a DAO for ADODB queries. |
| `public/views/` | PHP-based views + CSS (most text is Spanish). |
| `public/js/` | Core gameplay JS (`map.js`, `technologies.js`, `messages.js`, etc.) plus vendor bundles (DataTables, jsTree). |
| `public/config/` | Gameplay tuning knobs (`map.cfg.php`, `battle.cfg.php`, `buildings.cfg.php`, resource paths). |
| `public/lib/` | Autoloader (`inclusion.php`), time helpers, array utils. |
| `public/batch/` | Cron harness + logs. |
| `private/` | Admin-only controllers (world resets, seed generators, global scripts). |
| `docs/sql/` | Schema snapshots (`Tablas4.1.sql`, `Tablas4.1NOFOREIGNS.sql`). |

---

## Data & Static Cache Strategy

- `controllers/StaticData/initStaticData.php` builds singleton caches for terms, units, buildings, techs, resources, battle modifiers, etc., so subsequent requests only hydrate deltas.
- `getSessionPlayer.php` clones the logged-in player, stitches available units/buildings/techs based on age, and stores everything in `$_SESSION`.
- Configs (`config/map.cfg.php`, `config/sector.cfg.php`) derive map bounds from `SectorDAO` so zoom limits always respect DB state.

---

## Background Jobs & Game Rules

- **Battle resolution** — `config/battle.cfg.php` defines impact probabilities per class; `update_battle.php` consumes them when cron fires.
- **Construction & Training** — `update_building.php`, `update_trainingQueue.php`, and `update_divisionMovement.php` mutate sectors, adjust maintenance costs, and fire player messages.
- **Research** — `update_technologyLink.php` handles tech completion, including age transitions and incremental resource multipliers.
- **Global resets** — `private/controllers/global/start_new_game.php` truncates non-static tables, grows the map (`seed_map_generator.php`), reassigns capitols, and rewrites each player's `lastMapView`.

Run `public/batch/script.sh` (or wire its logic into a real cron/systemd unit) to keep the universe alive.

---

## Database & Seeds

1. Create an empty MySQL schema (default name: `ochorri` per `public/models/DAO/config.php`).
2. Import `docs/sql/Tablas4.1.sql` for full schema + foreign keys.  
   Need to bypass FK constraints temporarily? Use the `NOFOREIGNS` variant.
3. Optional: run `private/controllers/global/start_new_game.php` from a browser/CLI to procedurally fill the map and hand out starter sectors.

---

## Getting Started

### Requirements
- PHP 7.4+ with `short_open_tag` enabled (code uses `<?` everywhere).
- MySQL 5.7+ (or compatible).
- Web server pointing to the repo root (Apache + mod_php, Nginx + PHP-FPM, or `php -S 0.0.0.0:8000 -t public` for quick dev).
- CLI access for cron/worker scripts.

### Installation
1. Clone the repo and install dependencies (there are no Composer/NPM steps).
2. Copy `public/models/DAO/config.php` and adjust `$host`, `$database`, `$user`, `$password`.
3. Import the SQL schema (`mysql -u root -p ochorri < docs/sql/Tablas4.1.sql`).
4. Ensure your vhost/serve command points at the `public/` directory (root `index.php` just redirects there).
5. Make `public/img/*` writable if you want user-uploaded avatars/flags.

### Run the Game
- Start PHP: `php -S 127.0.0.1:8080 -t public` (remember to enable short tags via `php -d short_open_tag=1 ...`).
- Visit `/controllers/index/indexController.php` to log in or `/controllers/register/registerController.php` to create a civ.

### Keep the Universe Ticking
- Kick off the worker: `bash public/batch/script.sh` (edit the `HOME` path inside if you’re not on `/opt/lampp/...`).  
- Alternatively, wire `php public/controllers/batch/cronned.php` into cron every minute.

---

## Front-End Workflow

- JS entrypoints live in `public/js/`. Keep behavior modular—each subsystem (map, technologies, messages, ranking, detail boxes) has its own file.
- CSS often ships as `.css.php` so PHP can inject image paths; see `views/map/map.css.php` or `views/common/top.css`.
- When adding UI, pair a controller (under `public/controllers/*`) with a view partial (`public/views/*`) and, if needed, a JS helper.

---

## Useful Admin Scripts

- `private/controllers/global/start_new_game.php` — Wipes progress, regenerates the map, gives every player fresh territory.
- `private/controllers/global/seed_map_generator.php` — Extends the map grid using procedural names defined in `config/sector.cfg.php`.
- `public/controllers/register/set_initial_sector.php` — Assigns spawn coordinates for new players (also re-used during resets).

---

## Testing & Debugging

- Manual test harnesses live in `public/tests/blacloud/` (API probes, HTML fixtures, experimental scripts).
- The map/detail endpoints are easiest to poke with plain POSTs (e.g. curl against `controllers/detailBox/buildingsDetails_request.php` with `coordinateX/Y`).
- Enable `$debug = true` inside `public/models/DAO/config.php` to get ADODB query traces.

---

## Roadmap Ideas

- Modernize to PHP namespaces/composer autoloading to drop `require_once_model`.
- Replace the bash infinite loop with a queue worker (Symfony Console, Laravel Octane, etc.).
- Localize strings via term tables + JSON exports so English/other languages can join the fight.
- Port front-end to a modern framework (Vue/React) while reusing the existing endpoints.

---

Suit up, set your cron, and start conquering sectors. O'Chorri doesn’t sleep—and now neither will you.
