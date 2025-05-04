document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
        });
    }
    
    // Tabs functionality
    const tabs = document.querySelectorAll('.tab');
    
    if (tabs.length > 0) {
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Get the tab ID
                const tabId = this.getAttribute('data-tab');
                
                // Remove active class from all tabs
                document.querySelectorAll('.tab').forEach(t => {
                    t.classList.remove('active');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Hide all tab content
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Show the selected tab content
                const tabContent = document.getElementById(tabId + '-tab');
                if (tabContent) {
                    tabContent.classList.add('active');
                }
            });
        });
    }
    
    // Trip type radio button functionality
    const tripTypeRadios = document.querySelectorAll('input[name="trip_type"]');
    const returnDateField = document.querySelector('.return-date');
    
    if (tripTypeRadios.length > 0 && returnDateField) {
        tripTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'one_way') {
                    returnDateField.style.display = 'none';
                } else {
                    returnDateField.style.display = 'block';
                }
            });
        });
    }
    
    // Flight status search type toggle
    const searchTypeRadios = document.querySelectorAll('input[name="search_type"]');
    const flightSearch = document.getElementById('flight-search');
    const routeSearch = document.getElementById('route-search');
    
    if (searchTypeRadios.length > 0 && flightSearch && routeSearch) {
        searchTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'flight') {
                    flightSearch.classList.remove('hidden');
                    routeSearch.classList.add('hidden');
                } else {
                    flightSearch.classList.add('hidden');
                    routeSearch.classList.remove('hidden');
                }
            });
        });
    }
    
    // Set min date for date inputs to today
    const dateInputs = document.querySelectorAll('input[type="date"]');
    if (dateInputs.length > 0) {
        const today = new Date().toISOString().split('T')[0];
        dateInputs.forEach(input => {
            input.setAttribute('min', today);
            if (!input.value) {
                input.value = today;
            }
        });
    }

    // Booking form submission handler
    const bookingForm = document.getElementById('booking-form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const passengerName = document.getElementById('passenger_name').value.trim();
            const flightNumber = document.getElementById('flight_number').value;
            const departureDate = document.getElementById('departure_date').value;

            if (!passengerName || !flightNumber || !departureDate) {
                alert('Please fill in all required fields.');
                return;
            }

            // Generate random price greater than 3000 Rs
            const price = (Math.floor(Math.random() * 7000) + 3001).toFixed(2);

            // Create ticket content
            const ticketContent = 
                `Passenger Name: ${passengerName}\n` +
                `Flight Number: ${flightNumber}\n` +
                `Departure Date: ${departureDate}\n` +
                `Price: Rs. ${price}\n` +
                `Thank you for booking with Aerospace Airways!`;

            // Create a blob and trigger download
            const blob = new Blob([ticketContent], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `ticket_${passengerName.replace(/\s+/g, '_')}_${flightNumber}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            // Optionally reset the form
            bookingForm.reset();
        });
    }
});
