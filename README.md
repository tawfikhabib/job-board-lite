# 💼 Job Board Lite

A lightweight, developer‑friendly WordPress plugin for managing and displaying job listings — complete with a custom post type, meta fields, front‑end shortcodes, and REST API support.  
Perfect for small businesses, niche job boards, or as a base for custom extensions.

---

## 📦 Features

- **Custom Post Type** `job` with dedicated admin UI.
- **Custom Taxonomy** `job_type` for classifying jobs (Full-time, Part-time, etc.).
- **Custom Meta Fields** for company, location, salary range, employment type, apply URL/email, closing date.
- **Front‑end Shortcodes**:
  - `[job_board]` – Display job listings with optional filters.
  - `[job_submit]` – Front‑end submission form (for logged‑in users).
- **Template Fallback** for single job view if the theme doesn’t have one.
- **REST API Endpoints** for integrating with external systems.
- Lightweight CSS for quick styling, easily overridden.

---

## 🚀 Installation

1. Download or clone this repository into your WordPress `wp-content/plugins/` directory:
   ```bash
   git clone https://github.com/tawfikhabib/job-board-lite.git
   ```
2. Activate **Job Board Lite** from the WordPress **Plugins** admin page.
3. Ensure **pretty permalinks** are enabled in *Settings → Permalinks* for clean URLs and REST endpoints.

---

## 📝 Shortcodes

| Shortcode      | Description                                                | Example Usage        |
| -------------- | ---------------------------------------------------------- | -------------------- |
| `[job_board]`  | Displays published jobs in a grid/list with filters.        | `[job_board]`        |
| `[job_submit]` | Displays a front‑end submission form (logged‑in users only) | `[job_submit]`       |

**Example:**  
```
[job_board type="Full-time" per_page="5"]
```
Shows only Full‑time jobs, 5 per page.

---

## 🌐 REST API

**Namespace:** `jbl/v1`

| Method | Endpoint                  | Description                     |
| ------ | ------------------------- | -------------------------------- |
| GET    | `/jobs`                    | Retrieve all jobs                |
| GET    | `/jobs?type=Full-time`     | Filter jobs by type              |

**Example Request:**
```bash
curl https://example.com/wp-json/jbl/v1/jobs
```

---

## 📂 Folder Structure

```
job-board-lite/
├── job-board-lite.php         # Main plugin bootstrap
├── includes/
│   ├── cpt.php                 # Registers CPT & taxonomy
│   ├── meta.php                # Registers custom meta fields
│   ├── shortcodes.php          # Shortcodes for listing & submit form
│   └── rest.php                # REST API endpoints
├── assets/
│   ├── css/style.css           # Plugin styling
│   └── js/main.js              # Plugin JS (optional)
└── templates/
    └── single-job.php          # Single job template fallback
```

---

## ⚙️ Development

### Code Standards
This plugin follows **WordPress Coding Standards (WPCS)**.  
Run PHPCS to ensure compliance:
```bash
phpcs --standard=WordPress --ignore=vendor ./
```

---

## 📜 License
GPL‑2.0‑or‑later — Free to use, modify, and distribute.

---

## 👨‍💻 Author
**Tawfik Habib**  
Senior Software Engineer – PHP, Laravel, Vue.js, WordPress
