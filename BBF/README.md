# Brigade Brotherhood Fellowship (BBF) Website

> **One Brotherhood. One Purpose. One Nation.**

A premium national-organization-grade website for the Brigade Brotherhood Fellowship — a national association of brigade members across all 36 states and the FCT of Nigeria.

---

## Tech Stack

| Layer      | Technology                              |
|------------|-----------------------------------------|
| Markup     | HTML5 (semantic, accessible)            |
| Styling    | CSS3 (custom properties), Bootstrap 5   |
| Scripting  | Vanilla JavaScript (ES6+)               |
| Icons      | Font Awesome 6                          |
| Fonts      | Google Fonts (Playfair Display, Inter, Oswald) |
| Server     | Static — runs on XAMPP / VS Code Live Server |

No frameworks, no build tools, no backend required to run.

---

## Project Structure

```
BBF/
├── index.html              # Homepage (flagship page)
├── about.html              # About BBF
├── leadership.html         # National leadership profiles
├── chapters.html           # States & chapters (36 states + FCT)
├── events.html             # Events with category filtering
├── news.html               # News & announcements
├── gallery.html            # Photo gallery with lightbox
├── activities.html         # Activities & projects
├── resources.html          # Downloads & resources
├── membership.html         # Membership registration form
├── contact.html            # Contact page with map
├── login.html              # Member login (simulated)
├── dashboard.html          # Member dashboard (mock)
├── admin.html              # Admin dashboard (mock)
│
├── assets/
│   ├── css/
│   │   ├── style.css       # Master stylesheet + design system
│   │   ├── responsive.css  # All breakpoint overrides
│   │   └── dashboard.css   # Dashboard-specific styles (Phase N)
│   │
│   ├── js/
│   │   ├── data.js         # ★ ALL mock data (single source of truth)
│   │   ├── main.js         # Homepage logic + shared site utilities
│   │   ├── events.js       # Events page logic (Phase N)
│   │   ├── gallery.js      # Gallery lightbox logic (Phase N)
│   │   ├── membership.js   # Registration form validation (Phase N)
│   │   └── dashboard.js    # Dashboard rendering (Phase N)
│   │
│   └── images/             # Place all images here
│       ├── hero-bg.jpg     # Hero background (1920×1080 recommended)
│       ├── about-preview.jpg
│       ├── placeholder-leader.jpg
│       ├── placeholder-event.jpg
│       ├── placeholder-news.jpg
│       ├── placeholder-gallery.jpg
│       └── placeholder-avatar.jpg
│
└── README.md
```

---

## Running the Project

### Option 1 — XAMPP (recommended)
1. Place the `BBF/` folder inside `C:\xampp\htdocs\`
2. Start Apache in XAMPP Control Panel
3. Visit `http://localhost/BBF/`

### Option 2 — VS Code Live Server
1. Open the `BBF/` folder in VS Code
2. Right-click `index.html` → **Open with Live Server**

### Option 3 — Any static file server
```bash
# Python
python -m http.server 8080

# Node (npx)
npx serve .
```

---

## Design System

All design tokens are in `assets/css/style.css` under `:root {}`.

| Token                | Value              | Usage                     |
|----------------------|--------------------|---------------------------|
| `--bbf-navy`         | `#0A1628`          | Primary background        |
| `--bbf-gold`         | `#C9A84C`          | Accent, CTAs, icons       |
| `--bbf-red`          | `#B22234`          | Sparingly — alerts        |
| `--font-heading`     | Playfair Display   | All headings              |
| `--font-body`        | Inter              | Body text                 |
| `--font-accent`      | Oswald             | Labels, tags, nav         |

---

## Data Layer

**`assets/js/data.js`** is the single source of truth for all content:

| Variable                | Contents                              |
|-------------------------|---------------------------------------|
| `BBF_INFO`              | Organization contact & social links   |
| `BBF_STATS`             | Counter figures (states, chapters…)   |
| `BBF_VALUES`            | 7 core values                         |
| `BBF_BENEFITS`          | 8 membership benefits                 |
| `BBF_LEADERS`           | National leadership profiles          |
| `BBF_STATES`            | All 36 states + FCT chapter data      |
| `BBF_EVENTS`            | Event listings                        |
| `BBF_NEWS`              | News articles                         |
| `BBF_GALLERY`           | Gallery items                         |
| `BBF_ACTIVITIES`        | Activities & projects                 |
| `BBF_MEMBERSHIP_TYPES`  | Membership categories & pricing       |
| `BBF_ORGANIZATIONS`     | Brigade organization list             |
| `NIGERIA_STATES`        | State list (for forms)                |
| `BBF_MOCK_MEMBER`       | Sample member profile (dashboard)     |

All render functions in `main.js` consume these globals.
Every data array is marked with a `// TODO [BACKEND]` comment showing the exact API endpoint to replace it with.

---

## Backend Integration Guide

When a backend is ready:

1. **Replace data in `data.js`** — each array has a `// TODO [BACKEND]: Replace with GET /api/...` comment.
2. **Render functions stay the same** — they accept the same data shape.
3. **Forms** — each form has a `// TODO [BACKEND]` comment with the POST endpoint and expected payload.
4. **Auth** — `login.html` simulates login. Replace the JS handler with a real POST to `/api/auth/login` and set a session/JWT.
5. **Dashboard** — all mock data in `BBF_MOCK_MEMBER`; replace with authenticated GET calls.

---

## Images Needed

To make the site look complete, add these images to `assets/images/`:

| File                    | Recommended Size  | Content                          |
|-------------------------|-------------------|----------------------------------|
| `hero-bg.jpg`           | 1920 × 1080       | Brigade parade / group photo     |
| `about-preview.jpg`     | 900 × 700         | BBF members in fellowship        |
| `placeholder-leader.jpg`| 400 × 500         | Professional headshot style      |
| `placeholder-event.jpg` | 800 × 500         | Event/conference photo           |
| `placeholder-news.jpg`  | 800 × 500         | Newsroom / announcement image    |
| `placeholder-gallery.jpg`| 600 × 600        | Any BBF activity photo           |
| `placeholder-avatar.jpg`| 200 × 200         | Default member avatar            |

---

## Build Phases

| Phase | Pages / Features                                   | Status        |
|-------|----------------------------------------------------|---------------|
| 1     | Setup, design system, Homepage                     | ✅ Complete   |
| 2     | About, Leadership                                  | Pending       |
| 3     | States & Chapters, Events                          | Pending       |
| 4     | News, Gallery                                      | Pending       |
| 5     | Activities, Resources, Contact                     | Pending       |
| 6     | Membership registration form                       | Pending       |
| 7     | Login, Member Dashboard, Admin Dashboard           | Pending       |

---

## Accessibility

- Semantic HTML5 throughout (`<nav>`, `<section>`, `<article>`, `<aside>`, `<footer>`)
- All images have `alt` attributes
- ARIA labels on interactive elements, dialogs, and navigation regions
- Keyboard navigable (offcanvas closes on Escape, focus management)
- `prefers-reduced-motion` respected in CSS
- Focus-visible styles defined
- Color contrast follows WCAG AA guidelines for primary text

> Full WCAG compliance requires manual testing with assistive technologies.

---

*Brigade Brotherhood Fellowship © 2026 — Built for real impact, not demo purposes.*
