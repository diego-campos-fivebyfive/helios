/**
 * Random Data Api Adapter
 *
 * Claudinei Machado <claudinei@kolinalabs.com>
 */
$(function(){

    var random = $('[data-random]');

    if(random.length){

        random.on('click', function (event) {
            event.preventDefault();

            var caller = $(this),
                url = caller.data('random'),
                target = $(caller.data('target')),
                prefix = caller.data('prefix'),
                content = caller.html();

            caller.html('<i class="fa fa-spinner"></i> Loading...');

            $.ajax({
                url: url,
                method: 'get',
                success: function(response){

                    $.each(response, function(id, value){
                        var field = $(prefix+''+id);

                        if(field.length){
                            field.val(value);
                        }
                    });

                    caller.html(content);
                    //console.log(response);
                }
            })
        });
    }
});
