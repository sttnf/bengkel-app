document.addEventListener('DOMContentLoaded', function() {
    const alertDivs = document.querySelectorAll('.bg-red-100, .bg-green-100');
    alertDivs.forEach(alertDiv => {
        const closeButton = alertDiv.querySelector('svg');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                alertDiv.style.display = 'none';
            });
        } else {
            // Automatically hide after a few seconds if no close button
            setTimeout(() => {
                alertDiv.style.display = 'none';
            }, 5000); // Adjust time as needed
        }
    });
});