# Pet Adoption Center - Complete PHP Project

A fully functional pet adoption platform built with PHP and MySQL.

## Features

✅ **User Authentication**
- User registration and login system
- Password hashing for security
- Session management

✅ **Pet Management**
- Post pets for adoption with details and images
- Browse available pets
- View your posted pets

✅ **Adoption System**
- Send adoption requests for pets
- Notification system with badge counter
- Accept or reject adoption requests
- Automatic status updates (available → adopted)

✅ **Notifications**
- Real-time notification bell with unread count
- View all adoption requests
- Manage requests (accept/reject)
- Automatic cleanup when pet is adopted

✅ **Modern UI**
- Responsive design for all devices
- Beautiful gradient color scheme
- Card-based layout
- Smooth animations and transitions

## Installation Steps

### 1. Database Setup

1. Open phpMyAdmin (usually at `http://localhost/phpmyadmin`)
2. Create a new database named `pet_adoption_center`
3. Select the database and go to the SQL tab
4. Copy and paste the SQL schema provided at the top
5. Click "Go" to execute

### 2. Project Setup

1. Copy all PHP files to your web server directory:
   - **XAMPP**: `C:/xampp/htdocs/pet-adoption/`
   - **WAMP**: `C:/wamp/www/pet-adoption/`
   - **MAMP**: `/Applications/MAMP/htdocs/pet-adoption/`

2. Ensure all files are in the same directory:
   ```
   pet-adoption/
   ├── config.php
   ├── register.php
   ├── login.php
   ├── index.php
   ├── post_pet.php
   ├── adopt_request.php
   ├── notifications.php
   ├── my_pets.php
   ├── logout.php
   └── style.css
   ```

3. Update database credentials in `config.php` if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Your MySQL password
   define('DB_NAME', 'pet_adoption_center');
   ```

### 3. Start the Application

1. Start Apache and MySQL in XAMPP/WAMP/MAMP
2. Open your browser and navigate to:
   - `http://localhost/pet-adoption/register.php` (to create an account)
   - Or `http://localhost/pet-adoption/login.php` (if you have an account)

## File Structure

- **config.php** - Database configuration and helper functions
- **register.php** - User registration page
- **login.php** - User login page
- **index.php** - Main adoption wall (browse pets)
- **post_pet.php** - Post a new pet for adoption
- **adopt_request.php** - Handle adoption requests
- **notifications.php** - View and manage adoption requests
- **my_pets.php** - View user's posted pets
- **logout.php** - Logout functionality
- **style.css** - Complete styling for the application

## Usage Guide

### For New Users:
1. Register an account at `register.php`
2. Login with your credentials
3. Browse available pets on the main page
4. Click "Post Pet" to add a pet for adoption
5. Click "Adopt Me" on any pet to send an adoption request

### For Pet Owners:
1. Post pets through "Post Pet" page
2. View your pets in "My Pets"
3. Check the notification bell (🔔) for adoption requests
4. Accept or reject requests in the Notifications page
5. Accepted pets are marked as "Adopted" and removed from the wall

### Image URLs:
When posting a pet, you can use any direct image URL. For testing, you can use:
- `https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=400` (Dog)
- `https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=400` (Cat)
- `https://images.unsplash.com/photo-1552053831-71594a27632d?w=400` (Dog)

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention using prepared statements
- XSS protection using `htmlspecialchars()`
- Session-based authentication
- Input validation on all forms

## Database Schema

The application uses 4 main tables:
- **users** - Store user accounts
- **pets** - Store pet information
- **adoption_requests** - Track adoption requests
- **notifications** - Store notification data

## Troubleshooting

**Problem**: Can't connect to database
- **Solution**: Check MySQL is running and credentials in `config.php` are correct

**Problem**: Page shows blank or errors
- **Solution**: Enable error reporting in PHP or check Apache error logs

**Problem**: Images not showing
- **Solution**: Ensure image URLs are direct links (ending in .jpg, .png, etc.)

**Problem**: CSS not loading
- **Solution**: Ensure `style.css` is in the same directory as PHP files

## Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3
- **Server**: Apache (via XAMPP/WAMP/MAMP)

## License

This project is open-source and free to use for educational purposes.

## Support

For issues or questions, please check:
1. All files are in the correct directory
2. Database is created and tables are set up
3. Apache and MySQL are running
4. Database credentials are correct

---

**Happy Pet Adoption! 🐾**
