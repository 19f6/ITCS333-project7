<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Group Detail | UniHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="js/tailwind.js" defer></script>
    <script src="js/fetchdetails.js" defer></script>
    <script src="js/groupActions.js" defer></script>
</head>
<body class="bg-background font-poppins text-textDark">
    <!-- Navbar -->
    <?php include '../nav.php'; ?>
    
    <div class="content-container max-w-7xl mx-auto p-6 flex flex-col md:flex-row gap-10 items-start">
        <div class="flex-1 bg-white rounded-lg shadow-md p-6">
            <a href="study-groups.php" class="inline-flex items-center text-textDark no-underline mb-5 font-medium hover:text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                    <path d="M19 12H5"></path>
                    <path d="M12 19l-7-7 7-7"></path>
                </svg>
                Back to Study Groups
            </a>
            <div class="flex justify-between items-start mb-5 flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-textDark m-0"></h1>
                    <span class="bg-primary text-textLight px-4 py-1 rounded-full text-base inline-block"></span>
                </div>
                <div id="btns" class="flex gap-2 hidden">
                    <button id="editBtn" class="p-2 rounded bg-gray-500 text-white font-semibold cursor-pointer transition-all hover:bg-gray-600 border-none flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button id="deleteBtn" class="p-2 rounded bg-gray-400 text-white font-semibold cursor-pointer transition-all hover:bg-gray-500 border-none flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
                <div class="bg-background p-3 rounded-md">
                    <strong class="block text-sm text-gray-500 mb-1">College</strong>
                    <span id="group-college"></span>
                </div>
                <div class="bg-background p-3 rounded-md">
                    <strong class="block text-sm text-gray-500 mb-1">Location</strong>
                    <span id="group-location"></span>
                </div>
                <div class="bg-background p-3 rounded-md">
                    <strong class="block text-sm text-gray-500 mb-1">Members</strong>
                    <span id="group-members-count"></span>
                </div>
                <div class="bg-background p-3 rounded-md">
                    <strong class="block text-sm text-gray-500 mb-1">Created By</strong>
                    <span id="group-creator"></span>
                </div>

                <div class="bg-background p-3 rounded-md">
                    <strong class="block text-sm text-gray-500 mb-1">Created On</strong>
                    <span id="group-created-date"></span>
                </div>
            </div>
            
            <div class="my-6">
                <h3 class="text-xl font-semibold mb-4">Description</h3>
                <p id="group-description"></p>
            </div>
            
            <div class="my-6">
                <h3 class="text-xl font-semibold mb-4">Members</h3>
                <div id="members-container" class="flex flex-wrap gap-2">
                <div class="flex items-center bg-background py-2 px-4 rounded-full">
                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center mr-2 font-semibold">...</div>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="md:w-1/3 bg-white rounded-lg p-5 shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Comments (<span id="comments-count">0</span>)</h2>
            </div>
            
            <div id="comments-container">
            <div class="bg-white rounded-lg shadow-sm p-4 mb-4" id="commentsContainer">
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold"></span>
                        <span class="text-gray-500 text-sm"></span>
                    </div>
                    <p></p>
                </div>
            </div>
            
            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-2">Add a Comment</h3>
                <form id="comment-form">
                    <textarea id="comment-content" placeholder="Write your comment here..." class="w-full p-4 border border-borderColor rounded-lg min-h-32 mb-4 font-poppins resize-y" required></textarea>
                    <button type="submit" class="bg-primary text-textLight border-none py-2 px-5 rounded cursor-pointer font-semibold transition-colors hover:bg-secondary flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                        Post Comment
                    </button>
                </form>
            </div>
        </div>
    </div>
    

    <!-- Edit Group Modal -->
    <div id="edit-group-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full max-h-90vh overflow-y-auto mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold">Edit Study Group</h3>
                <button id="close-edit-modal" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="../backend/handlers/study-groups/updateGroup.php" method="POST" id="edit-form" class="grid gap-4">
                <input type="hidden" id="edit-group-id" name="group-id">
                
                <div>
                    <label for="edit-group-name" class="after:content-['_*'] after:text-red-500">Group Name</label>
                    <input type="text" id="edit-group-name" name="group-name" placeholder="E.g., ITCS317 Midterm prep" required class="w-full p-2 border border-borderColor rounded mt-1">
                </div>
                
                <div>
                    <label for="edit-college" class="after:content-['_*'] after:text-red-500">College</label>
                    <select id="edit-college" name="college" required class="w-full p-2 border border-borderColor rounded mt-1">
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
                    <label for="edit-course" class="after:content-['_*'] after:text-red-500">Course</label>
                    <input type="text" id="edit-course" name="course" required class="w-full p-2 border border-borderColor rounded mt-1">
                </div>
                
                <div>
                    <label for="edit-location-type" class="after:content-['_*'] after:text-red-500">Location</label>
                    <select id="edit-location-type" name="location-type" class="w-full p-2 border border-borderColor rounded mt-1">
                        <option value="online">Online</option>
                        <option value="in-person">In-person</option>
                        <option value="Both">Both</option>
                    </select>
                    <input type="text" id="edit-location" name="location" placeholder="Location details (if in-person)" class="w-full p-2 border border-borderColor rounded mt-2">
                </div>
                
                <div>
                    <label for="edit-description" class="after:content-['_*'] after:text-red-500">Description</label>
                    <textarea id="edit-description" name="description" rows="4" placeholder="Describe your study group, learning method, meeting frequency, etc." class="w-full p-2 border border-borderColor rounded mt-1"></textarea>
                </div>
                
                <div>
                    <label for="edit-max-members">Maximum Members</label>
                    <input type="number" id="edit-max-members" name="max-members" min="3" max="50" class="w-full p-2 border border-borderColor rounded mt-1">
                </div>

                <div id="edit-form-errors" class="text-red-500 mb-4 hidden"></div>
                
                <div class="flex gap-2 mt-2">
                    <button type="submit" class="bg-primary text-textLight py-3 border-none rounded cursor-pointer font-semibold transition-colors hover:bg-secondary flex-1">Update Group</button>
                    <button type="button" id="cancel-edit-modal" class="bg-gray-200 text-textDark py-3 border-none rounded cursor-pointer font-semibold transition-colors hover:bg-gray-300 flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
</body>
</html>