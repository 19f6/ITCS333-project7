const API_URL = "../backend/handlers/study-groups/fetchGroups.php";

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
		const response = await fetch(API_URL, {
			method: "GET",
			headers: {
				"Content-Type": "application/json",
				Accept: "application/json",
			},
		});

		if (!response.ok) {
			throw new Error(
				`Network response was not ok: ${response.status} ${response.statusText}`
			);
		}

		const data = await response.json();
		console.log(data);
		if (!data.success) {
			throw new Error(data.message || "Failed to fetch study groups");
		}

		allGroups = data.groups || [];
		filteredGroups = [...allGroups];

		// Extract colleges and courses for filters
		allGroups.forEach((group) => {
			if (group.college) collegeOptions.add(group.college);
			if (group.course) courseOptions.add(group.course);
		});

		// Display groups
		displayGroups(filteredGroups, currentPage);
		renderPagination(filteredGroups);
	} catch (error) {
		console.error("Error fetching study groups:", error);
		groupsContainer.innerHTML = `
            <div class="p-4 bg-red-100 text-red-700 rounded-lg">
                <p>Failed to fetch study groups: ${
							error.message || "Unknown error"
						}. Please try again later.</p>
            </div>
        `;
	} finally {
		spinner.classList.add("hidden");
		paginationContainer.classList.remove("hidden");
	}
}

// Display study groups
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
						}</p>
                <p><strong>Location:</strong> ${group.location} ${
			group.location_details ? `(${group.location_details})` : ""
		}</p>
                <p><strong>Members:</strong> ${group.members}/${
			group.max_members
		}</p>
                <p><strong>Description:</strong> ${group.description}</p>
            </div>
            <button class="join-group-btn bg-primary text-textLight border-none px-6 py-2 rounded cursor-pointer transition-colors hover:bg-secondary mt-4 w-full sm:w-auto" data-id="${
					group.id
				}">
                ${
							group.members >= group.max_members
								? "Group Full"
								: "Join Group"
						}
            </button>
        `;

		groupElement.addEventListener("click", (e) => {
			e.preventDefault();
			e.stopPropagation();
			window.location.href = `group-details.php?id=${group.id}`;
		});

		// Disable join button if group full
		const joinButton = groupElement.querySelector(".join-group-btn");
		if (group.members >= group.max_members) {
			joinButton.disabled = true;
			joinButton.classList.add("opacity-50", "cursor-not-allowed");
			joinButton.classList.remove("hover:bg-secondary");
		}

		joinButton.addEventListener("click", (e) => {
			e.preventDefault();
			e.stopPropagation();
			joinGroup(group.id);
		});
		
		groupsContainer.appendChild(groupElement);
	});
}


async function joinGroup(groupId) {
	try {
		const response = await fetch(`../backend/handlers/study-groups/joinGroup.php`, {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
			},
			body: JSON.stringify({ group_id: groupId }),
		});

		const data = await response.json();
		
		if (data.success) {
			// Show success message
			alert("You've successfully joined the group!");
			// Refresh groups to update member counts
			fetchStudyGroups();
		} else {
			alert(data.message || "Failed to join group");
		}
	} catch (error) {
		console.error("Error joining group:", error);
		alert("An error occurred while trying to join the group");
	}
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

	// Page numbers
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

	// Next btn
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
	const collegeFilter = document.getElementById("filter-college");

	Array.from(collegeOptions)
		.sort()
		.forEach((college) => {
			const option = document.createElement("option");
			option.value = college;
			option.textContent = college;
			collegeFilter.appendChild(option);
		});
}

// Initialize the page
async function init() {
	await fetchStudyGroups();
	addCoursesOptions();
	addCollegesOptions();
}

init();

