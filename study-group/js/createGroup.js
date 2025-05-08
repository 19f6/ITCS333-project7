const createGroupBtn = document.getElementById("create-group-btn");
const createGroupModal = document.getElementById("create-group-modal");
const closeModal = document.getElementById("close-modal");
const cancelModal = document.getElementById("cancel-modal");
const createForm = document.getElementById("create-form");
const formErrors = document.getElementById("form-errors");

createGroupBtn.addEventListener("click", () => {
	createGroupModal.classList.remove("hidden");
});

function closeModalHandler() {
	createGroupModal.classList.add("hidden");
	clearErrors(); // Clear errors when closing the modal
	createForm.reset(); // Reset the form
}

closeModal.addEventListener("click", closeModalHandler);
cancelModal.addEventListener("click", closeModalHandler);

createGroupModal.addEventListener("click", (e) => {
	if (e.target === createGroupModal) {
		closeModalHandler();
	}
});

function clearErrors() {
	formErrors.innerHTML = ""; // Clear any existing error messages
	formErrors.classList.add("hidden");
}

function displayError(message) {
	formErrors.textContent = message;
	formErrors.classList.remove("hidden");
}

createForm.addEventListener("submit", async (e) => {
	e.preventDefault();

	clearErrors();

	const groupName = document.getElementById("group-name").value.trim();
	const college = document.getElementById("college").value;
	const course = document.getElementById("course").value;
	const locationType = document.getElementById("location-type").value;
	const locationDetails = document.getElementById("location").value.trim();
	const description = document.getElementById("description").value.trim();
	const maxMembers = document.getElementById("max-members").value;

	if (!groupName || !college || !course || !locationType || !description) {
		displayError("Please fill in all required fields.");
		return;
	}

	if (
		(locationType === "in-person" || locationType === "Both") &&
		!locationDetails
	) {
		displayError("Please provide location details for in-person meetings.");
		return;
	}

	// Validate max members
	if (maxMembers !== "" && (maxMembers < 3 || maxMembers > 50)) {
		displayError("Maximum members must be between 3 and 50.");
		return;
	}

	const formData = new FormData(createForm);

	// Ensure max-members is included in the form data
	if (!formData.has("max-members")) {
		formData.append("max-members", maxMembers);
	}

	try {
		const response = await fetch("../backend/handlers/createGroup.php", {
			method: "POST",
			body: formData,
		});

		const data = await response.json();

		if (response.ok) {
			closeModalHandler();
			alert(data.message);
			// Refresh the page to show the new group
			window.location.reload();
		} else {
			displayError(data.message);
		}
	} catch (error) {
		console.error("Error:", error);
		displayError("An unexpected error occurred. Please try again.");
	}
});
