<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="Photos/YD_Logo_BG.png" type="image/png">
    <title>UTSAV-Contact</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #333;
            padding: 20px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-section img {
            max-width: 80px;
            height: 150px !important;
            width: 80px;
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.2));
        }

        .contact-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            max-width: 500px;
            width: 100%;
        }

        .contact-item {
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contact-item:last-child {
            margin-bottom: 0;
        }

        .contact-name {
            color: #ffffff;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .contact-role {
            color: #e0e0e0;
            font-size: 14px;
            margin-bottom: 8px;
            font-style: italic;
        }

        .phone-number {
            color: #e6efe7ff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: inline-block;
            transition: all 0.3s ease;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .phone-number:hover {
            background: rgba(76, 175, 80, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.2);
            color: #66BB6A;
        }

        .development-note {
            color: #f3eeee;
            text-align: center;
            font-size: 16px;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 193, 7, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(255, 193, 7, 0.2);
            backdrop-filter: blur(10px);
        }

        .back-link {
            display: inline-block;
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .logo-name{
            size: 50px;
            color: #ece5e5ff;
        }

        @media (max-width: 600px) {
            .contact-container {
                padding: 20px;
                margin: 0 10px;
            }
            
            .logo-section img {
                max-width: 100px;
            }

            .contact-name {
                font-size: 15px;
            }

            .phone-number {
                font-size: 14px;
                padding: 6px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="logo-section">
        <img src="Photos/YD_Logo_BG.png" alt="UTSAV Logo">
        <div class="logo-name">Yuvadeepti SMYM Thrickodithanam Forane</div>
    </div>
    
    <div class="contact-container">
        <div class="contact-item">
            <div class="contact-name">Fr. Prince Ethirettukudilil</div>
            <div class="contact-role">Director</div>
            <a href="tel:+919605521309" class="phone-number">📞 9605521309</a>
        </div>

        <div class="contact-item">
            <div class="contact-name">Deepu Aprem J Marattukalam</div>
            <div class="contact-role">President</div>
            <a href="tel:+91960553223" class="phone-number">📞 960553223</a>
        </div>

        <div class="contact-item">
            <div class="contact-name">Hima Tresa Jobi</div>
            <div class="contact-role">Deputy President</div>
            <a href="tel:+919764538" class="phone-number">📞 97645385</a>
        </div>
    </div>
    
    <a href="index.php" class="back-link">← Go back to home</a>
</body>
</html>