$('#abcSelect').on('change', function () {
    window.location.href = '/admin/voedingsmiddel/letter/' + $(this).val();
});
