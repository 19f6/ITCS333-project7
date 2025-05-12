document.addEventListener("DOMContentLoaded", () => {
	const editBtn = document.getElementById("editBtn");
	const deleteBtn = document.getElementById("deleteBtn");
	const editModal = document.getElementById("edit-group-modal");
	const closeEditModalBtn = document.getElementById("close-edit-modal");
	const cancelEditModalBtn = document.getElementById("cancel-edit-modal");
	const editForm = document.getElementById("edit-form");

	function getGroupIdFromUrl() {
		const urlParams = new URLSearchParams(window.location.search);
		return urlParams.get("id");
	}

	function openEditModal() {
		const groupId = getGroupIdFromUrl();
		if (!groupId) {
			alert("Error: Cannot identify the group to edit.");
			return;
		}
		fetchCurrentGroupDetails(groupId);
		editModal.classList.remove("hidden");
	}

	// fetch current group details for edit form
	async function fetchCurrentGroupDetails(groupId) {
		try {
			const response = await fetch(
				`../backend/handlers/study-groups/fetchGroupDetails.php?id=${groupId}`
			);

			if (!response.ok) {
				throw new Error(
					`Failed to fetch group details: ${response.status}`
				);
			}

			const data = await response.json();

			if (!data.success) {
				throw new Error(data.message || "Failed to fetch group details");
			}

			document.getElementById("edit-group-id").value = data.group.id;
			document.getElementById("edit-group-name").value = data.group.title;
			document.getElementById("edit-college").value = data.group.college_id;
			document.getElementById("edit-course").value = data.group.course;
			document.getElementById("edit-location-type").value = data.group
				.location
				? data.group.location.toLowerCase()
				: "";
			document.getElementById("edit-location").value =
				data.group.location_details || "";
			document.getElementById("edit-description").value =
				data.group.description;
			document.getElementById("edit-max-members").value =
				data.group.max_members;
		} catch (error) {
			console.error("Error fetching group details:", error);
			alert(`Failed to load group details: ${error.message}`);
		}
	}

	function closeEditModal() {
		editModal.classList.add("hidden");
	}

	// delete group 
	async function deleteGroup() {
		const groupId = getGroupIdFromUrl();
		if (!groupId) {
			alert("Error: Cannot identify the group to delete.");
			return;
		}

		if (
			confirm(
				"Are you sure you want to delete this study group? This action cannot be undone."
			)
		) {
			try {
				const response = await fetch(
					"../backend/handlers/study-groups/deleteGroup.php",
					{
						method: "POST",
						headers: {
							"Content-Type": "application/json",
						},
						body: JSON.stringify({ group_id: groupId }),
					}
				);

				const data = await response.json();

				if (data.success) {
					alert("Study group deleted successfully!");
					window.location.href = "study-groups.php";
				} else {
					alert(data.message || "Failed to delete study group.");
				}
			} catch (error) {
				console.error("Error deleting study group:", error);
				alert(
					`An error occurred while deleting the group: ${error.message}`
				);
			}
		}
	}

	if (editBtn) {
		editBtn.addEventListener("click", openEditModal);
	}

	if (deleteBtn) {
		deleteBtn.addEventListener("click", deleteGroup);
	}

	if (closeEditModalBtn) {
		closeEditModalBtn.addEventListener("click", closeEditModal);
	}

	if (cancelEditModalBtn) {
		cancelEditModalBtn.addEventListener("click", closeEditModal);
	}

	// form submission
	if (editForm) {
		editForm.addEventListener("submit", async (e) => {
			const groupId = getGroupIdFromUrl();
			if (!groupId) {
				alert("Error: Cannot identify the group to edit.");
				e.preventDefault();
				return;
			}

			e.preventDefault();

			const formData = {
				group_id: groupId,
				title: document.getElementById("edit-group-name").value,
				college: document.getElementById("edit-college").value,
				course: document.getElementById("edit-course").value,
				location: document.getElementById("edit-location-type").value,
				location_details: document.getElementById("edit-location").value,
				description: document.getElementById("edit-description").value,
				max_members: document.getElementById("edit-max-members").value,
			};

			try {
				const response = await fetch(
					"../backend/handlers/study-groups/editGroup.php",
					{
						method: "POST",
						headers: {
							"Content-Type": "application/json",
						},
						body: JSON.stringify(formData),
					}
				);

				const data = await response.json();

				if (data.success) {
					window.location.reload();
				} else {
					// error message
					const errorContainer =
						document.getElementById("edit-form-errors");
					errorContainer.textContent =
						data.message || "Failed to update group";
					errorContainer.classList.remove("hidden");
				}
			} catch (error) {
				console.error("Error updating group:", error);
				alert("An error occurred while updating the group");
			}
		});
	}
});
