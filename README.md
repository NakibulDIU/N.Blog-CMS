# N.blog CMS

A custom-built, lightweight, and powerful Content Management System (CMS) designed for bloggers. Built using **PHP** and **MySQL**, N.blog provides a complete suite of tools to manage posts, categories, comments, and administrators with ease.

## 🚀 Key Features

* **Dynamic Dashboard:** A centralized hub (`Dashboard.php`) to monitor all blog activity.
* **Post Management:** Full CRUD (Create, Read, Update, Delete) functionality for blog entries including image uploads.
* **Comment Moderation:** A robust system to approve or remove user comments to keep your community safe.
* **Category System:** Organize your content efficiently with a dynamic category management system.
* **Admin Authentication:** Secure login/logout system with session-based access control.
* **Admin Management:** Ability to add or remove site administrators.
* **Profile Customization:** Personalize admin profiles through `MyProfile.php`.

## 📂 Project Structure

Based on the core architecture of N.blog CMS:

* `Includes/`: The engine room of the application.
    * `DB.php`: Database connection configuration.
    * `Function.php`: Global helper functions and business logic.
    * `Sessions.php`: Manages user authentication and flash messages.
* `Uploads/`: Storage directory for all blog-related images.
* `Images/`: Static assets and UI elements.
* `CSS/`: Custom styles for the front-end and back-end interfaces.
* `.htaccess`: Configuration for URL rewriting and server-level security.

## 🛠️ Technology Stack

* **Language:** PHP
* **Database:** MySQL
* **Frontend:** HTML5, CSS3, JavaScript
* **Server:** Apache (recommended)

## 🔧 Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/your-username/N.blog-CMS.git
    ```
2.  **Database Setup:**
    * Create a new MySQL database named `n_blog`.
    * Import the provided SQL schema (if available) into your database.
    * Update `Includes/DB.php` with your database credentials (Host, User, Password).
3.  **Permissions:**
    * Ensure the `Uploads/` directory has write permissions for the web server.
4.  **Launch:**
    * Move the files to your local server (XAMPP/WAMP/MAMP) and navigate to `localhost/N.blog-CMS/Login.php`.

## 🛡️ Security Features

* **Session Management:** Uses secure PHP sessions to protect admin routes.
* **Input Sanitization:** Uses dedicated functions to prevent SQL Injection and XSS attacks.
* **Access Control:** `.htaccess` implemented to protect sensitive directory access.

---
