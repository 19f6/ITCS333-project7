document.addEventListener('DOMContentLoaded', () => {
    const eventForm = document.getElementById('event-form');
    const eventGrid = document.querySelector('.e-card');
    const searchInput = document.querySelector('nav input[type="text"]');
    const filterSelect = document.querySelector('nav select');

    let events = [];
    const API_URL = 'https://680d01dd2ea307e081d5b5e7.mockapi.io/api/events/Events';

    function createEventCard(event) {
        const card = document.createElement('div');
        card.className = 'card p-3 m-2';
        card.dataset.type = event.type || '';
        card.innerHTML = `
            <h5>${event.name}</h5>
            <p><strong>Date:</strong> ${new Date(event.date).toLocaleDateString()}</p>
            <p><strong>Type:</strong> ${event.type}</p>
            <p>${event.description}</p>
        `;
        return card;
    }

    function renderEvents(list) {
        eventGrid.innerHTML = '';
        if (list.length === 0) {
            eventGrid.innerHTML = '<p>No events found.</p>';
            return;
        }
        list.forEach(event => {
            const card = createEventCard(event);
            eventGrid.appendChild(card);
        });
    }

    function showLoading() {
        eventGrid.innerHTML = '<p>Loading events...</p>';
    }

    function showError(message) {
        eventGrid.innerHTML = `<p class="text-danger">${message}</p>`;
    }

    async function fetchEvents() {
        showLoading();
        try {
            const response = await fetch(API_URL);
            if (!response.ok) {
                throw new Error(`Error: ${response.status} ${response.statusText}`);
            }
            const data = await response.json();
            events = data;
            renderEvents(events);
        } catch (error) {
            console.log(error);
            showError('Failed to load events. Please try again later.');
        }
    }

    async function addNewEvent(newEvent) {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: newEvent.name,
                    date: newEvent.date,
                    type: newEvent.type,
                    description: newEvent.description
                })
            });

            if (!response.ok) {
                throw new Error(`Failed to create event. Status: ${response.status}`);
            }

            const createdEvent = await response.json();
            console.log('Event created:', createdEvent);
            events.push(createdEvent);
            renderEvents(events);
            return createdEvent;

        } catch (error) {
            console.error('Error creating event:', error);
            throw error;
        }
    }

    eventForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const name = eventForm['event-name'].value.trim();
        const date = eventForm['event-date'].value;
        const type = eventForm['event-type'].value;
        const description = eventForm['event-description'].value.trim();

        if (!name || !date || !type || !description) {
            alert('Please fill in all required fields.');
            return;
        }

        const newEvent = {
            name,
            date,
            type,
            description,
            createdAt: new Date().toISOString()
        };

        try {
            await addNewEvent(newEvent);
            eventForm.reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById('event-modal'));
            modal.hide();
        } catch (error) {
            console.error('Error submitting form:', error);
            alert('Failed to create event. Please try again.');
        }
    });

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        const filtered = events.filter(event =>
            event.name.toLowerCase().includes(query) ||
            event.description.toLowerCase().includes(query)
        );
        renderEvents(filtered);
    });

    filterSelect.addEventListener('change', (e) => {
        const type = e.target.value;
        if (type === '') {
            renderEvents(events);
        } else {
            const filtered = events.filter(event => event.type === type);
            renderEvents(filtered);
        }
    });
    fetchEvents();
});
