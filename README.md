# TIX — IT Request & Ticketing Portal

TIX is a lightweight web-based IT ticketing system built with PHP and MySQL. Users can submit IT requests and bug reports, developers can manage and resolve tickets, and administrators have full master control over the entire system — including user management, system configuration, and ticket oversight.

---

## Features

### For Users
- Register and log in to a personal account
- Submit tickets specifying the affected system, request type, and priority level
- View the full history and current status of your own tickets
- Post comments and follow up on open tickets

### For Developers
- Dashboard with real-time ticket counts by status (Open, In Progress, Resolved, Closed)
- Filter all tickets by system, status, or priority
- Update ticket status and priority, with automatic audit comments logged on status changes
- Manage systems/projects — add, activate, or deactivate them
- View and respond to all tickets across all users

### For Administrators
- Full access to everything developers can do
- Dedicated Admin Dashboard with system-wide stats and user breakdown
- Manage users — view all accounts, change roles (Admin / Developer / User), and delete users
- Protected against accidental lockout: cannot demote or delete the last administrator

### Role System
- The **first registered user** is automatically assigned the **Admin** role
- All subsequent registrations default to the **User** role
- Admins can promote users to Developer or Admin from the Users management page

---

## Tech Stack

| Layer      | Technology                          |
|------------|-------------------------------------|
| Backend    | PHP 7.4+ (PDO, sessions)            |
| Database   | MySQL 8                             |
| Frontend   | Bootstrap 5.3, Bootstrap Icons      |
| Server     | Apache (WAMP / LAMP / XAMPP)        |

---

## Project Structure

```
tix/
├── index.php               # Login & registration page
├── admin_dashboard.php     # Admin dashboard (user stats, system overview, recent tickets)
├── dashboard.php           # Developer dashboard (all tickets + filters + stats)
├── submit_ticket.php       # Ticket submission form
├── view_ticket.php         # Ticket detail, comments, and status updates
├── my_tickets.php          # User's own ticket list
├── manage_systems.php      # Admin/Developer: add/activate/deactivate systems
├── manage_users.php        # Admin only: manage user roles and accounts
├── logout.php              # Session destroy & redirect
├── includes/
│   ├── config.php          # App constants and session initialization
│   ├── db.php              # PDO database connection
│   ├── auth.php            # Auth helpers (isLoggedIn, isAdmin, isDeveloper, homeUrl, etc.)
│   ├── header.php          # Shared HTML header and role-based navigation
│   └── footer.php          # Shared HTML footer and scripts
├── resource/
│   ├── css/index.css       # Custom styles
│   └── php/
│       ├── class/
│       │   ├── User.php            # User auth, registration, and management
│       │   ├── Ticket.php          # Ticket CRUD and stats
│       │   ├── TicketComment.php   # Comments and audit log
│       │   └── SystemProject.php  # System/project management
│       └── funct/funct.php        # Shared helper functions (e, redirect, badges)
└── sql/
    └── schema.sql          # Database schema (includes migration note for existing installs)
```

---

## Database Schema

| Table             | Description                                              |
|-------------------|----------------------------------------------------------|
| `users`           | Registered accounts with role (admin/developer/user)    |
| `systems`         | IT systems or projects that tickets are filed against    |
| `tickets`         | Ticket records with status, priority, and type          |
| `ticket_comments` | Comments and audit log entries per ticket               |

### Ticket Types
`Bug Fix` · `Feature Request` · `Update` · `Support` · `Other`

### Priority Levels
`Low` · `Medium` · `High` · `Critical`

### Ticket Statuses
`Open` · `In Progress` · `Resolved` · `Closed`

---

## Installation

### Requirements
- PHP 7.4 or higher
- MySQL 8.0 or higher
- Apache with `mod_rewrite` enabled (WAMP, XAMPP, LAMP, etc.)

### Steps

1. **Clone the repository** into your web server's document root:
   ```bash
   git clone https://github.com/MFrncM/tix.git
   ```
   Place the `tix/` folder inside `www/` (WAMP) or `htdocs/` (XAMPP).

2. **Create the database** by importing the schema:
   ```bash
   mysql -u root -p < sql/schema.sql
   ```
   Or import `sql/schema.sql` via phpMyAdmin.

3. **Configure the database connection** in `includes/db.php`:
   ```php
   $pdo = new PDO('mysql:host=localhost;dbname=tix_db;charset=utf8mb4', 'root', '');
   ```
   Update the host, username, and password to match your environment.

4. **Configure the base URL** in `includes/config.php`:
   ```php
   define('BASE_URL', '/tix');
   ```
   Change `/tix` if your folder is named differently or served from a subdirectory.

5. **Open the app** in your browser:
   ```
   http://localhost/tix
   ```

6. **Register the first account** — it will automatically receive the Admin role and land on the Admin Dashboard.

### Migrating an Existing Installation

If you already have the database set up from a previous version, run this SQL to add the `admin` role:

```sql
ALTER TABLE users MODIFY COLUMN role ENUM('user', 'developer', 'admin') NOT NULL DEFAULT 'user';
```

Then update any existing user you want as admin directly in the database:

```sql
UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
```

---

## Role Access Summary

| Feature                  | User | Developer | Admin |
|--------------------------|:----:|:---------:|:-----:|
| Submit tickets           | ✓    | ✓         |       |
| View own tickets         | ✓    | ✓         | ✓     |
| View all tickets         |      | ✓         | ✓     |
| Update ticket status     |      | ✓         | ✓     |
| Manage systems           |      | ✓         | ✓     |
| Admin dashboard          |      |           | ✓     |
| Manage users             |      |           | ✓     |
