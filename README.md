# PFE Project Tracking System

A web platform to manage and track final year projects (PFE), built with PHP, MySQL, and XAMPP.

---

## 👥 Team

| Member | Role |
|--------|------|
| Member 1 | Frontend (HTML/CSS) |
| Member 2 | Backend (PHP/MySQL) |
| Member 3 | Integration + Automation |

---

## ⚙️ Local Setup (do this once)

### 1. Install required tools
- [XAMPP](https://www.apachefriends.org/) → for Apache + MySQL + PHP
- [Git](https://git-scm.com/) → for version control
- [VS Code](https://code.visualstudio.com/) → code editor

### 2. Clone the project
```bash
git clone https://github.com/Meriemkh26/pre-project-tracking.git
cd pre-project-tracking
```

### 3. Set up the database
1. Open XAMPP → start **Apache** and **MySQL**
2. Go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
3. Create a database called `pfe_tracking`
4. Click **Import** → select `backend/database.sql` → click **Go**

### 4. Run the project
1. Copy the project folder into `C:/xampp/htdocs/`
2. Open your browser and go to [http://localhost/pre-project-tracking/backend/public/](http://localhost/pre-project-tracking/backend/public/)

---

## 🔁 Git Workflow (every single day)

### Before coding:
```bash
git pull
```

### After coding:
```bash
git add .
git commit -m "your message here"
git push
```

---

## 📁 Project Structure
```
pre-project-tracking/
├── frontend/          → CSS, JS, images
├── backend/
│   ├── config/        → database connection
│   ├── controllers/   → app logic
│   ├── models/        → database queries
│   ├── views/         → HTML pages (PHP)
│   └── public/        → entry point (index.php)
├── automation/        → notifications + reports
└── database.sql       → database schema
```

---

## ⚠️ Rules

- ❌ Never work on the same file at the same time
- ❌ Never skip `git pull` before coding
- ✅ Commit small and often
- ✅ Each member works in her own folder