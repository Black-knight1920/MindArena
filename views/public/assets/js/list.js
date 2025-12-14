// Get modal and close button references
const editUserModal = document.getElementById('editUserModal');
const editModalClose = document.getElementById('editModalClose');

// Open modal and fill form
function openEditModal(userData) {
    document.getElementById('editUserId').value = userData.id;
    document.getElementById('editName').value = userData.name;
    document.getElementById('editEmail').value = userData.email;
    document.getElementById('editDob').value = userData.dob;
    document.getElementById('editDonation').value = userData.donation;

    editUserModal.style.display = 'flex';
}

// Close modal
editModalClose.addEventListener('click', () => {
    editUserModal.style.display = 'none';
});

// Close modal when clicking outside the modal content
window.addEventListener('click', (e) => {
    if (e.target === editUserModal) {
        editUserModal.style.display = 'none';
    }
});

// Attach event listeners to all edit buttons
document.querySelectorAll('.btn-edit').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();

        const userData = {
            id: button.getAttribute('data-user-id'),
            name: button.getAttribute('data-name'),
            email: button.getAttribute('data-email'),
            dob: button.getAttribute('data-dob'),
            donation: button.getAttribute('data-donation')
        };

        openEditModal(userData);
    });
});
