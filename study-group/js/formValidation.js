const groupName = document.getElementById("group-name");
const college = document.getElementById("college");
const level = document.getElementById("course");
const locationType = document.getElementById("location-type");
const location = document.getElementById("location");
const description = document.getElementById("description");
const maxMembers = document.getElementById("max-members");
// const gender = document.getElementById("gender");

// show error msg
function showError(input, message) {
	const parent = input.parentElement;
	const existingError = parent.querySelector(".error-message");
	if (existingError) {
		parent.removeChild(existingError);
	}

	input.classList.remove("border-red-500");
	input.classList.add("border-red-500");

	const errorDiv = document.createElement("div");
	errorDiv.className = "error-message text-red-500 text-sm mt-1";
	errorDiv.innerText = message;
	parent.appendChild(errorDiv);
}

// cleaning errors directly
function clearError(input) {
	const parent = input.parentElement;
	const existingError = parent.querySelector(".error-message");
	if (existingError) {
		parent.removeChild(existingError);
	}
	input.classList.remove("border-red-500");
	input.classList.add("border-borderColor");
}


export function validateForm() {
	let isValid = true;

	if (groupName.value.trim() === "") {
		showError(groupName, "Group name is required");
		isValid = false;
	} else {
		clearError(groupName);
	}

	if (college.value === "" || college.value === "Please Choose...") {
		showError(college, "College selection is required");
		isValid = false;
	} else {
		clearError(college);
	}

	if (level.value === "" || level.value === "Select a course") {
		showError(level, "Course selection is required");
		isValid = false;
	} else {
		clearError(level);
	}

	if (locationType.value === "") {
		showError(locationType, "Location type is required");
		isValid = false;
	} else {
		clearError(locationType);

		if (
			(locationType.value === "in-person" ||
				locationType.value === "Both") &&
			location.value.trim() === ""
		) {
			showError(
				location,
				"Location details are required for in-person meetings"
			);
			isValid = false;
		} else {
			clearError(location);
		}
	}

	if (description.value.trim() === "") {
		showError(description, "Description is required");
		isValid = false;
	} else {
		clearError(description);
	}

	if (maxMembers.value < 2 || maxMembers.value > 20) {
		showError(maxMembers, "Maximum members must be between 2 and 20");
		isValid = false;
	} else {
		clearError(maxMembers);
	}

	// if (
	// 	gender.value !== "any" &&
	// 	gender.value !== "male" &&
	// 	gender.value !== "female"
	// ) {
	// 	showError(gender, "Please select a valid gender preference");
	// 	isValid = false;
	// } else {
	// 	clearError(gender);
	// }

	return isValid;
}

// clear error on input change
const inputs = [
	groupName,
	college,
	level,
	locationType,
	location,
	description,
	maxMembers,
	// gender,
];

inputs.forEach((input) => {
	input.addEventListener("input", () => {
		clearError(input);
	});
});

// handler for location type changes
locationType.addEventListener("change", () => {
	if (locationType.value !== "in-person" && locationType.value !== "Both") {
		location.value = "";
		clearError(location);
	}
});


