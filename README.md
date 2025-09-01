Fitness Tracker

A web-based fitness tracking application that empowers users to manage workouts, nutrition, and fitness goals effectively. The system provides BMI calculation, nutrition and water logging, workout planning, progress tracking with photos, and social features like challenges. Built with PHP, MySQL, JavaScript, and CSS, it follows an incremental agile development model ensuring scalability, maintainability, and continuous improvement.

🎥 Demo Video
👉 https://youtu.be/vESjppiMb48?si=p1UyWCNSFo1uh8xs

🚀 Features

Secure Authentication with password hashing & session management

Workout Management: CRUD operations for workout plans & history

Nutrition Tracking with calorie & macronutrient calculations (Chart.js)

Goal Management based on SMART principles

Progress Monitoring: BMI logs & photo uploads

Social Features: Friends, challenges, and basic gamification

Security: CSRF protection, input sanitization, prepared statements

📂 Project Structure
fitness_tracker/
│── auth/        # Authentication (login, register, logout)
│── config/      # DB & security configuration
│── css/         # Stylesheets
│── includes/    # Header & footer
│── images/      # Exercise images & assets
│── *.php        # Core modules (workouts, nutrition, progress, etc.)
│── fitness_tracker.sql  # Database schema

🛠️ Tech Stack & Dependencies

Backend: PHP 8.2+

Database: MySQL 8.0

Frontend: HTML5, CSS3, JavaScript

Visualization: Chart.js

Server: Apache (via XAMPP/LAMP/WAMP)

Tools: phpMyAdmin, Figma (UI/UX design), Visio (architecture diagrams)

Security: PDO prepared statements, CSRF tokens, session hardening

Optional

GD Library (for handling progress photo uploads)

PHPUnit & Selenium (for automated testing)

⚙️ Installation & Running
Prerequisites

Install XAMPP
 or LAMP/WAMP stack

Ensure PHP ≥ 7.4 (tested on PHP 8.x) and MySQL ≥ 5.7

Steps

Clone repository:

git clone https://github.com/d-navanith/fitness_tracker.git


Move project to web root:

C:/xampp/htdocs/fitness_tracker


Import database:

Open phpMyAdmin

Create DB fitness_tracker

Import fitness_tracker.sql

Configure DB in config/db.php:

$host = "localhost";
$user = "root";
$password = "";
$dbname = "fitness_tracker";


Start Apache & MySQL in XAMPP

Access system via browser:

http://localhost/fitness_tracker

🧪 Testing & Maintenance

Testing Methodologies: Unit, integration, usability, security (OWASP Top 10)

Automated Tools: PHPUnit for backend, Selenium for end-to-end

Cross-browser Testing: Chrome, Firefox, Edge, Safari

Maintenance Plan:

Daily DB backups & log monitoring

Weekly performance optimization

Monthly security audits & patching

🐛 Known Bugs / Limitations

Limited mobile optimization (not a PWA or native app)

No external integration with APIs or wearables (e.g., Fitbit, Apple Health)

Nutrition data limited to a small dataset

No password recovery / email verification yet

Image uploads lack strict size/type validation

📌 Roadmap
Short-term (3–6 months)

Implement PWA/mobile-friendly version

Add API integration for food & exercise databases

Enhance analytics with trend insights

Medium-term (6–12 months)

Wearable device integration (Fitbit, Apple Watch)

Machine learning for personalized recommendations

Expanded social features (community challenges, leaderboards)

Long-term (1–2 years)

AI-driven adaptive workouts & diet planning

Healthcare provider integration (secure medical fitness monitoring)

Gamification: achievements, streaks, rewards

📊 Evaluation

✅ Strengths: Secure, modular, user-friendly, comprehensive fitness features

⚠️ Weaknesses: Limited mobile optimization & analytics, basic social features

🔒 Security: Passes SQL injection, CSRF, and session security tests

🎯 User Feedback: Positive on usability, intuitive navigation, and dashboard insights

🤝 Contributing

Pull requests are welcome! Please follow best practices and ensure security measures in contributions.

📄 License

This project is licensed under the MIT License – feel free to use and modify.
