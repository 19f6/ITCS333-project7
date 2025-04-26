const collegeFilter = document.getElementById('filter-college');
const courseFilter = document.getElementById('filter-course');
const locationFilter = document.getElementById('filter-location');
const searchInput = document.getElementById('search');

collegeFilter.addEventListener('change', applyFilters);
courseFilter.addEventListener('change', applyFilters);
locationFilter.addEventListener('change', applyFilters);
searchInput.addEventListener('input', applyFilters);

// filtering and searching
function applyFilters() {
    const selectedCollege = collegeFilter.value.toLowerCase();
    const selectedCourse = courseFilter.value.toLowerCase();
    const selectedLocation = locationFilter.value.toLowerCase();
    const searchKeyword = searchInput.value.toLowerCase();

    filteredGroups = allGroups.filter(group => {
        const matchesCollege = selectedCollege ? group.college.toLowerCase() === selectedCollege : true;
        const matchesCourse = selectedCourse ? group.course.toLowerCase() === selectedCourse : true;
        const matchesLocation = selectedLocation ? group.location.toLowerCase() === selectedLocation : true;
        const matchesSearch =
			!searchKeyword ||
			(group.title && group.title.toLowerCase().includes(searchKeyword)) ||
			(group.description && group.description.toLowerCase().includes(searchKeyword)) ||
			(group.course && group.course.toLowerCase().includes(searchKeyword));   

        return matchesCollege && matchesCourse && matchesLocation && matchesSearch;
    });

    
    currentPage = 1;
    displayGroups(filteredGroups, currentPage);
    renderPagination(filteredGroups);
}





