document.getElementById('infoList').addEventListener('dragstart', function drag($evt) {

    var evt = {data: {$: $evt}}; // Create CKEditor event.
    var element = $evt.target;
    var content = $(element).data('info');

    var clone = $('#'+content).clone().removeAttr('id');
    content = $(clone).html();

    // Crie a fachada de transferência de dados para que possa definir tipos de dados personalizados.
    CKEDITOR.plugins.clipboard.initDragDataTransfer(evt);
    evt.data.dataTransfer.setData('infoList', true);
    // Passa o que vai ser mostrado
    evt.data.dataTransfer.setData('text/html', content);
});

//var idPage = 0;
function newPage() {
    var idPage = $("#idConjunct").html();

    var conjunct = $("#conjunct").clone().removeAttr('id');
    $(conjunct).attr("id","conjunct_"+idPage);
    $(conjunct).attr("data-position",idPage);

    var page = $("#page").clone().removeAttr('id');
    $(page).attr("id","page_"+idPage);

    //bts principais
    var topPageButtons = $("#topPageButtons").clone().removeAttr('id');
    $(topPageButtons).attr("id","topPageButtons_"+idPage);
    for (n=0;n<$(topPageButtons).children().length;n++){
        var btM = $(topPageButtons).children()[n];
        if(n===0){
           // $(btM).attr("data-target","#idModal_"+idPage);
            $(btM).attr("onclick","addSessionModal("+idPage+")");
        }else if(n===1) {
            $(btM).attr("onclick","delPageModal("+idPage+")");
        }else{
           // $(btM).attr("data-pg","page_"+idPage);
            $(btM).attr("data-pos",idPage);
        }
    }

    //adiciona no bloco
    $("#bloco").append(conjunct);
    $("#conjunct_"+idPage+"").append(topPageButtons);
   $("#conjunct_"+idPage+"").append(page);
    var separator = $('#separator').clone().removeAttr('id');
    $("#conjunct_"+idPage+"").append(separator);
    idPage++;

    $("#idConjunct").html(idPage);
}

function addSessionModal(idPage) {
    //bt nova sessao
    var btSessionsInsideTheModal = $("#btSessionsInsideTheModal").clone().removeAttr('id');

    for (n=0;n<$(btSessionsInsideTheModal).children().length;n++){
        var btn = $(btSessionsInsideTheModal).children()[n];
        $(btn).attr("data-pg","page_"+idPage);
    }
    //modal sessao
    var modal = $("#idModal")/*.clone().removeAttr('id')*/;
    $($(modal[0]).children().children().children()[1]).html('<p>Escolha uma sessão.</p>');
    $($(modal[0]).children().children().children()[1]).append(btSessionsInsideTheModal);
}

function setSession(btn) {

    var idEditor = $("#idEditor").html();

    var dataModel = $(btn).data("model");
    var session;

    if(dataModel==="12-0"){
        session = $("#sessao-12").clone().removeAttr('id');
    }else if (dataModel==="6-6"){
        session = $("#sessao-6-6").clone().removeAttr('id');
    }else if (dataModel==="8-4"){
        session = $("#sessao-8-4").clone().removeAttr('id');
    }else if (dataModel==="4-8"){
        session = $("#sessao-4-8").clone().removeAttr('id');
    }else if (dataModel==="4-4-4"){
        session = $("#sessao-4-4-4").clone().removeAttr('id');
    }else if (dataModel==="12-6-6"){
        session = $("#sessao-12-6-6").clone().removeAttr('id');
    }

    cont = 0;
    for (n=1;n<session.children().length;n++){
        cont++;
        var editor = session.children()[n];
        $(editor).attr("id","editor_"+idEditor);
        $(editor).attr("contenteditable","true");
        idEditor++;
    }
    idEditor-=cont;
    var pg = $(btn).data("pg");
    $("#"+pg+"").append(session);
    for (n = 1;n<session.children().length;n++){
        CKEDITOR.inline( "editor_"+idEditor, {
            extraPlugins: 'hcard,sourcedialog,justify'
        } );
        idEditor++;
    }
    $("#idEditor").html(idEditor);
}

function delPageModal(idPage) {
    //bt Delete
    var btDeleteInsideTheModal = $("#btDeleteInsideTheModal").clone().removeAttr('id');

    for (n=0;n<$(btDeleteInsideTheModal).children().length;n++) {
        var btD = $(btDeleteInsideTheModal).children()[n];
        $(btD).attr("data-pg", "page_" + idPage);
    }
    //modal delete
    var modalDel = $("#idModalDel");
    $($(modalDel[0]).children().children().children()[1]).html('');
    $($(modalDel[0]).children().children().children()[1]).append(btDeleteInsideTheModal);
}

function delPage(btn) {
    var page = $(btn).data("pg");
    var conj = $("#"+page+"").parent();
    // console.log($(conj)[0]);
    $(conj)[0].remove();
}

function delSes(btn) {
    var decision = confirm("Deseja excluir esta sessão?");
    if (decision === true){
        $(btn).parent().remove();
    }
}

/*coloca os novos dados nas tags drag e no ckEditor*/
$(document).ready(function(){
    /*pega as tags ocultas de dragAndDrop disponiveis para uso*/
    var tags = document.getElementById("tagsProposal");

    for (n = 0;n<$(tags).children().length;n++){
        /*pega o id de cada tag*/
        var idTag = $($(tags).children()[n]).attr("id");
        /*pega todos os elementos com classe relacionada a essa
        tag na proposta para recebimento dos dados*/
        var contentOfTag = document.getElementsByClassName("d"+idTag);
        /*pega conteúdo a ser colocado nas tags*/
        /*e se for base64 transforma em img*/
        if($("#d"+idTag+"").data("type")==='base64'){
            /*coloca em todas as tags que encontrou na proposta*/
            for(x=0;x<$(contentOfTag).length;x++){
                //console.log($($(contentOfTag)[x]).attr('id'));
                var src = $($($(contentOfTag)[x]).children()[0]).attr('src');

                if(!(src===undefined)){
                    $($($(contentOfTag)[x]).children()[0]).attr('src','data:image/png;base64,'+$("#d"+idTag+"").html());
                }else if((src===undefined) && !($($(contentOfTag)[x]).attr('id')===undefined)){
                    $($(contentOfTag)[x]).attr('id','');
                    var image = decodeBase64($("#d"+idTag+"").html());
                    $(contentOfTag[x]).html('');
                    $($(contentOfTag)[x]).append(image);
                }
            }
        }else {
            var content = $("#d"+idTag+"").html();
            /*coloca em todas as tags que encontrou na proposta*/
            for(x=0;x<$(contentOfTag).length;x++){
                $(contentOfTag[x]).html(content);
            }
        }
    }
});

function decodeBase64(base64) {
     var image = new Image();
     image.src = 'data:image/png;base64,'+base64;
     //document.body.appendChild(image);
     return image;
}

function up(btn) {
    var dataPosAt = $(btn).data("pos");
    var bloc = $("#bloco");

    for (o=0;o<$(bloc).children().length;o++){
        var current = $(bloc.children()[o]).data("position");

        if(dataPosAt === current && o>0){
            var temp = bloc.children()[o];
            var prev = bloc.children()[o-1];
            $( $( '#'+$(temp).attr('id') ) ).insertBefore( $( '#'+$(prev).attr('id') )  );
            return;
        }
    }
}

function down(btn) {
    var dataPosAt = $(btn).data("pos");
    var bloc = $("#bloco");

    for (o=0;o<$(bloc).children().length;o++){
        var current = $(bloc.children()[o]).data("position");

        if(dataPosAt === current && o<$(bloc).children().length - 1){

            var temp = bloc.children()[o];
            var next = bloc.children()[o+1];
            $( $( '#'+$(temp).attr('id') ) ).insertAfter( $( '#'+$(next).attr('id') )  );
            return;
        }
    }
}
