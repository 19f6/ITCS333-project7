const newsScroller = document.querySelector('.News-scroller');
const eventsContainer = document.querySelector('.right-panel');
const searchInput = document.getElementById('search');
const subjectFilter = document.getElementById('faculty');
const browseFilter = document.getElementById('filter-news');
const viewAllNewsBtn = document.getElementById('view-all-news');
const createBtn = document.getElementById('create-news');
const formPanel = document.getElementById('news-form-panel');
const publishBtn = document.getElementById('publish-news');
const closeFormBtn = document.getElementById('close-form');
const imageInput = document.getElementById('news-image');
let imagePreviewData = null;

const apiURL = 'data.json';
let jsonNews = [];
let localNews = JSON.parse(localStorage.getItem('localNews')) || [];
let allNews = [];
let displayedNews = [];
let currentPage = 1;
const itemsPerPage = 5;

let pagination = document.querySelector('.pagination');
if (!pagination) {
    pagination = document.createElement('div');
    pagination.className = 'pagination';
    document.querySelector('.bottom-panel').after(pagination);
}

async function fetchNews() {
    showLoading();
    try {
        const response = await fetch(apiURL);
        if (!response.ok) throw new Error('Failed to fetch news');
        const data = await response.json();
        jsonNews = [...data];
        updateAllNews();
    } catch (error) {
        showError(error.message);
    }
}

function updateAllNews() {
    allNews = [...localNews, ...jsonNews];
    displayedNews = [...allNews];
    currentPage = 1;
    renderNews();
}

function renderNews() {
    clearNews();
    const paginatedItems = getPaginatedItems(displayedNews, currentPage, itemsPerPage);

    paginatedItems.forEach(item => {
        const link = document.createElement('a');
        link.href = `News/template.html?id=${item.id}`;
        link.target = '_blank';
        link.style.textDecoration = 'none';

        const card = document.createElement('div');
        card.className = 'News-card';
        card.innerHTML = `
            <img src="${item.image}" id="img2">
            <p>${item.title}</p>
        `;

        const btnContainer = document.createElement('div');
        btnContainer.style.marginTop = '8px';

        if (!item.fromJson) {
            const deleteBtn = document.createElement('span');
            deleteBtn.innerHTML = '🗑️';
            deleteBtn.title = 'Delete';
            deleteBtn.style.cursor = 'pointer';
            deleteBtn.onclick = (e) => {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this news?')) {
                    localNews = localNews.filter(n => n.id !== item.id);
                    localStorage.setItem('localNews', JSON.stringify(localNews));
                    updateAllNews();
                }
            };

            const editBtn = document.createElement('span');
            editBtn.innerHTML = '✏️';
            editBtn.title = 'Edit';
            editBtn.style.cursor = 'pointer';
            editBtn.style.marginLeft = '10px';
            editBtn.onclick = (e) => {
                e.preventDefault();
                formPanel.style.display = 'flex';
                document.getElementById('news-title').value = item.title;
                document.getElementById('news-body').value = item.body;
                document.getElementById('news-category').value = item.category;
                document.getElementById('news-tags').value = item.tags || '';
                imagePreviewData = item.image;
                document.getElementById('image-preview').src = imagePreviewData;
                formPanel.setAttribute('data-edit-id', item.id);
            };

            btnContainer.appendChild(deleteBtn);
            btnContainer.appendChild(editBtn);
            card.appendChild(btnContainer);
        }

        link.appendChild(card);
        newsScroller.appendChild(link);

        if (item.id >= 6) {
            const eventLink = document.createElement('a');
            eventLink.href = `News/template.html?id=${item.id}`;
            eventLink.target = '_blank';

            const eventCard = document.createElement('div');
            eventCard.className = 'Events-card';
            eventCard.innerHTML = `
                <img src="${item.image}">
                <div class="Events-card-content">
                    <p><strong>${item.title}</strong></p>
                    <span class="tag">${item.category}</span>
                </div>
            `;

            eventLink.appendChild(eventCard);
            eventsContainer.appendChild(eventLink);
        }
    });

    renderPagination();
}

function clearNews() {
    if (newsScroller) newsScroller.innerHTML = '';
    const eventCards = eventsContainer.querySelectorAll('.Events-card');
    eventCards.forEach(card => card.parentElement.remove());
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
    if (!pagination) return;
    pagination.innerHTML = '';
    const totalPages = Math.ceil(displayedNews.length / itemsPerPage);

    const prevButton = document.createElement('a');
    prevButton.innerHTML = '«';
    prevButton.href = '#';
    prevButton.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            renderNews();
        }
    });
    pagination.appendChild(prevButton);

    for (let i = 1; i <= totalPages; i++) {
        const pageLink = document.createElement('a');
        pageLink.textContent = i;
        pageLink.href = '#';
        if (i === currentPage) pageLink.classList.add('active');

        pageLink.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = i;
            renderNews();
        });

        pagination.appendChild(pageLink);
    }

    const nextButton = document.createElement('a');
    nextButton.innerHTML = '»';
    nextButton.href = '#';
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
    if (newsScroller) newsScroller.innerHTML = '<p>Loading news...</p>';
}

function showError(message) {
    if (newsScroller) newsScroller.innerHTML = `<p style="color:red">${message}</p>`;
}

createBtn.addEventListener('click', () => {
    formPanel.style.display = 'flex';
    formPanel.removeAttribute('data-edit-id');
    imagePreviewData = null;
    document.getElementById('image-preview').src = '';
});

closeFormBtn.addEventListener('click', () => {
    formPanel.style.display = 'none';
});

imageInput.addEventListener('change', () => {
    const file = imageInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviewData = e.target.result;
            document.getElementById('image-preview').src = imagePreviewData;
        };
        reader.readAsDataURL(file);
    }
});

publishBtn.addEventListener('click', () => {
    const title = document.getElementById('news-title').value.trim();
    const body = document.getElementById('news-body').value.trim();
    const category = document.getElementById('news-category').value;
    const tags = document.getElementById('news-tags').value.trim();

    if (!title || !body || !category || !imagePreviewData) {
        alert("All fields are required including an image.");
        return;
    }

    const existingId = formPanel.getAttribute('data-edit-id');

    if (existingId) {
        const index = localNews.findIndex(n => n.id == existingId);
        if (index !== -1) {
            localNews[index] = { ...localNews[index], title, body, category, tags, image: imagePreviewData };
        }
    } else {
        const newId = jsonNews.length + localNews.length + 1;
        const newNews = { id: newId, title, body, category, tags, image: imagePreviewData, fromJson: false };
        localNews.unshift(newNews);
    }

    localStorage.setItem('localNews', JSON.stringify(localNews));
    formPanel.style.display = 'none';
    document.getElementById('news-title').value = '';
    document.getElementById('news-body').value = '';
    document.getElementById('news-category').value = '';
    document.getElementById('news-tags').value = '';
    imageInput.value = '';
    imagePreviewData = null;
    document.getElementById('image-preview').src = '';
    updateAllNews();
});

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
    window.location.href = 'News/all-news.html';
});

fetchNews();
