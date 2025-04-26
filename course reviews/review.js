const API_URL = "https://680d187ec47cb8074d8f8bc6.mockapi.io/reviews";

let allCourses = [];
let currentPage = 1;
const itemsPerPage = 3;
let selectedRating = 0;

const courseContainer = document.getElementById("course-container");
const filterCollege = document.getElementById("filter-college");
const filterCourse = document.getElementById("filter-course");
const searchInput = document.getElementById("search");
const createForm = document.getElementById("create-form");

async function fetchCourses() {
    showLoading();
    try {
        const response = await fetch(API_URL);
        if (!response.ok) {
            throw new Error("Failed to fetch courses");
        }
        const data = await response.json();
        allCourses = data;
        renderCourses();
        populateFilterCourses();
    } catch (error) {
        showError(error.message);
    }
}

function showLoading() {
    courseContainer.innerHTML = "<p>Loading courses...</p>";
}

function showError(message) {
    courseContainer.innerHTML = `<p class="text-red-500">${message}</p>`;
}

function renderCourses() {
    const filtered = applyFilters(allCourses);
    const paginated = applyPagination(filtered);

    courseContainer.innerHTML = "";

    if (paginated.length === 0) {
        courseContainer.innerHTML = "<p>No courses found.</p>";
        return;
    }

    paginated.forEach(course => {
        const courseCard = document.createElement("div");
        courseCard.className = "course-card p-4 bg-white rounded shadow";
        courseCard.innerHTML = `
            <div class="course-header mb-2">
                <h3 class="course-title font-bold text-lg">${course.course}</h3>
                <span class="course-subject text-gray-500">${course.college}</span>
            </div>
            <div class="course-details mb-2">
                <p><strong>Description:</strong> ${course.review}</p>
            </div>
            <div class="stars text-yellow-400 text-2xl mb-2">
                ${"★".repeat(course.rating)}${"☆".repeat(5 - course.rating)}
            </div>
            <button class="join-button bg-blue-500 hover:bg-blue-600 text-white py-1 px-4 rounded">Add your review</button>
        `;
        courseContainer.appendChild(courseCard);
    });

    renderPagination(filtered.length);
}

function applyFilters(courses) {
    const college = filterCollege.value;
    const search = searchInput.value.toLowerCase();

    return courses.filter(course => {
        const matchesCollege = !college || course.college === college;
        const matchesSearch = course.course.toLowerCase().includes(search) ||
            course.review.toLowerCase().includes(search);
        return matchesCollege && matchesSearch;
    });
}

function applyPagination(courses) {
    const start = (currentPage - 1) * itemsPerPage;
    return courses.slice(start, start + itemsPerPage);
}

function renderPagination(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const paginationDiv = document.createElement("div");
    paginationDiv.className = "flex gap-2 mt-4 justify-center";

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        btn.className = `px-3 py-1 rounded ${i === currentPage ? "bg-blue-500 text-white" : "bg-gray-200"}`;
        btn.addEventListener("click", () => {
            currentPage = i;
            renderCourses();
        });
        paginationDiv.appendChild(btn);
    }

    courseContainer.appendChild(paginationDiv);
}

function populateFilterCourses() {
    const courseNames = [...new Set(allCourses.map(c => c.course))];
    filterCourse.innerHTML = `<option value="">All Courses</option>`;
    courseNames.forEach(name => {
        const option = document.createElement("option");
        option.value = name;
        option.textContent = name;
        filterCourse.appendChild(option);
    });
}

filterCollege.addEventListener("change", () => {
    currentPage = 1;
    renderCourses();
});

filterCourse.addEventListener("change", () => {
    currentPage = 1;
    searchInput.value = filterCourse.value;
    renderCourses();
});

searchInput.addEventListener("input", () => {
    currentPage = 1;
    renderCourses();
});

createForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const courseName = document.getElementById("course-name").value.trim();
    const college = document.getElementById("subject").value;
    const level = document.getElementById("level").value;
    const review = document.getElementById("description").value.trim();

    if (!courseName || !college || !level || !review || selectedRating === 0) {
        alert("Please fill all fields and select a rating!");
        return;
    }

    const newReview = {
        course: courseName,
        college: college,
        review: review,
        rating: selectedRating
    };

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(newReview),
        });

        if (!response.ok) {
            throw new Error("Failed to submit review");
        }

        alert(`Review created successfully!\nRating: ${selectedRating} Stars`);
        createForm.reset();
        selectedRating = 0;
        fetchCourses();
    } catch (error) {
        alert(error.message);
    }
});

const starInputs = document.querySelectorAll("input[name='rating']");
starInputs.forEach(star => {
    star.addEventListener("change", () => {
        selectedRating = parseInt(star.value, 10);
    });
});

fetchCourses();
