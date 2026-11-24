# NSBM SportsHub — Sports Club Management System

A full-stack web application developed for the **Web and Mobile Application Development** module at **NSBM Green University**. NSBM SportsHub centralizes university sports club operations—enabling administrators to manage clubs, memberships, training schedules, tournaments, match results, facility bookings, and equipment inventory while allowing students to join clubs and track campus sports activities.

---

## 🔗 Project Links

- **Live Hosted Application:** https://nsbmsportshub.infinityfree.io/
- **Video Demonstration (YouTube):** [TBD]
- **GitHub Repository:** https://github.com/DasanayakeTK/NSBM_SportsHub

---

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3 (Custom CSS variables, responsive grid/flexbox, dynamic module backgrounds), Vanilla JavaScript (live form validation, floating toast notifications)
- **Backend:** Procedural PHP (Session-based authentication & role-based access control)
- **Database:** MySQL (Relational schema managed via phpMyAdmin)
- **Hosting & Development:** XAMPP (Local) / InfinityFree (Production)

---

## ✨ Key Features

### Admin Panel
- **Clubs & Memberships:** Create, update, and delete sports clubs; approve or reject student membership requests.
- **Training Schedules:** Schedule, edit, and remove club practice sessions with dates, times, and venues.
- **Tournaments & Fixtures:** Create university tournaments and schedule match fixtures between clubs.
- **Match Results:** Record official match scores and update fixture statuses (`Completed` / `Cancelled`).
- **Facility Bookings:** Review and approve or reject student venue booking requests.
- **Equipment Inventory:** Manage sports gear stock, tracking total and available quantities.

### Student / User Panel
- **Authentication & Profile:** Secure registration and login with real-time JavaScript input validation and personal profile view.
- **Club Discovery:** Browse university sports clubs and submit membership applications.
- **Schedules & Fixtures:** View upcoming training sessions, tournament brackets, and scheduled matches.
- **Match Results:** Track completed match outcomes and scores.
- **Bookings & Equipment:** Submit facility reservation requests and view real-time sports equipment availability.

---

## 👥 Team Members & Contributions

| # | Member Name | Student ID | Assigned Module | Git Branch |
| :--- | :--- | :--- | :--- | :--- |
| 1 | Dasanayake DMTK | 41725 | Authentication, User Profiles & UI Styling | `auth_profiles` |
| 2 | Peiris MRS | 42207 | Sports Clubs Directory | `clubs_directory` |
| 3 | Medagama MGHKVK | 42168 | Club Memberships & Approvals | `club_memberships` |
| 4 | Ajmal MSM | 42138 | Training Schedules | `training_schedules` |
| 5 | Kavindya RGM | 42331 | Tournaments & Match Fixtures | `tournament-fixtures` |
| 6 | Akarsha MPR | 42235 | Match Results & Standings | `match_results` |
| 7 | Dilhani HATM | 42059 | Facility Bookings | `facility_booking` |
| 8 | Dias PHVS | 41971 | Equipment Inventory & Database Schema | `equipment_inventory` |

---

## 📁 Project Structure

```text
NSBM_SportsHub/
├── assets/
│   ├── css/style.css           # Main custom stylesheet and dynamic page backgrounds
│   ├── images/                 # Logos, homepage banner, and module backgrounds
│   ├── js/main.js              # Form validation, toast animations, and loader logic
│   └── videos/loader.mp4       # Page transition loader animation
├── core/
│   ├── auth_check.php          # Session validation and login redirect guard
│   ├── db_connect.php          # Environment-aware MySQL connection (Local & Live)
│   ├── footer.php              # Shared page footer and script loader
│   └── header.php              # Shared navigation bar and dynamic body routing
├── database/
│   └── sportshub_schema.sql    # Relational database schema and seed data
├── modules/
│   ├── auth_profiles/          # Login, registration, logout, and user profile
│   ├── club_memberships/       # Student club applications and admin approvals
│   ├── clubs_directory/        # Club browsing and admin club CRUD
│   ├── equipment_inventory/    # Inventory listing and admin stock management
│   ├── facility_bookings/      # Facility booking requests and admin approvals
│   ├── match_results/          # Completed match results and admin score entry
│   ├── tournaments_fixtures/   # Tournament creation, fixture scheduling, and lists
│   └── training_schedules/     # Training session management and student schedules
└── index.php                   # Landing page and role-based dashboard
```
