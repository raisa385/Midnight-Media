# Midnight Media: ISP Media Content FTP Server

Midnight Media is an FTP-style server application built for an Internet Service Provider (ISP) to host and manage various media content (Movies, Software, TV Series, Games, etc. — simulated). The system implements **role-based access control** and **content management features**, following basic web security principles and an **MVC architecture**.

---

## Features

### Role-Based Access

| Role | Permissions |
|------|-------------|
| **Admin** | Full control — manage moderators, upload/delete content, view all requests |
| **Moderator** | Manage media content, view and respond to content requests |
| **Member (Unregistered)** | Browse, search, filter, and download content; submit content requests |

### Core Functionality

- **User Authentication** — Secure registration and login for admins and moderators, with hashed passwords and session management
- **Content Management** — Upload, edit, delete, and browse media files by category and sub-category
- **Content Requests** — Members can request new content; admins and moderators can view and update request statuses
- **AJAX & Responsive UI** — Dynamic content updates and a user-friendly interface
- **Security** — SQL injection prevention, XSS protection, CSRF awareness, and secure file handling

---

## Technical Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP (MVC architecture) |
| **Database** | Shared schema (`users`, `categories`, `contents`, `content_requests`) |
| **Frontend** | HTML, CSS, JavaScript (with AJAX) |
| **File Storage** | Dummy text files — stored in `public/uploads/contents/` |

---

## Database Schema

### `users`
`id`, `name`, `email`, `password_hash`, `role`, `profile_picture`, `created_at`

### `categories`
`id`, `name`, `parent_id`, `created_at`

### `contents`
`id`, `title`, `description`, `file_path`, `category_id`, `uploader_id`, `download_count`, `uploaded_at`

### `content_requests`
`id`, `requester_ip/session_id`, `content_title`, `category_requested`, `message`, `status`, `created_at`

---

## Project Structure
├── controllers/     # Request handling and business logic
├── models/          # Database interaction
├── views/           # HTML templates
├── config/          # Configuration files
└── public/
└── uploads/
└── contents/  # Uploaded media files

---

## Git Workflow

- **Main branch** — Protected, no direct pushes
- **Feature branches** — Each student works on a feature branch (e.g., `feature/task1-2353251-3`)
- **Pull Requests** — Merge feature branches into `main` via PRs

---

## Team & Task Breakdown

| Student ID | Task | Main Features |
|------------|------|----------------|
| `23-53251-31` | User Authentication | Authentication, Profile, Home, Category Navigation |
| `23-54082-32` | Admin | Manage Moderators & Contents |
| `23-54089-33` | Moderator | Manage Contents, View Requests |
| `23-54716-34` | Member | Browse, Search, Request Content |

---

## Setup & Requirements

- **PHP** with PDO/mysqli and prepared statements
- **Server-side and client-side validation**
- **Session management** for authenticated pages
- **AJAX endpoints** return JSON
- **File upload validation** (MIME type, size, extension)
