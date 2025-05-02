const API_URL = "https://680d187ec47cb8074d8f8bc6.mockapi.io/courses";
const REVIEWS_API = "https://680d187ec47cb8074d8f8bc6.mockapi.io/reviews";

let allCourses = [];
let filteredCourses = [];
let allReviews = [];
let currentReviewCourse = null;
let currentPage = 1;
const itemsPerPage = 4;

// DOM Elements
const courseContainer = document.getElementById("course-container");
const reviewModal = document.getElementById("review-modal");
const reviewDetails = document.getElementById("review-course-details");
const reviewComments = document.getElementById("review-comments");
const reviewForm = document.getElementById("review-form");
const reviewRating = document.getElementById("review-rating");
const reviewComment = document.getElementById("review-comment");

// Fetch courses and reviews
async function fetchCourses() {
    const spinner = document.getElementById("loading-spinner");
    const paginationContainer = document.getElementById("pagination");
    const collegeSelect = document.getElementById("filter-college");
    const courseSelect = document.getElementById("filter-course");

    const collegeOptions = new Set();
    const courseOptions = new Set();

    spinner.classList.remove("hidden");
    paginationContainer.classList.add("hidden");

    try {
        const [coursesRes, reviewsRes] = await Promise.all([
            fetch(API_URL),
            fetch(REVIEWS_API)
        ]);
        if (!coursesRes.ok || !reviewsRes.ok) throw new Error("Network error");

        allCourses = await coursesRes.json();
        allReviews = await reviewsRes.json();
        filteredCourses = [...allCourses];

        // Populate dropdowns
        allCourses.forEach((course) => {
            if (course.college) collegeOptions.add(course.college);
            if (course.title) courseOptions.add(course.title);
        });

        collegeSelect.innerHTML = '<option value="">All Colleges</option>';
        courseSelect.innerHTML = '<option value="">All Courses</option>';

        collegeOptions.forEach((college) => {
            const option = document.createElement("option");
            option.value = college;
            option.textContent = college;
            collegeSelect.appendChild(option);
        });

        courseOptions.forEach((course) => {
            const option = document.createElement("option");
            option.value = course;
            option.textContent = course;
            courseSelect.appendChild(option);
        });

        displayCourses(filteredCourses, currentPage);
        renderPagination(filteredCourses);
    } catch (error) {
        console.log("Error:", error);
        courseContainer.innerHTML = `
            <div class="p-4 bg-red-100 text-red-700 rounded-lg">
                Failed to load courses. Try again later.
            </div>`;
    } finally {
        spinner.classList.add("hidden");
        paginationContainer.classList.remove("hidden");
    }
}

// Display paginated course cards
function displayCourses(courses, page = 1) {
    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const paginated = courses.slice(start, end);

    courseContainer.innerHTML = "";

    if (paginated.length === 0) {
        courseContainer.innerHTML = "<p>No courses found.</p>";
        return;
    }

    paginated.forEach((course) => {
        const courseReviews = allReviews.filter((r) => r.course === course.title);
        const averageRating = courseReviews.length
            ? (courseReviews.reduce((sum, r) => sum + Number(r.rating), 0) / courseReviews.length).toFixed(1)
            : "No ratings yet";
        const totalReviews = courseReviews.length;

        const courseCard = document.createElement("div");
        courseCard.className = "course-card p-4 bg-white rounded shadow mb-4";
        courseCard.innerHTML = `
            <div class="mb-4">
                <h3 class="text-lg font-semibold">${course.title}</h3>
                <p><strong>College:</strong> ${course.college || "N/A"}</p>
                <p><strong>Description:</strong> ${course.description}</p>
            </div>
            <button class="review-btn bg-primary text-white px-4 py-2 rounded hover:bg-secondary">Add your review</button>
        `;

        // Handle review button click
        courseCard.querySelector(".review-btn").addEventListener("click", () => {
            currentReviewCourse = course;

            reviewDetails.innerHTML = `
                <h3 class="text-xl font-bold">${course.title}</h3>
                <p><strong>College:</strong> ${course.college}</p>
                <p><strong>Description:</strong> ${course.description}</p>
            `;

            const courseReviews = allReviews.filter((r) => r.course === course.title);
            reviewComments.innerHTML = courseReviews.length
                ? courseReviews.map(r => `<div class="border p-2 rounded"><strong>Rating:</strong> ${r.rating}<br/>${r.comment}</div>`).join("")
                : `<p>No reviews yet.</p>`;

            reviewModal.classList.remove("hidden");
        });

        courseContainer.appendChild(courseCard);
    });
}

// Handle review form submission
reviewForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const newReview = {
        course: currentReviewCourse.title,
        rating: reviewRating.value,
        comment: reviewComment.value,
        createdAt: new Date().toISOString(),
    };

    try {
        const res = await fetch(REVIEWS_API, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(newReview),
        });

        if (!res.ok) throw new Error("Failed to post review");

        const savedReview = await res.json();
        allReviews.push(savedReview);

        // Reset form
        reviewRating.value = "";
        reviewComment.value = "";

        // Refresh comment list in modal
        const updatedReviews = allReviews.filter((r) => r.course === currentReviewCourse.title);
        reviewComments.innerHTML = updatedReviews.map(r =>
            `<div class="border p-2 rounded"><strong>Rating:</strong> ${r.rating}<br/>${r.comment}</div>`
        ).join("");

        // Refresh rating on main UI
        displayCourses(filteredCourses, currentPage);
    } catch (error) {
        alert("Error posting review. Try again.");
        console.error(error);
    }
});

// Handle modal close button
document.getElementById("close-review-modal").addEventListener("click", () => {
    reviewModal.classList.add("hidden");
});

// Handle filters
document.getElementById("filter-college").addEventListener("change", applyFilters);
document.getElementById("filter-course").addEventListener("change", applyFilters);
document.getElementById("search").addEventListener("input", applyFilters);

function applyFilters() {
    const college = document.getElementById("filter-college").value;
    const course = document.getElementById("filter-course").value;
    const keyword = document.getElementById("search").value.toLowerCase();

    filteredCourses = allCourses.filter((item) => {
        return (
            (college === "" || item.college === college) &&
            (course === "" || item.title === course) &&
            (item.title.toLowerCase().includes(keyword) || item.description.toLowerCase().includes(keyword))
        );
    });

    currentPage = 1;
    displayCourses(filteredCourses, currentPage);
    renderPagination(filteredCourses);
}

// Pagination
function renderPagination(courses) {
    const paginationContainer = document.getElementById("pagination");
    paginationContainer.innerHTML = "";

    const totalPages = Math.ceil(courses.length / itemsPerPage);
    if (totalPages <= 1) return;

    const createButton = (text, handler, disabled = false) => {
        const btn = document.createElement("a");
        btn.href = "#";
        btn.innerHTML = text;
        btn.className = `px-4 py-2 rounded no-underline ${
            disabled ? "text-gray-400" : "text-textDark hover:bg-gray-200"
        }`;
        btn.onclick = (e) => {
            e.preventDefault();
            if (!disabled) handler();
        };
        return btn;
    };

    paginationContainer.appendChild(
        createButton("&laquo;", () => {
            currentPage = Math.max(currentPage - 1, 1);
            displayCourses(filteredCourses, currentPage);
            renderPagination(filteredCourses);
        }, currentPage === 1)
    );

    for (let i = 1; i <= totalPages; i++) {
        const pageBtn = createButton(i, () => {
            currentPage = i;
            displayCourses(filteredCourses, currentPage);
            renderPagination(filteredCourses);
        }, false);
        if (i === currentPage) pageBtn.classList.add("bg-primary", "text-white");
        paginationContainer.appendChild(pageBtn);
    }

    paginationContainer.appendChild(
        createButton("&raquo;", () => {
            currentPage = Math.min(currentPage + 1, totalPages);
            displayCourses(filteredCourses, currentPage);
            renderPagination(filteredCourses);
        }, currentPage === totalPages)
    );
}

// Optional: Modal for creating new courses
document.getElementById("create-review-btn").addEventListener("click", () => {
    document.getElementById("create-course-modal").classList.remove("hidden");
});
document.getElementById("close-modal").addEventListener("click", () => {
    document.getElementById("create-course-modal").classList.add("hidden");
});
document.getElementById("cancel-modal").addEventListener("click", () => {
    document.getElementById("create-course-modal").classList.add("hidden");
});
document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("menu-toggle");
    const menu = document.getElementById("menu-items");

    if (toggle && menu) {
        toggle.addEventListener("click", () => {
            menu.classList.toggle("hidden");
        });
    }
});

// Init
function initiate() {
    fetchCourses();
}

initiate();
