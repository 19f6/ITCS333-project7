import { validateForm } from "./formValidation.js";

const createGroupBtn = document.getElementById("create-group-btn");
const createGroupModal = document.getElementById("create-group-modal");
const closeModal = document.getElementById("close-modal");
const cancelModal = document.getElementById("cancel-modal");

createGroupBtn.addEventListener("click", () => {
	createGroupModal.classList.remove("hidden");
	//document.body.style.overflow = 'hidden';
});

function closeModalHandler() {
	createGroupModal.classList.add("hidden");
	//document.body.style.overflow = 'auto';
}

closeModal.addEventListener("click", closeModalHandler);
cancelModal.addEventListener("click", closeModalHandler);

createGroupModal.addEventListener("click", (e) => {
	if (e.target === createGroupModal) {
		closeModalHandler();
	}
});

const createForm = document.getElementById("create-form");

createForm.addEventListener("submit", (e) => {
	e.preventDefault();
	if (validateForm()) {
		console.log("Valid form");
		closeModalHandler();
	} else {
		console.log("Invalid Form.");
	}
});
