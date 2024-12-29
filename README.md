# Library Management System

This is a full-stack Library Management System designed to manage books, members, and library transactions. The frontend is developed using HTML, CSS, and JavaScript, while the backend is built using Node.js and PHP. MySQL is used as the database to store library data.

## Project Description

The Library Management System provides two interfaces:

- **Librarian Interface**: Librarians can perform CRUD operations on books, manage user accounts, view all loans, and filter book records. They can also view detailed information about each user’s loan history, including the number of books each user currently has on loan.
- **User Interface**: Library users can search for available books and view details about each book.

The system uses JWT tokens for secure authentication. Login tokens are generated upon successful login and are verified with each backend request to ensure only authorized users access specific resources.

## Features

- **Book Management**: Add, update, delete, and view books in the library.
- **Member Management**: Manage library members' information and track membership status.
- **Transaction Management**: Handle book loans, returns, and due date tracking.
- **Search Functionality**: Quickly find books and members through a search interface.
- **Admin Access**: Admin controls to manage system data and settings.
- **JWT Authentication**: Secure authentication and authorization through JWT tokens, validated on each backend request.
## Technology Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: Node.js, PHP
- **Database**: MySQL

## Installation

### Prerequisites

- [Node.js](https://nodejs.org/) for running backend services.
- [PHP](https://www.php.net/) for handling backend tasks.
- [MySQL](https://www.mysql.com/) for database management.
- [XAMPP](https://www.apachefriends.org/) to set up a local PHP server. **Ensure the project is located inside the `htdocs` folder in your XAMPP installation directory.**

### Steps

Clone the repository:

```bash
git clone https://github.com/your-username/library-management-system.git
cd library-management-system
```


## Database Setup

Create a MySQL database named `library_management`.  
Configure the MySQL database connection in the .env file:
```bash
DB_HOST=your_mysql_host
DB_USER=your_mysql_user
DB_PASS=your_mysql_password
DB_NAME=library_management
```
## Backend Setup

Install Node.js dependencies:

```bash
cd backend
npm install
```
Run any PHP files required for backend tasks by setting up a PHP server (e.g., XAMPP or WAMP).

## Accessing the Application
Open index.html in your browser or navigate to http://localhost:<your_port> to start using the Library Management System.

