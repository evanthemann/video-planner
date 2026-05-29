# Video Planner — Build Plan

A self-hosted PHP app for capturing and organizing YouTube video ideas across two channels.

---

## Environment

- Apache DocumentRoot is `/hub` → `http://localhost/`
- Project lives at `~/hub/video-planner/` → `http://localhost/video-planner/`
- A link to it lives in `~/hub/index.php` (or index.html)
- SQLite DB written by Apache (`www-data`), so after creating the `data/` dir run:
  `sudo chown -R www-data:www-data ~/hub/video-planner/data`
- PHP 8+ with PDO_SQLite (no frameworks)
- W3.CSS from CDN for styling
- Vanilla JS only

---

## Stack

- PHP 8+ (no frameworks)
- W3.CSS — `https://www.w3schools.com/w3css/4/w3.css`
- W3 flat colors — `https://www.w3schools.com/lib/w3-colors-flat.css`
- SQLite3 via PDO
- Vanilla JS

---

## Project Structure

```
video-planner/
├── index.php
├── add.php
├── detail.php
├── delete.php
├── db.php
├── helpers.php
├── data/
│   └── planner.db       # auto-created on first run
└── assets/
    └── app.css
```

---

## Database Schema

### ideas
| column | type | notes |
|---|---|---|
| id | INTEGER PK AUTOINCREMENT | |
| title | TEXT | nullable |
| channel | TEXT NOT NULL | `edit_with_evan`, `evan_mann`, `unassigned` — default `unassigned` |
| status | TEXT NOT NULL | `idea`, `scripted`, `filmed`, `edited`, `published` — default `idea` |
| brain_dump | TEXT | nullable |
| tags | TEXT | comma-separated, nullable |
| reference_urls | TEXT | newline-separated, nullable |
| estimated_length | TEXT | `short`, `long`, `series`, or NULL |
| created_at | TEXT NOT NULL | default `datetime('now')` |
| updated_at | TEXT NOT NULL | default `datetime('now')` |

---

## Pages

### index.php — Dashboard

- Dark grey top bar with app title and per-channel idea counts
- **Quick-add bar** (always visible):
  - Brain dump textarea
  - Channel dropdown
  - Collapsible title field (hidden by default, shown via "+ Add title" link)
  - Submit → POSTs to `add.php` → redirects to `index.php`
  - Validation: brain_dump OR title must be present
- **List view**: rows sorted by `created_at DESC` — title/brain_dump preview | channel badge | status badge | tags | created date — clicking a row opens `detail.php?id=X`

### add.php — POST only
- Validate brain_dump OR title present
- Insert into `ideas`
- Redirect to `index.php`

### detail.php — Idea detail & edit
- Editable: title, channel, status (segmented buttons), brain dump (autogrow textarea), tags, reference URLs, estimated length (segmented buttons)
- Save button — self-posting, updates `updated_at`
- Delete button → W3.CSS modal confirmation → POSTs to `delete.php`
- Shows `created_at` / `updated_at` timestamps (read-only)
- Back link to `index.php`

### delete.php — POST only
- Accepts idea `id`
- Deletes row
- Redirects to `index.php`

---

## Badge Colors

| Channel | Class |
|---|---|
| Edit with Evan | `w3-red` |
| Evan Mann | `w3-indigo` |
| Unassigned | `w3-grey` |

| Status | Class |
|---|---|
| idea | `w3-blue-grey` |
| scripted | `w3-purple` |
| filmed | `w3-orange` |
| edited | `w3-teal` |
| published | `w3-green` |

---

## Security

- PDO prepared statements for all queries
- `htmlspecialchars()` on all output
- POST-only for all write operations
- `display_errors` off; use `error_log()`

---

## Build Order

1. `db.php` + `helpers.php` — connection, schema init, shared functions
2. `index.php` — static shell with quick-add bar
3. `add.php` — wire up quick-add form, confirm ideas save and list
4. `detail.php` — view then make editable
5. `delete.php` + modal confirmation
