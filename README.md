# Cafe Website

A responsive cafe website with customer and admin interfaces built using PHP, MySQL, HTML, and CSS.

## Color Scheme

- Green: #577e4c
- White: #fdfdfd

## Features

### Customer Interface

- Home page with cafe introduction and featured items
- Menu page with categorized food and beverage items
- Contact page with location information and contact form

### Admin Interface

- Secure login system
- Dashboard with overview of orders and statistics
- Orders management
- Menu management (add, edit, delete items)

## Installation Instructions

1. **Clone or download the project**
   Place the files in your web server directory (e.g., `htdocs` for XAMPP).

2. **Create the database**

   - Open phpMyAdmin or your preferred MySQL management tool
   - Create a new database named `cafe_db`
   - Import the database schema from `database/cafe_db.sql`

3. **Configure database connection**

   - Open `config/db.php`
   - Update the database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root'); // Change if needed
     define('DB_PASS', '');     // Change if needed
     define('DB_NAME', 'cafe_db');
     ```

4. **Set up the image directories**

   - Ensure the directory `assets/images/menu` exists and is writable
   - If you're uploading images through the admin panel, make sure PHP has write permissions

5. **Access the website**
   - Customer Interface: `http://localhost/Cafe/`
   - Admin Interface: `http://localhost/Cafe/admin/`

## Admin Access

- Default admin username: `admin`
- Default admin password: `admin123`
- **Important**: Change the default credentials after first login

## Project Structure

```
/
├── index.php               # Entry point for customer interface
├── admin/                  # Admin interface files
│   ├── index.php           # Admin login
│   ├── dashboard.php       # Admin dashboard
│   ├── orders.php          # Orders management
│   ├── menu.php            # Menu management
├── assets/                 # Static assets
│   ├── css/                # CSS files
│   ├── js/                 # JavaScript files
│   ├── images/             # Image files
├── config/                 # Configuration files
│   ├── db.php              # Database connection
├── database/               # Database files
│   ├── cafe_db.sql         # Database schema
├── includes/               # PHP includes
│   ├── header.php          # Header template
│   ├── footer.php          # Footer template
│   ├── functions.php       # Helper functions
└── pages/                  # Customer pages
    ├── home.php            # Home page
    ├── menu.php            # Menu page
    ├── contact.php         # Contact page
```

## Troubleshooting

- **Database Connection Issues**: Verify your database credentials in `config/db.php`
- **Image Upload Problems**: Check folder permissions for `assets/images/menu`
- **404 Errors**: Make sure your .htaccess file is properly configured for your server

## License

MIT
