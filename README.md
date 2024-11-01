# Taxi Booking System

A web-based taxi booking application developed using PHP, MySQL, HTML, CSS, and JavaScript. This system allows users to book taxis while providing an admin interface for managing drivers, vehicles, and bookings. Built with XAMPP as the local server environment, this system is suitable for small to medium-sized transportation services.

## Features

- **User Registration and Role Selection**: Users can register as either customers or drivers.
- **Admin Panel**: Admins can manage drivers, customers, and view and edit bookings.
- **Driver and Vehicle Management**: Track driver availability, manage vehicle details like type and fuel type.
- **Location-based Booking**: Customers can filter drivers based on their location for convenient booking.
- **Booking System**: Customers can book rides with real-time booking status updates.
- **Sorting Options**: Filter taxis based on vehicle type, fuel type, and driver characteristics.
- **Database Integration**: MySQL is used for managing data across the system reliably.

## Technologies Used

- **Backend**: PHP for server-side scripting and MySQL for database management.
- **Frontend**: HTML, CSS, and JavaScript for user interface and interactivity.
- **Server Setup**: XAMPP to host and run the application locally.

## Installation

To run this project locally, follow these steps:

1. **Clone the Repository**:
    ```bash
    git clone https://github.com/abilbiju/taxi-booking-system.git
    cd taxi-booking-system
    ```

2. **Set Up XAMPP**:
   - Download and install [XAMPP](https://www.apachefriends.org/index.html).
   - Move the project files to the `htdocs` folder in the XAMPP installation directory (usually located in `C:/xampp/htdocs` on Windows).

3. **Database Configuration**:
   - Start XAMPP and open `phpMyAdmin` (usually at `http://localhost/phpmyadmin`).
   - Create a new database (e.g., `taxi_booking`).
   - Import the SQL file provided in the repository (`database.sql`) to set up the necessary tables and data structure.

4. **Configure Database Connection**:
   - Open the project’s PHP files and configure database connection settings in the configuration file (e.g., `db.php`) with your MySQL username and password.

5. **Run the Application**:
   - Open your browser and navigate to `http://localhost/taxi-booking-system` to start using the application.

## Project Structure

- `index.php`: Main landing page.
- `admin/`: Contains pages and scripts for admin management of drivers, vehicles, and bookings.
- `customer/`: Interface for customers to book taxis and view booking status.
- `driver/`: Interface for drivers to manage availability and view bookings.

## Usage

1. **User Registration**: Register as a customer or driver.
2. **Admin Login**: Admin can log in to manage users, drivers, and bookings.
3. **Booking**: Customers can view available drivers and make bookings based on location.
4. **Booking Management**: Admin can view, update, and delete bookings as needed.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
