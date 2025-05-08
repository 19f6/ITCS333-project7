function getGroupIdFromUrl() {
	const urlParams = new URLSearchParams(window.location.search);
	return urlParams.get("id");
}

// fetch group details
async function fetchGroupDetails() {
	const groupId = getGroupIdFromUrl();

	if (!groupId) {
		displayError(
			"Missing group ID. Please go back to the study groups page."
		);
		return;
	}

	try {
		const response = await fetch(
			`../backend/handlers/fetchGroupDetails.php?id=${groupId}`
		);

		if (!response.ok) {
			if (response.status === 401) {
				displayError("You must be logged in to view this group.");
				return;
			} else if (response.status === 403) {
				displayError(
					"You don't have permission to view this group. Please join the group first."
				);
				return;
			} else if (response.status === 404) {
				displayError("This study group could not be found.");
				return;
			}

			throw new Error(`Failed to fetch group details: ${response.status}`);
		}

		const data = await response.json();
		console.log(data);

		if (!data.success) {
			throw new Error(data.message || "Failed to fetch group details");
		}

		displayGroupDetails(data.group, data.members, data.comments);
	} catch (error) {
		console.error("Error fetching group details:", error);
		displayError(`Failed to load group details: ${error.message}`);
	}
}

// display error message
function displayError(message) {
	const contentContainer =
		document.querySelector(".content-container") || document.body;

	contentContainer.innerHTML = `
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-6 mx-auto max-w-2xl">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline">${message}</span>
            <div class="mt-4">
                <a href="study-groups.php" class="bg-primary text-white px-4 py-2 rounded hover:bg-secondary">Back to Study Groups</a>
            </div>
        </div>
    `;
}

function displayGroupDetails(group, members, comments) {
	document.title = `${group.title} | UniHub`;

	// Title and course
	document.querySelector("h1").textContent = group.title;
	const courseElement = document.querySelector(
		".bg-primary.text-textLight.px-4.py-1.rounded-full"
	);
	if (courseElement) courseElement.textContent = group.course || "No course";

	// Details section
	const collegeElement = document.getElementById("group-college");
	if (collegeElement)
		collegeElement.textContent = group.college || "Not specified";

	const locationElement = document.getElementById("group-location");
	if (locationElement) {
		const location = group.location || "Unknown";
		const locationDetails = group.location_details
			? ` (${group.location_details})`
			: "";
		locationElement.textContent = location + locationDetails;
	}

	const membersCountElement = document.getElementById("group-members-count");
	if (membersCountElement)
		membersCountElement.textContent = `${members.length}/${group.max_members}`;

	const creatorElement = document.getElementById("group-creator");
	if (creatorElement)
		creatorElement.textContent = group.user_name || "Unknown";

	const createdDateElement = document.getElementById("group-created-date");
	if (createdDateElement) {
		const createdDate = new Date(group.created_at);
		createdDateElement.textContent = createdDate.toLocaleDateString("en-US", {
			year: "numeric",
			month: "long",
			day: "numeric",
		});
	}

	// Description
	const descriptionElement = document.getElementById("group-description");
	if (descriptionElement)
		descriptionElement.textContent = group.description || "No description.";

	// Members list
	const membersContainer = document.getElementById("members-container");
	if (membersContainer) {
		membersContainer.innerHTML = "";
		members.forEach((member) => {
			const initial = member.name.charAt(0).toUpperCase();
			const memberElement = document.createElement("div");
			memberElement.className =
				"flex items-center bg-background py-2 px-4 rounded-full";
			memberElement.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center mr-2 font-semibold">${initial}</div>
                <span>${member.name}</span>
            `;
			membersContainer.appendChild(memberElement);
		});
	}

	// Comments
	const commentsContainer = document.getElementById("comments-container");
	const commentCountElement = document.getElementById("comments-count");
	if (commentCountElement) commentCountElement.textContent = comments.length;

	if (commentsContainer) {
		const form = commentsContainer.querySelector("form");
		commentsContainer.innerHTML = "";
		if (form) commentsContainer.appendChild(form); // Re-append the form if needed

		comments.forEach((comment) => {
			const date = new Date(comment.created_at);
			const formattedDate = date.toLocaleString("en-US", {
				year: "numeric",
				month: "short",
				day: "numeric",
				hour: "2-digit",
				minute: "2-digit",
			});

			const commentElement = document.createElement("div");
			commentElement.className = "bg-white rounded-lg shadow-sm p-4 mb-4";
			commentElement.innerHTML = `
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">${comment.user_name}</span>
                    <span class="text-gray-500 text-sm">${formattedDate}</span>
                </div>
                <p>${comment.comment_content}</p>
            `;
			commentsContainer.appendChild(commentElement);
		});
	}

	document.getElementById("comment-form").addEventListener("submit", (e) => {
		e.preventDefault();
		submitComment(group.id);
	});
}

async function submitComment(groupId) {
	const commentContent = document
		.getElementById("comment-content")
		.value.trim();

	if (!commentContent) return;

	try {
		const response = await fetch("../backend/handlers/addGroupComment.php", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
			},
			body: JSON.stringify({
				group_id: groupId,
				content: commentContent,
			}),
		});

		const data = await response.json();
		console.log("okayyyyyy");
		if (data.success) {
			document.getElementById("comment-content").value = "";
			window.location.reload();
		} else {
			alert(data.message || "Failed to add comment");
			document.getElementById("comment-content").value = "";
		}
	} catch (error) {
		console.error("Error adding comment:", error);
		alert("An error occurred while adding your comment");
	}
}

// Initialize the page
document.addEventListener("DOMContentLoaded", () => {
	fetchGroupDetails();
});
