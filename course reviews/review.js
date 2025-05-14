document.addEventListener('DOMContentLoaded', () => {
    const createButton = document.getElementById('create-review-btn');
    const createModal = document.getElementById('create-course-modal');
    const closeModalButton = document.getElementById('close-modal');
    const createForm = document.getElementById('create-form');
    const reviewContainer = document.getElementById('course-container');
    let currentEditId = null;
    let currentPage = 1;

    createButton.addEventListener('click', () => {
        createModal.classList.remove('hidden');
        currentEditId = null;
    });

    closeModalButton.addEventListener('click', () => {
        createModal.classList.add('hidden');
    });

    createForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(createForm);
        if (currentEditId) {
            formData.append('id', currentEditId);
        }

        try {
            const response = await fetch('../backend/handlers/review/createcoursereview.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            alert(result.message);
            if (result.success) {
                createModal.classList.add('hidden');
                createForm.reset();
                fetchReviews();
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while submitting your review.');
        }
    });

    document.getElementById('search-btn').addEventListener('click', () => {
        currentPage = 1;
        fetchReviews();
    });

    async function fetchReviews() {
        try {
            const response = await fetch(`../backend/handlers/review/fetchReview.php?page=${currentPage}`);
            const result = await response.json();
            if (result.success) {
                reviewContainer.innerHTML = '';

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

                createPagination(result.totalPages);
            } else {
                alert('Failed to load reviews.');
            }
        } catch (error) {
            alert('An error occurred while fetching reviews.');
        }
    }

    function createPagination(totalPages) {
        const paginationContainer = document.getElementById('pagination');
        paginationContainer.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.innerText = i;
            button.classList.add('text-textDark', 'px-4', 'py-2', 'border', 'rounded', 'mx-1');
            button.onclick = () => {
                currentPage = i;
                fetchReviews();
            };
            paginationContainer.appendChild(button);
        }
    }

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
                currentEditId = id;
            } else {
                alert('Failed to load review for editing.');
            }
        } catch (error) {
            alert('An error occurred while fetching the review.');
        }
    };

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
                    fetchReviews();
                }
            } catch (error) {
                alert('An error occurred while deleting the review.');
            }
        }
    };

    fetchReviews();
});