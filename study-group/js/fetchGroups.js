const API_URL = "https://6800f54d81c7e9fbcc40ffec.mockapi.io/api/study";

const groupsPerPage = 5;

const groupsContainer = document.getElementById("groups-container");


let currentPage = 1;
let allGroups = [];
let filteredGroups = [];
let collegeOptions = new Set();
let courseOptions = new Set();


async function fetchStudyGroups() {
	const spinner = document.getElementById("loading-spinner");
	const paginationContainer = document.getElementById("pagination");

	spinner.classList.remove("hidden");
	paginationContainer.classList.add("hidden"); 

	try {
		const response = await fetch(API_URL);
		if (!response.ok) {
			throw new Error("Network response was not ok");
		}
		allGroups = await response.json();
		filteredGroups = [...allGroups];

		// extract colleges and courses for filters
		allGroups.forEach((group) => {
			if (group.college) collegeOptions.add(group.college);
			if (group.course) courseOptions.add(group.course);
		});

		// display 
		displayGroups(filteredGroups, currentPage);
		renderPagination(filteredGroups);
	} catch (error) {
		console.log("Error fetching study groups:", error);
		groupsContainer.innerHTML = `
            <div class="p-4 bg-red-100 text-red-700 rounded-lg">
                <p>Failed to fetch study groups. Please try again later.</p>
            </div>
        `;
	} finally {
		spinner.classList.add("hidden"); 
		paginationContainer.classList.remove("hidden"); 
	}
}

// display study groups
function displayGroups(groups, page = 1) {
	const start = (page - 1) * groupsPerPage;
	const end = start + groupsPerPage;
	const paginatedGroups = groups.slice(start, end);

	if (paginatedGroups.length === 0) {
		groupsContainer.innerHTML = `
            <div class="p-6 bg-gray-100 rounded-lg text-center">
                <p>No study groups found matching your criteria.</p>
            </div>
        `;
		return;
	}

	groupsContainer.innerHTML = "";
	paginatedGroups.forEach((group) => {
		const groupElement = document.createElement("div");
		groupElement.className = "bg-white p-6 rounded-lg shadow mb-4";

		groupElement.innerHTML = `
                <div class="flex justify-between items-center mb-4 flex-col sm:flex-row gap-2">
                    <h3 class="group-title text-lg font-semibold">${
								group.title
							}</h3>
                    <span class="bg-primary text-textLight px-3 py-1 rounded-full text-sm">${
								group.course
							}</span>
                </div>
                <div class="group-details">
                    <p><strong>College:</strong> ${
								group.college || "Not specified"
							} </p>
                    <p><strong>Location:</strong> ${group.location} ${
			group.locationDetail ? `(${group.locationDetail})` : ""
		}</p>
                    <p><strong>Members:</strong> ${group.members}/${
			group.maxMembers
		}</p>
                    <p><strong>Description:</strong> ${group.description}</p>
                </div>
                <button class="join-group-btn bg-primary text-textLight border-none px-6 py-2 rounded cursor-pointer transition-colors hover:bg-secondary mt-4 w-full sm:w-auto" data-id="${
							group.id
						}">
                    ${
								group.members >= group.maxMembers
									? "Group Full"
									: "Join Group"
							}
                </button>
            `;

		// disable join button if group full
		const joinButton = groupElement.querySelector(".join-group-btn");
		if (group.members >= group.maxMembers) {
			joinButton.disabled = true;
			joinButton.classList.add("opacity-50", "cursor-not-allowed");
			joinButton.classList.remove("hover:bg-secondary");
		}

		joinButton.addEventListener("click", (e) => {
			e.preventDefault();
			joinGroup(group.id);
		});

		groupsContainer.appendChild(groupElement);
	});
}


function renderPagination(groups) {
	const paginationContainer = document.getElementById("pagination");
	paginationContainer.innerHTML = "";

	const totalPages = Math.ceil(groups.length / groupsPerPage);

	if (totalPages <= 1) return; 

	const prev = document.createElement("a");
	prev.href = "#";
	prev.innerHTML = "&laquo;";
	prev.className =
		"text-textDark px-4 py-2 no-underline transition-colors rounded hover:bg-gray-200";
	prev.onclick = (e) => {
		e.preventDefault();
		if (currentPage > 1) {
			currentPage--;
			displayGroups(filteredGroups, currentPage);
			renderPagination(filteredGroups);
		}
	};
	paginationContainer.appendChild(prev);

	// page numbers
	for (let i = 1; i <= totalPages; i++) {
		const pageLink = document.createElement("a");
		pageLink.href = "#";
		pageLink.textContent = i;
		pageLink.className = `px-4 py-2 no-underline rounded ${
			i === currentPage
				? "bg-primary text-white"
				: "text-textDark hover:bg-gray-200"
		}`;
		pageLink.onclick = (e) => {
			e.preventDefault();
			currentPage = i;
			displayGroups(filteredGroups, currentPage);
			renderPagination(filteredGroups);
		};
		paginationContainer.appendChild(pageLink);
	}

	// next btn
	const next = document.createElement("a");
	next.href = "#";
	next.innerHTML = "&raquo;";
	next.className =
		"text-textDark px-4 py-2 no-underline transition-colors rounded hover:bg-gray-200";
	next.onclick = (e) => {
		e.preventDefault();
		if (currentPage < totalPages) {
			currentPage++;
			displayGroups(filteredGroups, currentPage);
			renderPagination(filteredGroups);
		}
	};
	paginationContainer.appendChild(next);
}

// should change courses and college fetching
function addCoursesOptions() {
	const courseFilter = document.getElementById("filter-course");

	Array.from(courseOptions)
		.sort()
		.forEach((course) => {
			const option = document.createElement("option");
			option.value = course;
			option.textContent = course;
			courseFilter.appendChild(option);
		});
}

function addCollegesOptions() {
	const courseFilter = document.getElementById("filter-college");

	Array.from(collegeOptions)
		.sort()
		.forEach((course) => {
			const option = document.createElement("option");
			option.value = course;
			option.textContent = course;
			courseFilter.appendChild(option);
		});
}


async function init() {
	await fetchStudyGroups();
	addCoursesOptions();
	addCollegesOptions();
}

init();

