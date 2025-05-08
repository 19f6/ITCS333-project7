<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>UniHub | Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"/>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4">
<?php
  $error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
  unset($_SESSION['login_error']);
?>
  <main class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
    <h1 class="text-2xl font-bold text-center text-[#9C9FE5] mb-6">Welcome Back</h1>

    <!-- Error Message Container -->
    <?php if(!empty($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
      <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
    </div>
    <?php endif; ?>

    <!-- LOGIN FORM -->
    <form action="./backend/handlers/login.php" method="POST" class="space-y-5">
      <div>
        <label for="email" class="block mb-1 font-medium text-slate-700">Email</label>
        <input type="email" id="email" name="email" required
               class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9C9FE5]"/>
      </div>
      <div>
        <label for="password" class="block mb-1 font-medium text-slate-700">Password</label>
        <input type="password" id="password" name="password" required
               class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9C9FE5]"/>
      </div>

      <button type="submit"
              class="w-full bg-[#9C9FE5] hover:bg-[#7c7fd9] text-white py-2 rounded-lg transition duration-200 font-semibold">
        Login
      </button>
    </form>

    <p class="text-center text-sm text-slate-600 mt-4">
      Don't have an account?
      <a href="register.php" class="text-[#7c7fd9] hover:underline">Register</a>
    </p>
  </main>

</body>
</html>