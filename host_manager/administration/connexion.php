<?php
session_start();
require_once("../configuration/base_donnees.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin["password"])) {
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_username"] = $admin["username"];
            header("Location: tableau_bord.php");
            exit();
        } else {
            $error = "Login ou mot de passe incorrect";
        }
    } else {
        $error = "Veuillez remplir tous les champs";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: 'Inter', Arial, sans-serif;
  background: 
    linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)),
    url('../assets/bg.jpeg') center/cover no-repeat;
}

.box {
  width: 360px;
  background: rgba(30, 41, 59, 0.8);
  backdrop-filter: blur(12px);
  padding: 32px 28px;
  border-radius: 16px;
  color: #f1f5f9;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

h2 {
  text-align: center;
  margin-bottom: 24px;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 24px;
  color: #ffffff;
  text-transform: uppercase;
}

.input {
  position: relative;
  margin-bottom: 16px;
}

.input i.fa-user,
.input i.fa-lock {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 14px;
}

.input input {
  width: 100%;
  padding: 12px 14px 12px 40px;
  border: 1px solid #334155;
  border-radius: 10px;
  outline: none;
  background: #f1f2f5;
  color: #0a0b0c;
  font-size: 14px;
  transition: 0.2s;
}

.input input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

.input input::placeholder {
  color: #64748b;
}

.eye {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #94a3b8;
  font-size: 14px;
}

.eye:hover {
  color: #0b0c0c;
}

button {
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 10px;
  background: #4f46e5;
  color: white;
  cursor: pointer;
  margin-top: 8px;
  font-weight: 500;
  font-size: 14px;
  transition: 0.2s;
}

button:hover {
  background: #5851e7;
}

.error {
  background: #7f1d1d;
  padding: 10px;
  border-radius: 8px;
  text-align: center;
  margin-bottom: 16px;
  font-size: 13px;
  color: #fecaca;
}
</style>
</head>

<body>

<div class="box">

<h2>Login</h2>

<?php if($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST">

<div class="input">
<i class="fa fa-user"></i>
<input type="text" name="username" placeholder="Username" required autocomplete="username">
</div>

<div class="input">
<i class="fa fa-lock"></i>
<input type="password" id="password" name="password" placeholder="Password" required autocomplete="current-password">
<i class="fa fa-eye eye" id="eyeIcon" onclick="toggle()"></i>
</div>

<button type="submit">Login</button>

</form>

</div>

<script>
function toggle(){
  let p = document.getElementById("password");
  let icon = document.getElementById("eyeIcon");
  if (p.type === "password") {
    p.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    p.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}
</script>

</body>
</html>
