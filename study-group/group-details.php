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
                <div class="flex gap-2">
                    <button class="p-2 rounded bg-gray-500 text-white font-semibold cursor-pointer transition-all hover:bg-gray-600 border-none flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button class="p-2 rounded bg-gray-400 text-white font-semibold cursor-pointer transition-all hover:bg-gray-500 border-none flex items-center justify-center">
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
                    <!-- Members will be added dynamically here -->
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
                <!-- Comments will be added dynamically here -->
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

    
</body>
</html>