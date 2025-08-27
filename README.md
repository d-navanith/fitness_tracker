# Fitness Tracker

A **web-based fitness tracking application** that helps users manage their workouts, nutrition, goals, and progress. The system allows individuals to track fitness activities, calculate BMI, log food and water intake, set goals, and monitor progress through reports and photos.

---

## 🚀 Features

- **User Authentication**
  - Register, login, and manage user sessions securely.
  
- **Workout Management**
  - Create, edit, and delete workout plans.
  - Track completed workouts and view workout history.
  - Access exercise library with details.

- **Nutrition Tracking**
  - Log daily food intake and water consumption.
  - Pre-populated food and exercise datasets.

- **Goals & Progress**
  - Set and track fitness goals.
  - Upload and manage progress photos.
  - BMI calculator for health insights.

- **Social Features**
  - Add friends and participate in fitness challenges.

---

## 📂 Project Structure

```
fitness_tracker/
│── auth/                  # Authentication (login, register, logout)
│── config/                # Database & session configuration
│── css/                   # Stylesheets
│── images/                # Exercise & progress images
│── includes/              # Reusable components (header, footer)
│── *.php                  # Core features (dashboard, workouts, nutrition, etc.)
│── fitness_tracker.sql    # Database schema
```

---

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript  
- **Backend**: PHP (Core PHP)  
- **Database**: MySQL  
- **Server**: XAMPP / Apache  

---

## ⚙️ Installation & Setup

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) or any PHP-MySQL server stack.
- Web browser (Google Chrome, Firefox, etc.).

### Steps
1. Clone or download this repository:
   ```bash
   git clone https://github.com/d-navanith/fitness_tracker.git
   ```
2. Move the project folder to the server root:
   ```
   C:/xampp/htdocs/fitness_tracker
   ```
3. Import the database:
   - Open **phpMyAdmin** → Create a new database (e.g., `fitness_tracker`).
   - Import the file `fitness_tracker.sql`.

4. Configure database connection:
   - Open `config/db.php` and update credentials:
     ```php
     $host = "localhost";
     $user = "root";
     $password = "";
     $dbname = "fitness_tracker";
     ```
5. Start Apache & MySQL in XAMPP.

6. Run the project in your browser:
   ```
   http://localhost:8080/fitness_tracker
   ```

---

## 👤 Default Users (if available)
- Admin/User accounts can be created via the registration page.
- Modify roles manually in the database if required.

---

## 📌 Future Enhancements
- Integration with fitness wearables (e.g., Fitbit, Apple Health).
- Mobile app version.
- AI-based personalized workout recommendations.
- Gamification features (badges, streaks, achievements).

---

## 🤝 Contributing
Pull requests are welcome! Please follow best practices and proper commit messages.

---

## 📄 License
This project is licensed under the **MIT License** – feel free to use and modify.
