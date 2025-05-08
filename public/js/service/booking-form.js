document.addEventListener('DOMContentLoaded', () => {
    let currentStep = 1;

    const steps = document.querySelectorAll('.step-content');
    const stepIndicators = document.querySelectorAll('[id^="step-indicator-"]');
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');
    const serviceOptions = document.querySelectorAll('input[name="service_type"]');
    const dateInput = document.getElementById('date');
    const serviceIdInput = document.getElementById('service_id');
    const servicePriceInput = document.getElementById('service_price');
    const serviceDurationInput = document.getElementById('service_duration');
    const summaryElements = {
        service: document.getElementById('summary-service'),
        price: document.getElementById('summary-price'),
        duration: document.getElementById('summary-duration'),
        date: document.getElementById('summary-date'),
        time: document.getElementById('summary-time'),
        vehicle: document.getElementById('summary-vehicle'),
        plate: document.getElementById('summary-plate'),
        notes: document.getElementById('summary-notes'),
        year: document.getElementById('summary-year'),
        color: document.getElementById('summary-color'),
        vin: document.getElementById('summary-vin')
    };
    const notesContainer = document.querySelector('.notes-container');

    const goToStep = (step) => {
        steps.forEach(s => s.classList.add('hidden'));
        stepIndicators.forEach((indicator, idx) => {
            const isCompleted = idx + 1 < step;
            const isCurrent = idx + 1 === step;
            indicator.className = `relative flex items-center justify-center w-10 h-10 rounded-full ${isCompleted || isCurrent ? 'bg-blue-600 text-white' : 'bg-white text-gray-400 border-2 border-gray-200'}`;
        });
        document.getElementById(`step-${step}`).classList.remove('hidden');
        currentStep = step;

        const prevStepBtn = document.getElementById('prevStepBtn');
        const nextStepBtn = document.getElementById('nextStepBtn');
        const submitBtn = document.getElementById('submitBtn');

        if (prevStepBtn) prevStepBtn.classList.toggle('hidden', step === 1);
        const isLastStep = step === steps.length;
        if (nextStepBtn) nextStepBtn.classList.toggle('hidden', isLastStep);
        if (submitBtn) submitBtn.classList.toggle('hidden', !isLastStep);

        if (step === 4) updateSummary();
    };

    const validateStep = (step) => {
        if (step === 1) return Array.from(serviceOptions).some(option => option.checked);
        if (step === 2) return dateInput.value && document.querySelector('input[name="time"]:checked');
        if (step === 3) return ['vehicle_brand', 'vehicle_model', 'vehicle_year', 'plate_number']
            .every(id => document.getElementById(id).value);
        return true;
    };

    const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });

    const formatTime = (timeStr) => timeStr.substring(0, 5);

    const updateSummary = () => {
        const selectedService = document.querySelector('input[name="service_type"]:checked');
        if (selectedService) {
            summaryElements.service.textContent = selectedService.value;
            summaryElements.price.textContent = `Rp ${parseInt(selectedService.dataset.price).toLocaleString('id-ID')}`;
            summaryElements.duration.textContent = `${selectedService.dataset.duration} jam`;
        }
        if (dateInput.value) summaryElements.date.textContent = formatDate(dateInput.value);
        const selectedTime = document.querySelector('input[name="time"]:checked');
        if (selectedTime) summaryElements.time.textContent = formatTime(selectedTime.value);

        // Update vehicle details in summary
        const vehicleBrand = document.getElementById('vehicle_brand').value;
        const vehicleModel = document.getElementById('vehicle_model').value;
        summaryElements.vehicle.textContent = vehicleBrand && vehicleModel ? `${vehicleBrand} ${vehicleModel}` : '-';
        summaryElements.plate.textContent = document.getElementById('plate_number').value || '-';
        summaryElements.year.textContent = document.getElementById('vehicle_year').value || '-';
        summaryElements.color.textContent = document.getElementById('vehicle_color').value || '-';
        summaryElements.vin.textContent = document.getElementById('vehicle_vin').value || '-';

        // Update notes section
        const notes = document.getElementById('notes').value;
        notesContainer.classList.toggle('hidden', !notes);
        summaryElements.notes.textContent = notes || '';
    };

    const fetchTimeSlots = () => {
        const serviceId = serviceIdInput.value;
        const date = dateInput.value;
        if (!serviceId || !date) return;

        const url = `/service/get-times?service_id=${encodeURIComponent(serviceId)}&date=${encodeURIComponent(date)}`;
        const timeSlotsContainer = document.getElementById('time-slots-container');
        timeSlotsContainer.innerHTML = '<p class="col-span-3 text-center py-4">Loading available time slots...</p>';
        const currentSelectedTime = document.querySelector('input[name="time"]:checked')?.value;

        fetch(url)
            .then(response => response.ok ? response.json() : Promise.reject())
            .then(data => {
                const timeSlots = Array.isArray(data) ? data : [];
                if (timeSlots.length) {
                    updateTimeSlots(timeSlots, currentSelectedTime);
                } else {
                    timeSlotsContainer.innerHTML = '<p class="col-span-3 text-center py-4">No available time slots.</p>';
                    if (nextButtons[1]) nextButtons[1].disabled = true;
                }
            })
            .catch(() => {
                timeSlotsContainer.innerHTML = '<p class="col-span-3 text-center text-red-500 py-4">Failed to load time slots.</p>';
                if (nextButtons[1]) nextButtons[1].disabled = true;
            });
    };

    const updateTimeSlots = (timeSlots, previouslySelectedTime = null) => {
        const timeSlotsContainer = document.getElementById('time-slots-container');
        timeSlotsContainer.innerHTML = '';
        timeSlots.forEach(slot => {
            const time = slot.time.substring(0, 5);
            const isSelected = previouslySelectedTime === slot.time;
            const label = document.createElement('label');
            label.className = `time-slot px-4 py-3 border rounded-lg flex items-center justify-center cursor-pointer ${isSelected ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-blue-200'}`;
            label.innerHTML = `
                    <input type="radio" name="time" value="${slot.time}" ${isSelected ? 'checked' : ''} class="sr-only">
                    <span>${time}</span>
                `;
            timeSlotsContainer.appendChild(label);
        });
        document.querySelectorAll('.time-slot input[type="radio"]').forEach(slot => {
            slot.addEventListener('change', () => {
                document.querySelectorAll('.time-slot').forEach(card => card.className = 'time-slot px-4 py-3 border rounded-lg flex items-center justify-center cursor-pointer border-gray-200 hover:border-blue-200');
                slot.closest('.time-slot').className = 'time-slot px-4 py-3 border rounded-lg flex items-center justify-center cursor-pointer border-blue-600 bg-blue-50';
                if (nextButtons[1]) nextButtons[1].disabled = false;
            });
        });
        if (nextButtons[1]) nextButtons[1].disabled = !document.querySelector('input[name="time"]:checked');
    };

    nextButtons.forEach(button => button.addEventListener('click', () => {
        if (validateStep(currentStep)) goToStep(currentStep + 1);
    }));

    prevButtons.forEach(button => button.addEventListener('click', () => goToStep(currentStep - 1)));

    serviceOptions.forEach(option => option.addEventListener('change', function () {
        serviceIdInput.value = this.dataset.id;
        servicePriceInput.value = this.dataset.price;
        serviceDurationInput.value = this.dataset.duration;
        document.querySelectorAll('.service-option').forEach(card => card.className = 'service-option p-4 border rounded-lg cursor-pointer transition-all border-gray-200 hover:border-blue-200');
        this.closest('.service-option').className = 'service-option p-4 border rounded-lg cursor-pointer transition-all border-blue-600 bg-blue-50';
        fetchTimeSlots();
    }));

    dateInput.addEventListener('change', fetchTimeSlots);

    ['vehicle_brand', 'vehicle_model', 'vehicle_year', 'plate_number', 'vehicle_vin', 'vehicle_color'].forEach(id => {
        document.getElementById(id).addEventListener('input', () => {
            if (nextButtons[2]) nextButtons[2].disabled = !validateStep(3);
        });
    });

    goToStep(currentStep);
});