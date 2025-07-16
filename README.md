Project Setup Guide
1. Install XAMPP
Download the latest XAMPP from https://www.apachefriends.org.

Run the installer and select Apache, MySQL, PHP, and phpMyAdmin (default options).

2. Locate the htdocs Folder
Windows: C:\xampp\htdocs

Mac/Linux: /opt/lampp/htdocs

Copy your project files into this folder (e.g., extract a ZIP or clone a repository here).

3. Import the SQL Database
Find your database file .

Open phpMyAdmin (http://localhost/phpmyadmin).

Create a new database, go to Import, select your .sql file, and execute.

4. Launch the Project
Start Apache and MySQL via the XAMPP Control Panel.

Access the project in your browser:

http://localhost/PHPproject 


Support & Contact
For assistance, please contact:
📧 Email: guilhermefilho095@gmail.com

# PHPproject
Main Objective
To develop a comprehensive web system for managing employees (with different access levels: manager, stock keeper, and regular employee) and inventory items, featuring CRUD (Create, Read, Update, Delete) functionalities for both modules.

Technologies Used
Back-end: Pure PHP (with session-based authentication).

Database: MySQL (tables: funcionarios and produtos).

Front-end: HTML5, CSS3 (responsive designs with gradients), and JavaScript (real-time validations).

Security: Input sanitization, password hashing (password_hash), and role-based access control.

Key Features
1. Employee Module
Authentication: Login with credential verification and role-based redirection (manager, stock keeper, employee).

Dashboard:

Manager: Full access (add/edit/delete employees and products).

Stock Keeper: Inventory management (products only).

Employee: Limited to profile viewing.

Employee CRUD:

Add: Form with password strength validation (color-coded feedback).

List: Searchable table with edit/delete actions.

Edit: Data updates (optional password retention).

Delete: Confirmation before deletion (except for the logged-in user).

2. Inventory Module
Product CRUD:

Add: Fields for name, price, quantity, and status (available/sold).

List: Table with status and stock-level filters (color-coded for low stock).

Edit/Delete: Similar to the employee module.

Additional Features
User Profile: Update personal data (including password).

Logout: Session termination with redirect to the login page.

Target Audience
Managers: System administrators with full control.

Stock Keepers: Inventory management staff.

Employees: Users with limited access (profile only).

Key Differentiators
Intuitive Interface: Modern design with visual feedback (success/error messages, password strength indicator).

Security: Protection against SQL injection (input sanitization) and secure password storage.

Responsiveness: Adapts to different devices.

Real-Time Validations: JavaScript for passwords and numeric fields (e.g., price > 0).

Database Structure
Table funcionarios: Fields like idFunc, nickname, senha (hash), tipo (manager/employee/stock keeper).

Table produtos: Fields like id_produto, nome_produto, preco, quantidade_estoque, estado.

Final Notes
Ready to Use: Fully functional with sample data (e.g., products like "RTX 4090" and pre-registered employees).

Scalable: Can be expanded with new features (e.g., reports, purchase orders).

This project demonstrates full-stack web development skills, from database modeling to secure and interactive interface implementation.
