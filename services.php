<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="Photos/YD_Logo_BG.png" type="image/png">
    <title>UTSAV-Services</title>
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
        }

        .img {
            text-align: center;
            size: 30%;
            margin-bottom: 30px;
        }

        .img img {
            max-width: 400px;
            width: 90%;
            height: auto;
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.2));
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

        @media (max-width: 600px) {
            .title-text {
                font-size: 20px;
            }
            
            .title-logo {
                width: 40px;
                height: 40px;
            }
            
            .img img {
                max-width: 300px;
            }
        }

        p{
            color: #f3eeeeff;
        }
    </style>
</head>
<body>
    
    
    <div class="img">
        <img src="Photos/under_construction-removebg-preview.png" alt="Under Construction">
        <p>This site is under development.Come back soon..</p>
    </div>
    
    <a href="index.php" class="back-link">Go back to home</a>
</body>
</html>