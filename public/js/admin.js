$('#abc-select-admin').on('change', function () {
    window.location.href = '/admin/voedingsmiddelen/letter/' + $(this).val();
});
