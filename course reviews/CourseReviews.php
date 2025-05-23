<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Reviews</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <script src="review.js" defer></script>
  <script src="tailwindreview.js"></script>
  <!-- <link rel="stylesheet" href="CourseReviews.css"> -->
</head>

<body class="bg-background font-poppins text-textDark">

  <nav id="navbar" class="flex w-full justify-between items-center flex-wrap p-4 md:px-12 shadow-md bg-primary">
    <div class="flex justify-between items-center w-full md:w-auto">
        <a class="home text-2xl font-semibold text-textLight no-underline" href="#home">UniHub</a>
        <button id="menu-toggle" class="md:hidden text-textLight focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
    <ul id="menu-items" class="hidden md:flex flex-col md:flex-row list-none p-0 m-0 gap-2 md:gap-8 w-full md:w-auto mt-4 md:mt-0">
      <li><a href="../event calander/event.html" class="text-textLight no-underline py-2 block md:inline transition-colors hover:text-secondary">Calendar</a></li>
      <li><a href="../study-group/study-groups.html" class="text-textLight no-underline py-2 block md:inline transition-colors hover:text-secondary">Study Groups</a></li>
      <li><a href="../course reviews/CourseReviews.html" class="text-textLight no-underline py-2 block md:inline transition-colors hover:text-secondary">Course Reviews</a></li>
      <li><a href="../Campus-News/index.html" class="text-textLight no-underline py-2 block md:inline transition-colors hover:text-secondary">News</a></li>
      <li><a href="../Course Notes/index.html" class="text-textLight no-underline py-2 block md:inline transition-colors hover:text-secondary">Notes</a></li>
      <li><a href="../StudentMarketPlace/index.html" class="text-textLight no-underline py-2 block md:inline transition-colors hover:text-secondary">Marketplace</a></li>
    </ul>
  </nav>

  <div class="flex flex-col items-start">
    <div class="w-full px-5 md:px-20 my-6">
      <h1 class="text-2xl font-semibold text-textDark">Course Reviews</h1>
    </div>
    <div class="flex flex-col mx-auto w-full px-4">
      <div class="flex mb-6 border-b-2 border-borderColor">
        <div class="px-8 py-4 cursor-pointer transition-all duration-300 border-b-3 border-primary font-semibold">Reviews</div>
      </div>

      <div class="flex flex-col md:flex-row flex-wrap gap-4 mb-6">
        <input type="text" id="search" placeholder="Search for courses or colleges..." />
        <button id="search-btn">Search</button>
      </div>

      <div id="course-container"></div>

      <div id="loading-spinner" class="flex justify-center items-center py-6 hidden">
        <div class="animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent"></div>
      </div>

      <div id="pagination" class="flex justify-center items-center mt-5 gap-2 flex-wrap">
        <!-- Pagination buttons will be dynamically generated here -->
      </div>
    </div>
  </div>

  <button id="create-review-btn" class="fixed bottom-8 right-8 bg-primary text-white p-4 rounded-full shadow-lg hover:bg-secondary transition-colors duration-300 flex items-center justify-center w-14 h-14 z-20">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
    </svg>
  </button>

  <div id="create-course-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full max-h-90vh overflow-y-auto mx-4">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-semibold">Create a Course Review</h3>
        <button id="close-modal" class="text-gray-500 hover:text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <form action="../backend/handlers/createcoursereview.php" method="POST" id="create-form" class="grid gap-4">
        <div>
          <label for="course-name" class="after:content-['_*'] after:text-red-500">Course Name</label>
          <input type="text" id="course-name" name="course" placeholder="E.g., ITCS317" required class="w-full p-2 border border-borderColor rounded mt-1">
        </div>
        <div>
          <label for="college" class="after:content-['_*'] after:text-red-500">College</label>
          <select id="college" name="college" required class="w-full p-2 border border-borderColor rounded mt-1">
            <option value="" disabled selected hidden>Please Choose...</option>
            <option value="1">College of Information Technology</option>
            <option value="2">College of Arts</option>
            <option value="3">College of Law</option>
            <option value="4">College of Engineering</option>
            <option value="5">College of Physical Education</option>
            <option value="6">College of Health and Sport Sciences</option>
            <option value="7">Languages Institute</option>
            <option value="8">College of Applied Studies</option>
            <option value="9">College of Science</option>
            <option value="10">College of Business Administration</option>
          </select>
        </div>
        <div>
          <label for="rating" class="after:content-['_*'] after:text-red-500">Rating</label>
          <select id="rating" name="rating" required class="w-full p-2 border border-borderColor rounded mt-1">
            <option value="" disabled selected hidden>Select a rating</option>
            <option value="1">1 - Poor</option>
            <option value="2">2 - Fair</option>
            <option value="3">3 - Average</option>
            <option value="4">4 - Good</option>
            <option value="5">5 - Excellent</option>
          </select>
        </div>
        <div>
          <label for="description" class="after:content-['_*'] after:text-red-500">Description</label>
          <textarea id="description" name="description" rows="4" placeholder="Add your comments." required class="w-full p-2 border border-borderColor rounded mt-1"></textarea>
        </div>
        <div class="flex gap-2 mt-2">
          <button type="submit" class="bg-primary text-textLight py-3 border-none rounded cursor-pointer font-semibold transition-colors hover:bg-secondary flex-1">Create Review</button>
          <button type="button" id="cancel-modal" class="bg-gray-200 text-textDark py-3 border-none rounded cursor-pointer font-semibold transition-colors hover:bg-gray-300 flex-1">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <div id="add-review-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full mx-4">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold">Add Your Review</h3>
        <button onclick="document.getElementById('add-review-modal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
          ✕
        </button>
      </div>
      <div class="mb-2">
        <strong>Course:</strong> <span id="review-course-title"></span><br>
        <strong>College:</strong> <span id="review-course-college"></span><br>
        <strong>Description:</strong> <p id="review-course-description" class="mt-1"></p>
      </div>
      <input type="hidden" id="review-course-id">
      <textarea id="user-comment" rows="4" class="w-full p-2 border border-borderColor rounded mt-2" placeholder="Write your comment here..."></textarea>
      <button class="mt-4 bg-primary text-white py-2 px-4 rounded hover:bg-secondary" onclick="submitReview()">Submit Review</button>
    </div>
  </div>

  <div id="review-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <button id="close-review-modal" class="text-gray-500 float-right">X</button>
      <div id="review-course-details" class="mb-4"></div>

      <h4 class="text-lg font-semibold mb-2">Existing Reviews</h4>
      <div id="review-comments" class="mb-4 space-y-2"></div>

      <h4 class="text-lg font-semibold mb-2">Add Your Review</h4>
      <form id="review-form">
        <label class="block mb-2">
          Rating (1–10):
          <input type="number" min="1" max="10" id="review-rating" class="border p-2 w-full" required />
        </label>
        <label class="block mb-2">
          Comment:
          <textarea id="review-comment" class="border p-2 w-full" rows="4" required></textarea>
        </label>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded mt-2">Submit Review</button>
      </form>
    </div>
  </div>
</body>
</html>