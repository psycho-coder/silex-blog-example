(function(){
    $('.input-group.date').datepicker({
        format: "dd.mm.yyyy",
        autoclose: true,
        language: "ru"
    });

    $(document).on('click', '[data-confirmation]', function(e){
        e.preventDefault();
        var button = $(this);

        bootbox.confirm($(button).data('confirmation'), function(result) {
            if ( result == true ) {
                window.location.replace($(button).attr('href'));
            }
        });
    });

})();


