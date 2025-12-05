# Online Computer Store

A full-stack e-commerce web application for computer hardware and peripherals, featuring a comprehensive product catalog with categories, subcategories, and advanced filtering options.

##  Developer Information

**Student Name:** Lovepreet Singh  
**Student ID:** 5143007

---

## 📋 Project Description

This project is a complete e-commerce platform designed specifically for computer hardware and accessories. The application features a multi-level categorization system that allows users to browse through various product groups, categories, and subcategories.

### Key Features

- **Multi-Level Product Hierarchy:**
  - 6 Main Product Groups (Laptops, Desktop Computers, PC Components, Displays & Monitors, Peripherals & Accessories, Cables & Adapters)
  - 27 Product Categories
  - 66 Product Subcategories
  - 100+ Products with detailed specifications

- **Product Catalog:**
  - Comprehensive product listings with descriptions
  - Original and deal pricing
  - Stock availability tracking
  - Sale indicators for discounted products
  - Product images support

- **Database Structure:**
  - Groups Table: Main product classifications
  - Categories Table: Product categories within each group
  - Subcategories Table: Detailed product subcategories
  - Products Table: Complete product information with pricing and inventory
  - Users Table: Stores users details
  - Orders Table: Complete user order details with delievery address
  - Orders History Table: Stores product history for each order


# Project Setup Instructions

Follow the steps below to set up and run the **Online Computer Store** project on your local system using **XAMPP**.

---

## 1. Install XAMPP

Download and install XAMPP for your operating system.

After installation, open the **XAMPP Control Panel**.

---

## 2. Navigate to the `htdocs` Folder

Open the XAMPP installation directory:

* **Windows:** `C:/xampp/htdocs/`
* **macOS:** `/Applications/XAMPP/htdocs/`
* **Linux:** `/opt/lampp/htdocs/`

Clone or copy the project repository inside this `htdocs` folder:

```bash
git clone <your-repository-link>
```

This will create the project folder at:

```
/htdocs/online-computer-store/
```

---

## 3. Start Apache and MySQL

Open the **XAMPP Control Panel** and start:

* **Apache Web Server**
* **MySQL Database**

Both should show a green "Running" status.

---

## 4. Open phpMyAdmin

Visit phpMyAdmin in your browser:

```
http://localhost:8000/phpmyadmin
```

*(If port 8000 doesn’t work, try `http://localhost/phpmyadmin`)*

---

## 5. Import the Database

1. Create a new database: `online_computer_store`.
2. Click **Import**.
3. Select the `.sql` file from the project.
4. Import it to populate all required tables.

---

## 6. Set File Upload Permissions (Linux/macOS)

If your project includes file uploads, set proper permissions:

```bash
chmod -R 777 /path/to/xampp/htdocs/online-computer-store/
```

This allows uploading images/files without permission issues.

*(Windows users can skip this step.)*

---

##  7. Run the Project

After setup, open the website in your browser:

```
http://localhost/online-computer-store/
```

Now you can fully navigate through the website and explore product pages, categories, checkout, admin panel, etc.

Admin Details:
admin@gmail.com
Password:- admin@123

---

## 8. Troubleshooting

* If pages don't load, ensure **Apache & MySQL** are running.
* Check if the project folder name matches the URL.
* Verify the database credentials in `includes/db.php`.
* If uploads fail, re-check permissions.

---

## Setup Complete!

Your local development environment is now ready. Enjoy building and customizing your project!
