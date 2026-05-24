/**
 * Modal and Toast Utility Functions
 * Reusable across the application
 */

/**
 * Show toast notification
 * @param {string} msg - Message to display
 * @param {string} type - 'success' or 'error'
 */
const showToast = (msg, type = 'success') => {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const toast = $(`<div class="fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg font-bold text-base z-50">${msg}</div>`);
    $('body').append(toast);
    setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3000);
};

/**
 * AJAX handler with automatic error handling
 * @param {object} config - AJAX config object
 */
const sendAjax = (config) => {
    $.ajax({
        ...config,
        error: function () {
            showToast(config.errorMsg || 'Request failed', 'error');
        }
    });
};

/**
 * Toggle modal visibility
 * @param {string} modalId - Modal element ID
 * @param {boolean} show - Show or hide modal
 */
const toggleModal = (modalId, show) => {
    const $modal = $(`#${modalId}`);
    if (show) {
        $modal.removeClass('hidden').addClass('flex');
    } else {
        $modal.addClass('hidden').removeClass('flex');
        $modal.find('form')[0]?.reset();
        $modal.find('.error').addClass('hidden').text('');
    }
};

/**
 * Setup modal event listeners
 * @param {string} modalId - Modal element ID
 * @param {string} openBtnId - Open button ID
 * @param {string} closeBtnId - Close button ID
 * @param {string} cancelBtnId - Cancel button ID
 */
const setupModalListeners = (modalId, openBtnId, closeBtnId, cancelBtnId) => {
    $(`#${openBtnId}`).click(() => toggleModal(modalId, true));
    $(`#${closeBtnId}`).click(() => toggleModal(modalId, false));
    $(`#${cancelBtnId}`).click(() => toggleModal(modalId, false));
    $(document).click((e) => $(e.target).is(`#${modalId}`) && toggleModal(modalId, false));
};
