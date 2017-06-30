var Http = {
    continue: 100,
    processing: 102,
    ok: 200,
    success: 200,
    created: 201,
    accepted: 202,
    no_content: 204,
    reset_content: 205,
    im_used: 226,
    found: 302,
    not_found: 404,
    conflict: 409,
    request: function(options){
        var config = $.extend({
            method: 'post',
            data: {}
        }, options);
        $.ajax({
            type: config.method,
            url: config.url,
            data: config.data,
            complete: function(xhr){
                if(config.hasOwnProperty('callback')){
                    config.callback(xhr);
                }
                if(200 == xhr.status){
                    if(config.hasOwnProperty('json')){
                        config.json(xhr.responseJSON);
                    }
                    if(config.hasOwnProperty('content')){
                        config.content(xhr.responseText);
                    }
                    if(config.hasOwnProperty('render')){
                        var render = 'object'  != typeof config.render ? $(config.render) : config.render;
                        if(render.length){
                            render.html(xhr.responseText);
                        }
                    }
                }
            },
            beforeSend: function(){
                if(config.hasOwnProperty('onRequest')){
                    config.onRequest();
                }
            }
        });
    }
};