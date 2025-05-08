<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>UniHub</title>
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
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">

  <!-- Navbar -->
<nav class="flex justify-between items-center flex-wrap px-12 py-4 bg-[#9C9FE5] shadow-md">
  <a class="text-white text-3xl font-bold no-underline" href="../index.html">UniHub</a>
  <div class="flex items-center">
    <ul class="flex flex-wrap list-none gap-8 mt-4 md:mt-0">
      <li><a href="event calander/event.html" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">Calendar</a></li>
      <li><a href="study-group/study-groups.html" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">Study Groups</a></li>
      <li><a href="course reviews/CourseReviews.html" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">Course Reviews</a></li>
      <li><a href="Campus-News/index.html" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">News</a></li>
      <li><a href="Course Notes/index.html" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">Notes</a></li>
      <li><a href="StudentMarketPlace/index.html" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">Marketplace</a></li>
      
      <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Profile Icon -->
        <li>
          <a href="" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5.121 17.804A9.936 9.936 0 0112 15c2.21 0 4.244.72 5.879 1.928M15 11a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Profile
          </a>
        </li>
      <?php else: ?>
        <!-- Login Button -->
        <li>
          <a href="login.php" class="bg-white text-[#7c7fd9] font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Login</a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>


  <!-- Main -->
  <main class="flex-1">

    <!-- Hero Section -->
    <section class="text-center px-4 py-16 bg-slate-100">
      <h1 class="text-4xl md:text-5xl font-bold mb-4 text-slate-900">Welcome to UniHub</h1>
      <p class="text-lg max-w-2xl mx-auto text-slate-700">
        UniHub is your all-in-one platform to collaborate, learn, and thrive.
        Whether you're searching for study groups, staying up-to-date with campus
        events, or reviewing courses — we've got you covered.
      </p>
    </section>

    <!-- Cards Section -->
    <section class="flex flex-wrap justify-center gap-8 px-4 py-12">
      <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-lg transition-transform hover:-translate-y-1 w-72">
        <h3 class="text-[#9C9FE5] text-xl font-semibold mb-2">📅 Calendar</h3>
        <p class="text-slate-600">Never miss important university dates and events. Stay organized with our academic calendar.</p>
      </div>
      <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-lg transition-transform hover:-translate-y-1 w-72">
        <h3 class="text-[#9C9FE5] text-xl font-semibold mb-2">💬 Course Reviews</h3>
        <p class="text-slate-600">See what students say about different courses. Plan your semester smarter with real feedback.</p>
      </div>
      <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-lg transition-transform hover:-translate-y-1 w-72">
        <h3 class="text-[#9C9FE5] text-xl font-semibold mb-2">🧑‍🤝‍🧑 Club Activities</h3>
        <p class="text-slate-600">Connect with campus clubs, attend events, and meet people who share your passions.</p>
      </div>
    </section>

  </main>

  <!-- Footer -->
  <footer class="text-center py-8 bg-slate-100 text-slate-500 text-sm">
    &copy; 2025 UniHub. All rights reserved.
  </footer>

</body>
</html>
