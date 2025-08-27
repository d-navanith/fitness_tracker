<?php
session_start();
session_destroy();
header("refresh:3; url=auth/login.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Fitness Tracker</title>
    <link rel="stylesheet" href="css/style.css">
    <style>

        .logout-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            padding: 20px;
        }
        
        .logout-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 50px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .logout-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(52, 152, 219, 0.3);
        }
        
        .logout-icon svg {
            width: 50px;
            height: 50px;
            color: white;
        }
        
        .logout-card h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2.2rem;
            font-weight: 700;
        }
        
        .logout-card p {
            color: #7f8c8d;
            margin-bottom: 30px;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        .countdown {
            font-size: 1.5rem;
            font-weight: 700;
            color: #3498db;
            margin: 0 5px;
        }
        
        .logout-btn {
            display: inline-block;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-decoration: none;
            padding: 15px 35px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .logout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 600px) {
            .logout-card {
                padding: 40px 30px;
            }
            
            .logout-card h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-card">
            <div class="logout-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </div>
            <h1>Successfully Logged Out</h1>
            <p>Thank you for using Fitness Tracker. You have been safely logged out of your account.</p>
            <p>Redirecting to login page in <span class="countdown" id="countdown">3</span> seconds...</p>
            <a href="auth/login.php" class="logout-btn">Go to Login Now</a>
        </div>
    </div>

    <script>
        let seconds = 3;
        const countdownElement = document.getElementById('countdown');
        
        const interval = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(interval);
            }
        }, 1000);
    </script>
</body>
</html>