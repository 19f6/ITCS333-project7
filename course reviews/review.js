document.addEventListener('DOMContentLoaded', () => {
    const createButton = document.getElementById('create-review-btn');
    const createModal = document.getElementById('create-course-modal');
    const closeModalButton = document.getElementById('close-modal');
    const createForm = document.getElementById('create-form');
    let currentEditId = null;

    // Show modal on button click
    createButton.addEventListener('click', () => {
        createModal.classList.remove('hidden');
        currentEditId = null; // Reset for new review
    });

    // Close modal
    closeModalButton.addEventListener('click', () => {
        createModal.classList.add('hidden');
    });

    // Handle form submission
    createForm.addEventListener('submit', async (event) => {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(createForm);
        if (currentEditId) {
            formData.append('id', currentEditId); // Add review ID for editing
        }

        try {
            const response = await fetch('../backend/handlers/review/createcoursereview.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            alert(result.message); // Notify user of success/failure
            if (result.success) {
                createModal.classList.add('hidden'); // Close modal on success
                createForm.reset(); // Reset form fields
                fetchReviews(); // Refresh the reviews list
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while submitting your review.');
        }
    });

    // Search functionality
    document.getElementById('search-btn').addEventListener('click', async () => {
        const searchQuery = document.getElementById('search').value;
        try {
            const response = await fetch(`../backend/handlers/review/fetchReview.php?search=${encodeURIComponent(searchQuery)}`);
            const result = await response.json();

            if (result.success) {
                const reviewContainer = document.getElementById('course-container');
                reviewContainer.innerHTML = ''; // Clear previous content

                result.data.forEach(review => {
                    const reviewElement = document.createElement('div');
                    reviewElement.classList.add('review-item', 'border', 'p-4', 'mb-4', 'rounded');
                    reviewElement.innerHTML = `
                        <h4 class="font-semibold">${review.course_title} (${review.college})</h4>
                        <p>${review.description}</p>
                        <p class="text-gray-500">Rating: ${review.rating}/5</p>
                        <p class="text-gray-400 text-sm">${new Date(review.created_at).toLocaleString()}</p>
                        <button onclick="editReview(${review.id})" class="text-blue-500">Edit</button>
                        <button onclick="deleteReview(${review.id})" class="text-red-500">Delete</button>
                    `;
                    reviewContainer.appendChild(reviewElement);
                });
            } else {
                alert('No results found.');
            }
        } catch (error) {
            alert('An error occurred while searching for reviews.');
        }
    });

    // Function to fetch and display reviews
    async function fetchReviews() {
        try {
            const response = await fetch('../backend/handlers/review/fetchReview.php');
            const result = await response.json();
            if (result.success) {
                const reviewContainer = document.getElementById('course-container');
                reviewContainer.innerHTML = ''; // Clear previous content

                result.data.forEach(review => {
                    const reviewElement = document.createElement('div');
                    reviewElement.classList.add('review-item', 'border', 'p-4', 'mb-4', 'rounded');
                    reviewElement.innerHTML = `
                        <h4 class="font-semibold">${review.course_title} (${review.college})</h4>
                        <p>${review.description}</p>
                        <p class="text-gray-500">Rating: ${review.rating}/5</p>
                        <p class="text-gray-400 text-sm">${new Date(review.created_at).toLocaleString()}</p>
                        <button onclick="editReview(${review.id})" class="text-blue-500">Edit</button>
                        <button onclick="deleteReview(${review.id})" class="text-red-500">Delete</button>
                    `;
                    reviewContainer.appendChild(reviewElement);
                });
            } else {
                alert('Failed to load reviews.');
            }
        } catch (error) {
            alert('An error occurred while fetching reviews.');
        }
    }

    // Edit Review Functionality
    window.editReview = async function(id) {
        try {
            const response = await fetch(`../backend/handlers/review/fetchReview.php?id=${id}`);
            const result = await response.json();

            if (result.success) {
                const review = result.data;
                document.getElementById('course-name').value = review.course_title;
                document.getElementById('college').value = review.college;
                document.getElementById('description').value = review.description;
                document.getElementById('rating').value = review.rating;
                createModal.classList.remove('hidden');
                currentEditId = id; // Set the ID for editing
            } else {
                alert('Failed to load review for editing.');
            }
        } catch (error) {
            alert('An error occurred while fetching the review.');
        }
    };

    // Delete Review Functionality
    window.deleteReview = async function(id) {
        if (confirm('Are you sure you want to delete this review?')) {
            try {
                const response = await fetch('../backend/handlers/review/deleteReview.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id })
                });

                const result = await response.json();
                alert(result.message);
                if (result.success) {
                    fetchReviews(); // Refresh the reviews list
                }
            } catch (error) {
                alert('An error occurred while deleting the review.');
            }
        }
    };

    // Initial fetch of reviews
    fetchReviews();
});