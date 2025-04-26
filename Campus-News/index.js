const newsScroller = document.querySelector('.News-scroller');
const eventsContainer = document.querySelector('.right-panel');
const searchInput = document.getElementById('search');
const subjectFilter = document.getElementById('faculty');
const browseFilter = document.getElementById('filter-news');
const viewAllNewsBtn = document.querySelector('.but1');
const seeAllNewsLink = document.querySelector('.right-panel a:last-child');
const pagination = document.querySelector('.pagination');
const apiURL = 'data.json';

let allNews = [];
let displayedNews = [];
let currentPage = 5;
const itemsPerPage = 5;

async function fetchNews() {
    showLoading();
    try {
        const response = await fetch(apiURL);
        if (!response.ok) {
            throw new Error('Failed to fetch news');
        }
        const data = await response.json();
        allNews = data.slice(0, 30).map(item => ({
            id: item.id,
            title: item.title,
            body: item.body,
            category: item.category,
            image: item.image
        }));
        displayedNews = [...allNews];
        renderNews();
    } catch (error) {
        showError(error.message);
    }
}

function renderNews() {
    clearNews();
    
    let itemsToShow = [];
    
    if (currentPage === 5) {
        
        itemsToShow = allNews.filter(item => item.id >= 1 && item.id <= 9); 
    } else {
        const start = (currentPage - 1) * itemsPerPage;
        itemsToShow = allNews.slice(start, start + itemsPerPage);
    }

    itemsToShow.forEach(item => {
        const link = document.createElement('a');
        link.href = `News/News${item.id}.html`;
        link.target = "_blank";
        link.style.textDecoration = 'none';

        const card = document.createElement('div');
        card.className = 'News-card';
        card.innerHTML = `
            <div class="news-image-container">
                <img src="${item.image}" alt="News Image" class="news-image" />
            </div>
            <h3>${item.title}</h3>
            <p>${item.body}</p>
            <small>${item.category}</small>
        `;
        link.appendChild(card);
        newsScroller.appendChild(link);

        const eventCard = document.createElement('div');
        eventCard.className = 'Events-card';
        eventCard.innerHTML = `
            <img src="${item.image}" alt="Event Image">
            <div class="Events-card-content">
                <p>${item.body}</p>
                <span class="tag">${item.category}</span>
            </div>
        `;
        eventsContainer.insertBefore(eventCard, seeAllNewsLink);
    });

    renderPagination();
}




function clearNews() {
    newsScroller.innerHTML = '';
    const eventCards = eventsContainer.querySelectorAll('.Events-card');
    eventCards.forEach(card => card.remove());
}

function filterNews() {
    const searchValue = searchInput.value.toLowerCase();
    const selectedCategory = subjectFilter.value;

    displayedNews = allNews.filter(news => {
        const matchTitle = news.title.toLowerCase().includes(searchValue);
        const matchCategory = selectedCategory ? news.category === selectedCategory : true;
        return matchTitle && matchCategory;
    });

    currentPage = 1;
    renderNews();
}

function getPaginatedItems(items, page, perPage) {
    const start = (page - 1) * perPage;
    return items.slice(start, start + perPage);
}

function renderPagination() {
    pagination.innerHTML = '';

    const totalPages = Math.ceil(displayedNews.length / itemsPerPage);

    // Previous Button
    const prevButton = document.createElement('a');
    prevButton.innerHTML = '&laquo;';
    prevButton.href = "#";
    prevButton.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            renderNews();
        }
    });
    pagination.appendChild(prevButton);

    // Display ALL page numbers (no hiding)
    for (let i = 1; i <= totalPages; i++) {
        const pageLink = document.createElement('a');
        pageLink.textContent = i;
        pageLink.href = "#";
        if (i === currentPage) {
            pageLink.classList.add('active');
        }
        pageLink.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = i;
            renderNews();
        });
        pagination.appendChild(pageLink);
    }

    // Next Button
    const nextButton = document.createElement('a');
    nextButton.innerHTML = '&raquo;';
    nextButton.href = "#";
    nextButton.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage < totalPages) {
            currentPage++;
            renderNews();
        }
    });
    pagination.appendChild(nextButton);
}



function showLoading() {
    newsScroller.innerHTML = '<p>Loading news...</p>';
    const eventCards = eventsContainer.querySelectorAll('.Events-card');
    eventCards.forEach(card => card.remove());
}

function showError(message) {
    newsScroller.innerHTML = `<p style="color: red;">${message}</p>`;
    eventsContainer.innerHTML = `<p style="color: red;">${message}</p>`;
}

// Form validation
function validateFormInput(input) {
    if (input.trim() === '') {
        alert('Search input cannot be empty');
        return false;
    }
    return true;
}

// Event Listeners
searchInput.addEventListener('input', filterNews);
subjectFilter.addEventListener('change', filterNews);
browseFilter.addEventListener('change', (e) => {
    if (e.target.value === 'Recent News') {
        displayedNews = [...allNews].sort((a, b) => b.id - a.id);
        currentPage = 1;
        renderNews();
    }
});

viewAllNewsBtn.addEventListener('click', () => {
    displayedNews = [...allNews];
    currentPage = 1;
    renderNews();
});

seeAllNewsLink.addEventListener('click', (e) => {
    e.preventDefault();
    displayedNews = [...allNews];
    currentPage = 1;
    renderNews();
});

fetchNews();