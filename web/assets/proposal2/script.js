document.getElementById('infoList').addEventListener('dragstart', function drag($evt) {

    var evt = {data: {$: $evt}}; // Create CKEditor event.
    var element = $evt.target;
    var content = $(element).data('info');
    if(content === 'project_graph'){
        var clone = $('#graph').clone().removeAttr('id');
        content = $(clone).html();

    }else if(content === 'project_items'){
        var clone = $('#items').clone().removeAttr('id');
        content = $(clone).html();
    }else if(content === 'project_client'){
        var clone = $('#client').clone().removeAttr('id');
        content = $(clone).html();
    }else if(content === 'project_power'){
        var clone = $('#power').clone().removeAttr('id');
        content = $(clone).html();
    }
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
    var btModal = $("#btModal").clone().removeAttr('id');
    $(btModal).attr("id","btModal_"+idPage);
    for (n=0;n<$(btModal).children().length;n++){
        var btM = $(btModal).children()[n];
        if(n===0){
            $(btM).attr("data-target","#idModal_"+idPage);
        }else if(n===1) {
            $(btM).attr("data-target","#idModalDel_"+idPage);
        }else{
           // $(btM).attr("data-pg","page_"+idPage);
            $(btM).attr("data-pos",idPage);
        }
    }

    //bt nova sessao
    var btSessions = $("#btSessions").clone().removeAttr('id');
    $(btSessions).attr("id","btSessions_"+idPage);

    for (n=0;n<$(btSessions).children().length;n++){
        var btn = $(btSessions).children()[n];
        $(btn).attr("data-pg","page_"+idPage);
        //$(btn).attr("data-pos",idPage);
    }
    //modal sessao
    var modal = $("#idModal").clone().removeAttr('id');
    $(modal).attr("id","idModal_"+idPage);
    //console.log("md "+$($(modal[0]).children().children().children()[1]).html());
    $($(modal[0]).children().children().children()[1]).append(btSessions);

    //bt Delete
    var btDelete = $("#btDelete").clone().removeAttr('id');
    $(btDelete).attr("id","btDelete_"+idPage);
    for (n=0;n<$(btDelete).children().length;n++) {
        var btD = $(btDelete).children()[n];
        $(btD).attr("data-pg", "page_" + idPage);
    }
    //modal delete
    var modalDel = $("#idModalDel").clone().removeAttr('id');
    $(modalDel).attr("id","idModalDel_"+idPage);
   // console.log("mddd "+$($(modalDel[0]).children().children().children()[1]).html());

    $($(modalDel[0]).children().children().children()[1]).append(btDelete);

    //adiciona no bloco
    $("#bloco").append(conjunct);
    $("#conjunct_"+idPage+"").append(btModal);
    $("#conjunct_"+idPage+"").append(modal);
    $("#conjunct_"+idPage+"").append(modalDel);
    $("#conjunct_"+idPage+"").append(page);
    var separator = $('#separator').clone().removeAttr('id');
    $("#conjunct_"+idPage+"").append(separator);
    idPage++;

    $("#idConjunct").html(idPage);
}

function delSes(btn) {
    var decision = confirm("Deseja excluir esta sessão?");
    if (decision === true){
        $(btn).parent().remove();
    }
}


$(document).ready(function(){
    var editors = document.getElementsByClassName("edit");
    console.log($($(editors)).length);
    for (n = 1;n<$($(editors)).length;n++){
        if(! ($($(editors)[n]).attr("id") === undefined)){
            console.log($($(editors)[n]).attr("id"));
            CKEDITOR.inline( $($(editors)[n]).attr("id"), {
                extraPlugins: 'hcard,sourcedialog,justify'
            } );
        }

    }
});

function set(btn) {

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
    }else if (dataModel==="del"){
        var page = $(btn).data("pg");
        var conj = $("#"+page+"").parent();
       // console.log($(conj)[0]);
        $(conj)[0].remove();
        return;
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