<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .box {
            background: white;
            padding: 2rem;
            width: 320px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        input {
            width: 80%;
            padding: 12px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 90%;
            padding: 12px;
            margin-top: 10px;
            background: black;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background: white;
            color: black;
            border: 1px solid black;
            transition: 0.3s ease-in;
        }
    </style>
</head>
<body>


<div class="box">



    <h2>Admin Login</h2>
    <?php if (isset($_GET['timeout'])): ?>
    <p style="margin: 0 auto; text-align: center; width: 90%; font-size: 14px; color:white; border-radius:5px; padding:5px; background-color:#c22929">You have been logged out due to inactivity.</p>
<?php endif; ?>
    <form method="POST" action="check_admin.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    
</div>

</body>
</html>
