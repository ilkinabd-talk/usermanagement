(function (cash) {
    "use strict";

    // Show modal
    cash("#programmatically-show-modal").on("click", function () {
        cash("#programmatically-modal").modal("show");
    });

    // Hide modal
    cash("#programmatically-hide-modal").on("click", function () {
        cash("#programmatically-modal").modal("hide");
    });

    // Toggle modal
    cash("#programmatically-toggle-modal").on("click", function () {
        cash("#programmatically-modal").modal("toggle");
    });

    // Handle delete modal
    cash("[data-delete]").on("click", function () {
        const recordId = cash(this).data('record-id');
        const deleteForm = cash("#delete-form");
        const link = deleteForm.attr('action').replace('[id]', recordId);
        deleteForm.attr('action', link);
        cash("#delete-modal-preview").modal("toggle");
    });

})(cash);
