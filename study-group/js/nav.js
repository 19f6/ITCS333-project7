const menuToggle = document.getElementById("menu-toggle");
const menuItems = document.getElementById("menu-items");

menuToggle.addEventListener("click", function () {
	menuItems.classList.toggle("hidden");
});


document.addEventListener("click", function (event) {
	const isSmallScreen = window.innerWidth < 768;
	const isMenuVisible = !menuItems.classList.contains("hidden");
	const clickedInsideNav = navbar.contains(event.target);

	if (isSmallScreen && isMenuVisible && !clickedInsideNav) {
		menuItems.classList.add("hidden");
	}
});

// change menu visibility when resizign
window.addEventListener("resize", function () {
	if (window.innerWidth >= 768) {
		menuItems.classList.remove("hidden");
	} else {
		menuItems.classList.add("hidden");
	}
});
