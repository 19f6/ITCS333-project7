document.addEventListener('DOMContentLoaded', () => {
    const eventForm = document.getElementById('event-form');
    const eventGrid = document.querySelector('.e-card');
    const searchInput = document.querySelector('nav input[type="text"]');
    const filterSelect = document.getElementById('filter-type');
    const paginationFooter = document.querySelector('footer.pagination');

    let currentPage = 1;
    const limit = 6;
    let currentType = '';

    const API_URL = '/backend/event-calendar/event_api.php';

    function createEventCard(event) {
        const card = document.createElement('div');
        card.className = 'card p-3';
        card.innerHTML = `
            <h5>${event.title}</h5>
            <p><strong>Date:</strong> ${new Date(event.date).toLocaleDateString()}</p>
            <p><strong>Type:</strong> ${event.type || 'N/A'}</p>
            <p>${event.description}</p>
        `;

        if (event.attachment) {
            const fileLink = document.createElement('a');
            fileLink.href = `/backend/${event.attachment}`;
            fileLink.target = '_blank';
            const ext = event.attachment.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                const img = document.createElement('img');
                img.src = fileLink.href;
                img.alt = 'attachment';
                img.className = 'img-fluid mt-2';
                img.style.maxHeight = '150px';
                fileLink.appendChild(img);
            } else {
                fileLink.textContent = `📎 View attachment (${ext.toUpperCase()})`;
                fileLink.className = 'd-block mt-2';
            }
            card.appendChild(fileLink);
        }
        return card;
    }

    function renderEvents(list) {
        eventGrid.innerHTML = '';
        list.forEach(event => {
            const card = createEventCard(event);
            eventGrid.appendChild(card);
        });
    }

    function renderPaginationControls(pagination) {
        paginationFooter.innerHTML = '';
        const prevBtn = document.createElement('button');
        prevBtn.textContent = 'Previous';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => {
            currentPage--;
            fetchEvents();
        };

        const nextBtn = document.createElement('button');
        nextBtn.textContent = 'Next';
        nextBtn.disabled = currentPage >= pagination.pages;
        nextBtn.onclick = () => {
            currentPage++;
            fetchEvents();
        };

        const info = document.createElement('span');
        info.textContent = `Page ${pagination.page} of ${pagination.pages}`;
        info.style.padding = '0 10px';

        paginationFooter.appendChild(prevBtn);
        paginationFooter.appendChild(info);
        paginationFooter.appendChild(nextBtn);
    }

    async function fetchEvents() {
        eventGrid.innerHTML = '<p>Loading events...</p>';
        try {
            const params = new URLSearchParams({
                page: currentPage,
                limit: limit
            });

            if (currentType) {
                params.append('type', currentType);
            }

            const response = await fetch(`${API_URL}?${params.toString()}`);
            if (!response.ok) throw new Error(`Error: ${response.status}`);
            const data = await response.json();

            renderEvents(data.events);
            renderPaginationControls(data.pagination);
        } catch (error) {
            console.error(error);
            eventGrid.innerHTML = '<p class="text-danger">Failed to load events.</p>';
        }
    }

    async function addNewEvent(newEvent) {
        try {
            const formData = new FormData();
            formData.append('title', newEvent.name);
            formData.append('description', newEvent.description);
            formData.append('date', newEvent.date);
            formData.append('type', newEvent.type);
            const attachment = eventForm['event-attachment'].files[0];
            if (attachment) {
                formData.append('attachment', attachment);
            }

            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error(`Failed to create event. Status: ${response.status}`);
            currentPage = 1;
            await fetchEvents();
        } catch (error) {
            console.error('Error creating event:', error);
            alert('Failed to create event. Please try again.');
        }
    }

    eventForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = eventForm['event-name'].value.trim();
        const date = eventForm['event-date'].value;
        const type = eventForm['event-type']?.value || '';
        const description = eventForm['event-description'].value.trim();

        if (!name || !date || !description || !type) {
            alert('Please fill in all required fields.');
            return;
        }

        const newEvent = { name, date, type, description };
        await addNewEvent(newEvent);
        eventForm.reset();
        bootstrap.Modal.getInstance(document.getElementById('event-modal')).hide();
    });

    filterSelect.addEventListener('change', e => {
        currentPage = 1;
        currentType = e.target.value;
        fetchEvents();
    });

    fetchEvents();
});
