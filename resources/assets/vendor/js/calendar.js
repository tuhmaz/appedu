'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const eventButtons = document.querySelectorAll('.event');
  const modalEventTitle = document.getElementById('modalEventTitle');
  const modalEventDescription = document.getElementById('modalEventDescription');
  const modalEventDate = document.getElementById('modalEventDate');
  const modalEventTime = document.getElementById('modalEventTime');
  const modalEventLocation = document.getElementById('modalEventLocation');
  const modalEventStatus = document.getElementById('modalEventStatus');

  eventButtons.forEach(eventButton => {
    eventButton.addEventListener('click', function () {
      const eventTitle = this.dataset.title;
      const eventDescription = this.dataset.description;
      const eventDate = this.dataset.date;
      const eventTime = this.dataset.time;
      const eventLocation = this.dataset.location;
      const eventStatus = this.dataset.status;

      modalEventTitle.textContent = eventTitle;
      modalEventDescription.textContent = eventDescription || 'No description available';
      modalEventDate.textContent = eventDate;
      modalEventTime.textContent = eventTime || 'No time specified';
      modalEventLocation.textContent = eventLocation || 'No location specified';
      
      // تعيين لون الشارة حسب الحالة
      modalEventStatus.textContent = eventStatus;
      modalEventStatus.className = 'badge bg-' + 
        (eventStatus === 'upcoming' ? 'primary' : 
         eventStatus === 'ongoing' ? 'success' : 'secondary');

      const modal = new bootstrap.Modal(document.getElementById('eventModal'));
      modal.show();
    });
  });
});

document.addEventListener('DOMContentLoaded', function () {
   const events = document.querySelectorAll('.day.has-event');

  events.forEach(event => {
      event.addEventListener('click', function () {
           const title = this.getAttribute('data-title') || 'No Title';
          const description = this.getAttribute('data-description') || 'No Description';
          const date = this.getAttribute('data-date') || 'No Date';

           document.getElementById('modalEventTitle').textContent = title;
          document.getElementById('modalEventDescription').textContent = description;
          document.getElementById('modalEventDate').textContent = date;
      });
  });
});

// Event Modal Handling
document.addEventListener('DOMContentLoaded', function() {
    const eventModal = document.getElementById('eventModal');
    if (eventModal) {
        eventModal.addEventListener('hidden.bs.modal', function () {
            // Remove modal backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            // Remove modal-open class from body
            document.body.classList.remove('modal-open');
            // Remove inline styles from body
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
        });
    }

    // Add similar handling for other modals
    const addEventModal = document.getElementById('addEventModal');
    if (addEventModal) {
        addEventModal.addEventListener('hidden.bs.modal', function () {
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
        });
    }

    // Handle edit event modals
    const editEventModals = document.querySelectorAll('[id^="editEventModal"]');
    editEventModals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
        });
    });
});

// Handle modal focus management
const eventModal = document.getElementById('eventModal');
if (eventModal) {
  const focusableElements = eventModal.querySelectorAll(
    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
  );
  const firstFocusable = focusableElements[0];
  const lastFocusable = focusableElements[focusableElements.length - 1];

  // Store last focused element before modal
  let lastFocusedElement;

  eventModal.addEventListener('show.bs.modal', function () {
    // Store the element that was focused before opening modal
    lastFocusedElement = document.activeElement;
    // Focus first focusable element
    setTimeout(() => firstFocusable.focus(), 100);
  });

  eventModal.addEventListener('hide.bs.modal', function () {
    // Return focus to element that was focused before modal opened
    if (lastFocusedElement) {
      lastFocusedElement.focus();
    }
  });

  // Trap focus inside modal
  eventModal.addEventListener('keydown', function (e) {
    if (e.key === 'Tab') {
      if (e.shiftKey) {
        if (document.activeElement === firstFocusable) {
          e.preventDefault();
          lastFocusable.focus();
        }
      } else {
        if (document.activeElement === lastFocusable) {
          e.preventDefault();
          firstFocusable.focus();
        }
      }
    }
  });
}
