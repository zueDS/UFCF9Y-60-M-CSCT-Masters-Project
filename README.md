# UFCF9Y-60-M-CSCT-Masters-Project

# Installation Guide

## Prerequisites

Before installing the system, make sure the following software is installed:

* XAMPP
* PHP
* MySQL
* Web browser
* Git

## 1. Clone the Repository

```bash
git clone https://github.com/zueDS/UFCF9Y-60-M-CSCT-Masters-Project.git
```

Navigate to the project directory:

```bash
cd UFCF9Y-60-M-CSCT-Masters-Project
```

## 2. Configure the Web Server

Move the project folder into the XAMPP `htdocs` directory:

```text
xampp/htdocs/
```

For example:

```text
C:\xampp\htdocs\UFCF9Y-60-M-CSCT-Masters-Project
```

## 3. Start XAMPP

Open the XAMPP Control Panel and start:

* Apache
* MySQL

## 4. Database Setup

1. Open phpMyAdmin.
2. Create the required database.
3. Import the project's SQL database file.
4. Update the database connection settings in the project configuration file.

## 5. Run the System

Open a web browser and navigate to:

```text
http://localhost/UFCF9Y-60-M-CSCT-Masters-Project/
```

The system should now be available locally.

## 6. Troubleshooting

If the system does not load correctly, check:

* Apache is running.
* MySQL is running.
* The database name and credentials are correct.
* The project is located inside the XAMPP `htdocs` directory.
* The required PHP extensions are enabled.
* The URL matches the project folder name.

## Installation Complete

The system is now installed and ready to use.
