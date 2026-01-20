# 🐾 Pet Adoption Center - Modern Version

A fully functional pet adoption platform built with **PHP** and **MySQL** with a modern, responsive design and email notification system.

## ✨ Features

### 🔐 User Authentication
- User registration and login system
- Password hashing for security (bcrypt)
- Session management

### 🐕 Pet Management
- Post pets for adoption with details and images
- Browse available pets with filter options
- View and edit your posted pets
- Delete pets from listings

### 📝 Adoption System
- **New**: Adoption form with adopter information collection
- Adopter must fill: Name, Age (18+), Email, Phone, Reason for adoption
- Pet owner receives detailed email with adopter's contact information
- Direct email communication enabled between adopters and owners
- Request status tracking (pending, accepted, rejected)

### 📧 Email Notifications
- Automatic email sends to pet owner when adoption request is made
- Beautiful HTML formatted emails with adopter information
- Includes adopter contact details for direct communication
- Configurable admin email address

### 🎨 Modern UI/UX
- **Modern gradient design** with purple/blue color scheme
- **Smooth animations** and transitions
- **Fully responsive** design for mobile, tablet, and desktop
- **Card-based layout** with hover effects
- Professional typography and spacing
- Beautiful form inputs with focus states
- Interactive buttons with gradient backgrounds

## 📦 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server (via XAMPP/WAMP/MAMP)

### 1. Database Setup

1. Open **phpMyAdmin** (usually at `http://localhost/phpmyadmin`)
2. Create a new database named `pet_adoption_center`
3. Select the database and go to the **SQL** tab
4. Copy and paste the contents of `pet_adoption_center_updated.sql`
5. Click "Go" to execute the SQL

### 2. Project Setup

1. Copy all files to your web server directory:
   - **XAMPP**: `C:/xampp/htdocs/pas/`
   - **WAMP**: `C:/wamp/www/pas/`
   - **MAMP**: `/Applications/MAMP/htdocs/pas/`

2. Ensure all these files are in the same directory:
```
adopt_request.php
adoption_form.php (NEW)
config.php
delete_pet.php
edit_pet.php (NEW)
index.php
login.php
logout.php
my_pets.php
post_pet.php
register.php
style.css
pet_adoption_center_updated.sql
README.md
```

3. Update database credentials in `config.php` if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pet_adoption_center');
define('ADMIN_EMAIL', 'your-email@example.com'); // Update this!
```

### 3. Start the Application

1. Start Apache and MySQL in XAMPP/WAMP/MAMP
2. Open your browser and navigate to:
   - `http://localhost/pas/register.php` - Create account
   - `http://localhost/pas/login.php` - Login

## 🚀 Usage Guide

### For New Users

1. **Register** an account at the registration page
2. **Login** with your credentials
3. **Browse** available pets on the adoption wall
4. **Click "Adopt Me"** on any pet
5. **Fill adoption form** with your information
6. **Submit** - pet owner receives email with your details

### For Pet Owners

1. **Post a Pet** through "Post Pet" page
   - Fill pet details (name, species, breed, age, gender)
   - Add description and optional image URL
   - Click "Post Pet"

2. **Manage Your Pets** in "My Pets"
   - **Edit** pet information
   - **Delete** pets from listings

3. **Receive Adoption Requests**
   - Get email when someone requests your pet
   - Email has adopter's name, age, email, phone, and reason
   - Contact adopter directly via email or phone

### Image URLs

Use direct image links ending in image extensions (.jpg, .png, etc.):
- `https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=400` (Dog)
- `https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=400` (Cat)
- `https://images.unsplash.com/photo-1552053831-71594a27632d?w=400` (Dog)

## 📁 File Structure

## 📁 File Structure

| File | Purpose |
|------|---------|
| **config.php** | Database config & helper functions |
| **index.php** | Main adoption wall (browse pets) |
| **adoption_form.php** | ⭐ NEW - Adoption request form |
| **adopt_request.php** | Redirect to adoption form |
| **post_pet.php** | Post new pet for adoption |
| **edit_pet.php** | ⭐ NEW - Edit pet information |
| **my_pets.php** | View & manage posted pets |
| **delete_pet.php** | Delete pet listing |
| **register.php** | User registration |
| **login.php** | User login |
| **logout.php** | Logout functionality |
| **style.css** | ⭐ MODERNIZED - Styling |

## ⭐ New Features in This Version

### 1. Adoption Form Page
- Beautiful form with pet summary showing
- Collects: Name, Age (18+), Email, Phone, Adoption Reason
- Full validation of all fields
- Success message and redirect

### 2. Email Notification System
- HTML formatted emails sent to pet owner
- Includes adopter's contact information
- Professional design with branding
- Configured in `config.php`

### 3. Edit Pet Page
- Edit all pet information
- Validate changes before saving
- User-friendly form with pre-filled values
- Redirect to pets list on success

### 4. Modern UI Design
✨ **Beautiful & Professional:**
- Purple/blue gradient color scheme
- Smooth animations and transitions
- Consistent spacing and typography
- Subtle shadows for depth
- Interactive hover effects
- Professional button styles

✨ **Fully Responsive:**
- Mobile-first approach
- Tablet optimized
- Desktop enhanced
- Touch-friendly on all devices

## 🔐 Security Features

- ✅ Password hashing with bcrypt
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection (htmlspecialchars)
- ✅ Session-based authentication
- ✅ Input validation on all forms

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## 🛠️ Technology Stack

| Technology | Purpose |
|-----------|---------|
| **PHP 7.4+** | Backend logic |
| **MySQL 5.7+** | Database |
| **HTML5** | Structure |
| **CSS3** | Styling & animations |
| **Apache** | Web server |

## 📊 Database Schema

### adoption_requests Table (UPDATED)
- `request_id` (Primary Key)
- `pet_id` (Foreign Key)
- `requester_id` (Foreign Key)
- `owner_id` (Foreign Key)
- ⭐ `adopter_name` - Adopter's full name
- ⭐ `adopter_age` - Adopter's age
- ⭐ `adopter_email` - Contact email
- ⭐ `adopter_phone` - Contact phone
- ⭐ `adoption_reason` - Why they want to adopt
- `status` (pending/accepted/rejected)
- `created_at` - Timestamp

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| Database won't connect | Check credentials in config.php, ensure MySQL is running |
| Blank pages | Enable PHP error reporting, check server logs |
| Images not showing | Use direct image URLs (ending in .jpg, .png, etc.) |
| CSS not loading | Verify style.css is in same directory |
| Emails not sending | Check mail server config, consider SMTP relay service |
| 404 on edit pet | Clear cache, verify edit_pet.php exists |

## 📝 License

Open-source and free for educational purposes.

## 👨‍💻 Support

For issues:
1. Check Troubleshooting section
2. Verify all files are in correct directory
3. Ensure database is created and tables are set up
4. Check PHP error logs for details
5. Verify Apache and MySQL are running

---

**Made with ❤️ for pet lovers everywhere** 🐾

**Happy Pet Adoption! 🐾**
