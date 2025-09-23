
# 🛒 E-Commerce Website using PHP & MySQL  

This is a fully functional e-commerce website developed using **PHP**, **MySQL**, **HTML**, **CSS (Tailwind)**, and **JavaScript**. It allows users to browse products, add them to a cart, proceed to checkout, and make payments using a fake payment gateway with real-time order status management.  

## 🚀 Features  
- **User Authentication**:  
  - User Registration & Login System  
  - Secure session handling  

- **Product Management**:  
  - Dynamic product listing with images & categories  
  - Search functionality for easy navigation  

- **Shopping Cart & Checkout**:  
  - Add, remove, or update product quantities in the cart  
  - Checkout form with name, email, phone, and address details  
  - Order summary page before payment  

- **Payment Gateway (Fake)**:  
  - Credit/Debit Card validation from the database  
  - UPI, Net Banking, and Cash on Delivery options  

- **Admin Panel**:  
  - Add, edit, or delete products  
  - View orders and update status (Pending, Completed, Cancelled)  
  - Search orders by Order ID  

- **UI & UX**:  
  - Styled using **Tailwind CSS** for a modern, responsive design  
  - Dark mode/Light mode toggle  
  - Consistent header/footer across pages  

- **Database**:  
  - MySQL database to store products, users, orders, and payment details  

---

## 🗂️ Project Structure
```
ecommerce/
├── db.php               // Database connection
├── login.php            // User login
├── register.php         // User registration
├── index.php            // Homepage
├── products.php         // Product listing
├── cart.php             // Shopping cart
├── checkout.php         // Checkout process
├── payment.php          // Payment gateway
├── admin/               // Admin panel pages
├── assets/              // Images, CSS, JS
└── includes/            // Common header/footer
```

---

## ⚙️ Tech Stack  
- **Frontend**: HTML, CSS (Tailwind), JavaScript  
- **Backend**: PHP  
- **Database**: MySQL  
- **Tools**: XAMPP, phpMyAdmin  

---

## 📸 Screenshots  
(Add some screenshots of your homepage, cart, checkout, and admin panel here.)  

---

## 📌 How to Run  
1. Clone this repository:  
   ```bash
   git clone https://github.com/your-username/ecommerce.git
   ```
2. Import the `ecommerce.sql` file into **phpMyAdmin**.  
3. Start XAMPP/WAMP server.  
4. Place the project folder inside `htdocs` (for XAMPP).  
5. Open browser and go to:  
   ```
   http://localhost/ecommerce/
   ```

---

## 🧑‍💻 Author  
Developed by [Your Name]  
