<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Group Finder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="js/fetchGroups.js" defer></script>
    <script src="js/filtering.js" defer></script>
    <script type="module" src="js/createGroup.js" defer></script>
    <script src="js/nav.js" defer></script>
    <script src="js/tailwind.js" defer></script>
</head>
<body class="bg-background font-poppins text-textDark">
    <!-- Navbar -->
    <?php include '../nav.php'; ?>
    
    <div class="flex flex-col items-start">
        <div class="w-full px-5 md:px-20 my-6">
            <h1 class="text-2xl font-semibold text-textDark">Study Group Finder</h1>
        </div>

        <div class="flex flex-col mx-auto w-full px-4">
            <div class="w-full">
                <div class="flex mb-6 border-b-2 border-borderColor">
                    <div class="px-8 py-4 cursor-pointer transition-all duration-300 border-b-3 border-primary font-semibold">Available Groups</div>
                </div>
                
                <!-- Filteration -->
                <div class="flex flex-col md:flex-row flex-wrap gap-4 mb-6">
                    <select class="p-2 border border-borderColor rounded flex-1" id="filter-college">
                        <option value="">Select a college</option>
                    </select>
                    <select class="p-2 border border-borderColor rounded flex-1" id="filter-course">
                        <option value="">All Courses</option>
                    </select>
                    <select class="p-2 border border-borderColor rounded flex-1" id="filter-location">
                        <option value="">All Locations</option>
                        <option value="Online">Online</option>
                        <option value="In-person">In-person</option>
                        <option value="Both">Both</option>
                    </select>
                    <input type="text" id="search" placeholder="Search by keyword..." class="p-2 border border-borderColor rounded md:flex-2">
                </div>

                <!-- Groups  -->
                <div id="groups-container">
                </div>
                <div id="loading-spinner" class="flex justify-center items-center py-6 hidden">
                    <div class="animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent"></div>
                </div>
                  
                <!-- Pagination -->
                <div id="pagination" class="flex justify-center items-center mt-5 gap-2 flex-wrap">
                    <a href="#" class="text-textDark px-4 py-2 no-underline transition-colors rounded hover:bg-gray-200">&laquo;</a> 
                    <a href="#" class="bg-primary text-white px-4 py-2 no-underline rounded">1</a>
                    <a href="#" class="text-textDark px-4 py-2 no-underline transition-colors rounded hover:bg-gray-200">2</a>
                    <a href="#" class="text-textDark px-4 py-2 no-underline transition-colors rounded hover:bg-gray-200">3</a>
                    <a href="#" class="text-textDark px-4 py-2 no-underline transition-colors rounded hover:bg-gray-200">4</a>
                    <a href="#" class="text-textDark px-4 py-2 no-underline transition-colors rounded hover:bg-gray-200">5</a>
                    <a href="#" class="text-textDark px-4 py-2 no-underline transition-colors rounded hover:bg-gray-200">6</a>
                    <a href="#" class="text-textDark px-4 py-2 no-underline transition-colors rounded hover:bg-gray-200">&raquo;</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- button to create group -->
    <button id="create-group-btn" class="fixed bottom-8 right-8 bg-primary text-white p-4 rounded-full shadow-lg hover:bg-secondary transition-colors duration-300 flex items-center justify-center w-14 h-14 z-20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
    </button>
    
    <!-- Create Group -->
    <div id="create-group-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full max-h-90vh overflow-y-auto mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold">Create a Study Group</h3>
                <button id="close-modal" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="../backend/handlers/study-groups/createGroup.php" method="POST" id="create-form" class="grid gap-4">
                <div>
                    <label for="group-name" class="after:content-['_*'] after:text-red-500">Group Name</label>
                    <input type="text" id="group-name" name="group-name" placeholder="E.g., ITCS317 Midterm prep" required class="w-full p-2 border border-borderColor rounded mt-1">
                </div>
                <div>
                    <label for="college" class="after:content-['_*'] after:text-red-500">College</label>
                    <select id="college" name="college" required class="w-full p-2 border border-borderColor rounded mt-1">
                        <option value="">Select a college</option>
                        <option value="" disabled="" hidden="" selected="">Please Choose...</option> 
                        <option value="1"> College of Information Technology </option> 
                        <option value="2"> College of Arts </option> 
                        <option value="3"> College of Law </option>
                        <option value="4"> College of Engineering </option>
                        <option value="5"> College of Physical Education </option>
                        <option value="6"> College of Health and Sport Sciences </option>
                        <option value="7"> Languages Institute </option>
                        <option value="8"> College of Applied Studies </option>
                        <option value="9"> College of Science </option> 
                        <option value="10"> College of Business Administration </option>
                    </select>
                </div>

                <div>
                    <label for="course" class="after:content-['_*'] after:text-red-500">Course</label>
                    <input type="text" id="course" name="course" required class="w-full p-2 border border-borderColor rounded mt-1">
                </div>
                <div>
                    <label for="location-type" class="after:content-['_*'] after:text-red-500">Location</label>
                    <select id="location-type" name="location-type" class="w-full p-2 border border-borderColor rounded mt-1">
                        <option value="online">Online</option>
                        <option value="in-person">In-person</option>
                        <option value="Both">Both</option>
                    </select>
                    <input type="text" id="location" name="location" placeholder="Location details (if in-person)" class="w-full p-2 border border-borderColor rounded mt-2">
                </div>
                <div>
                    <label for="description" class="after:content-['_*'] after:text-red-500">Description</label>
                    <textarea id="description" name="description" rows="4" placeholder="Describe your study group, learning method, meeting frequency, etc." class="w-full p-2 border border-borderColor rounded mt-1"></textarea>
                </div>
                <div>
                    <label for="max-members">Maximum Members</label>
                    <input type="number" id="max-members" name="max-members" min="3" max="50" value="6" class="w-full p-2 border border-borderColor rounded mt-1">
                </div>

                <div id="form-errors" class="text-red-500 mb-4 hidden"></div>
                <div class="flex gap-2 mt-2">
                    <button type="submit" class="bg-primary text-textLight py-3 border-none rounded cursor-pointer font-semibold transition-colors hover:bg-secondary flex-1">Create Group</button>
                    <button type="button" id="cancel-modal" class="bg-gray-200 text-textDark py-3 border-none rounded cursor-pointer font-semibold transition-colors hover:bg-gray-300 flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>