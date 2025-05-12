<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="flex justify-between items-center flex-wrap px-12 py-4 bg-[#9C9FE5] shadow-md">
  <a class="text-white text-3xl font-bold no-underline" href="/ITCS333-project7/index.php">UniHub</a>
  <div class="flex items-center">
    <ul class="flex flex-wrap list-none gap-8 mt-4 md:mt-0">
      <li><a href="/ITCS333-project7/event calander/event.html" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">Calendar</a></li>
      <li><a href="/ITCS333-project7/study-group/study-groups.php" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">Study Groups</a></li>
      <li><a href="/ITCS333-project7/course reviews/CourseReviews.html" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">Course Reviews</a></li>
      <li><a href="/ITCS333-project7//Campus-News/index.html" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">News</a></li>
      <li><a href="/ITCS333-project7//Course Notes/index.html" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">Notes</a></li>
      <li><a href="/ITCS333-project7//StudentMarketPlace/index.html" class="text-white font-medium hover:text-[#7c7fd9] transition-colors">Marketplace</a></li>
      
      <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Logout Button -->
        <li>
          <a href="logout.php" class="bg-white text-[#7c7fd9] font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Log out</a>
        </li>
      <?php else: ?>
        <!-- Login Button -->
        <li>
          <a href="/ITCS333-project7/login.php" class="bg-white text-[#7c7fd9] font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Login</a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>