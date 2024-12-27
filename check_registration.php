<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Selection</title>
    <link rel="stylesheet" href="styles2.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .container h1 {
            margin-bottom: 20px;
        }
        .container button {
            padding: 15px 25px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
            transition: background 0.3s ease;
        }
        .btn-login {
            background-color: #007bff;
            color: white;
        }
        .btn-login:hover {
            background-color: #0056b3;
        }
        .btn-register {
            background-color: #28a745;
            color: white;
        }
        .btn-register:hover {
            background-color: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Are You Already Registered?</h1>
        <button class="btn-login" onclick="location.href='login.php'">Yes, I am Registered</button>
        <button class="btn-register" onclick="location.href='register.php'">No, Register Me</button>
    </div>
</body>
</html>
