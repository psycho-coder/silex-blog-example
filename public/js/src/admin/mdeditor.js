(function($){

    function preview(source, options, callback){
        var defaults = {
            previewURL: '',
            paramName: 'content'
        }

        this.options = $.extend({}, defaults, options);

        var url  = this.options.previewURL;
        var data = {}
        data[this.options.paramName] = source;

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'html',
            data: data
        }).done(callback);
    }

    $(document).on('change', '#md-source textarea', function(e){
        $(e.target).addClass('changed');
    });

    $(document).on('show.bs.tab', 'a[href="#md-preview"]', function(e){
        var $preview = $('#md-preview');
        var $source  = $('#md-source textarea');

        // make a reuest only if source was changed or it's first time when preview tab opened
        if ( $source.hasClass('changed') || ($source.val().length > 0 && $preview.html().length == 0) ) {
            // show loader icon
            $preview.html('<img src="/img/ajax-loader.gif" class="md-loader" alt="" />');

            preview($source.val(), { previewURL: $source.data('preview-url') }, function(html){
                $preview.html(html);
                $source.removeClass('changed');
                // highlite code with Highlite.js
                $('pre code').each(function(i, e) {hljs.highlightBlock(e)});
            });
        }
    });

})(jQuery);