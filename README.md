# 📚 Library REST API – PHP + MySQL

A full **CRUD REST API** for managing a library book collection, built with PHP and MySQL. Includes a clean frontend UI to interact with every endpoint.

---

## 📋 About

This project demonstrates how to build a RESTful API from scratch using core PHP — no frameworks. It covers all four HTTP methods (GET, POST, PUT, DELETE) with proper status codes, input validation, and parameterised queries to prevent SQL injection.

---

## ✨ Features

- ✅ Full CRUD — Create, Read, Update, Delete books
- 🔍 Search by title or author
- 🔎 Filter by genre or availability
- 🛡️ Parameterised queries (SQL injection safe)
- 🌐 CORS enabled (usable from any frontend)
- 🎨 Dark-mode frontend UI to test all endpoints

---

## 🗂️ Project Structure

```
library-api/
├── api.php       # REST API – handles all routes
├── db.php        # Database connection config
├── index.html    # Frontend UI
├── setup.sql     # Database schema + sample data
└── README.md
```

---

## 🛠️ Built With

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

---

## 🚀 Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) or [WAMP](https://www.wampserver.com/)
- PHP 8+, MySQL 5.7+

### Setup

**1. Clone the repo**
```bash
git clone https://github.com/sofsof23/library-api.git
```

**2. Move to your server root**
```
XAMPP → htdocs/library-api/
WAMP  → www/library-api/
```

**3. Create the database**

Open phpMyAdmin → click **Import** → select `setup.sql` → click **Go**

Or run via CLI:
```bash
mysql -u root -p < setup.sql
```

**4. Update database credentials**

Open `db.php` and set your MySQL username/password:
```php
define('DB_USER', 'root');
define('DB_PASS', '');
```

**5. Open in browser**
```
http://localhost/library-api/index.html
```

---

## 📡 API Reference

**Base URL:** `http://localhost/library-api/api.php`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api.php` | Get all books |
| GET | `/api.php?id=1` | Get book by ID |
| GET | `/api.php?search=orwell` | Search by title/author |
| GET | `/api.php?genre=Fiction` | Filter by genre |
| GET | `/api.php?available=1` | Filter by availability |
| POST | `/api.php` | Add a new book |
| PUT | `/api.php?id=1` | Update a book |
| DELETE | `/api.php?id=1` | Delete a book |

### POST / PUT Body (JSON)
```json
{
  "title":     "The Great Gatsby",
  "author":    "F. Scott Fitzgerald",
  "genre":     "Fiction",
  "year":      1925,
  "available": 1
}
```

### Example Responses

**GET all books**
```json
{
  "success": true,
  "count": 5,
  "data": [
    { "id": 1, "title": "The Great Gatsby", "author": "F. Scott Fitzgerald", ... }
  ]
}
```

**POST (add book)**
```json
{ "success": true, "message": "Book added successfully", "id": 6 }
```

**Error**
```json
{ "error": "Book not found" }
```

---

## 🧠 What This Demonstrates

- Building a REST API without a framework
- HTTP method routing (GET / POST / PUT / DELETE)
- Prepared statements to prevent SQL injection
- Proper HTTP status codes (200, 201, 400, 404, 500)
- CORS headers for cross-origin requests
- Connecting a vanilla JS frontend to a PHP backend

---

## 📌 Status

> ✅ Complete — all endpoints working.

---

*Made by [@sofsof23](https://github.com/sofsof23)*
