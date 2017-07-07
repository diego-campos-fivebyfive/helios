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

var idPage = 0;
function newPage() {
    var conjunct = $("#conjunct").clone().removeAttr('id');
    $(conjunct).attr("id","conjunct_"+idPage);
    $(conjunct).attr("data-position",idPage);

    var page = $("#page").clone().removeAttr('id');
    $(page).attr("id","page_"+idPage);
    /*idPage++;*/

    var btSessions = $("#btSessions").clone().removeAttr('id');
    $(btSessions).attr("id","btSessions_"+idPage);


    // alert($(btSessions).children().length);
    for (n=0;n<$(btSessions).children().length;n++){
        var btn = $(btSessions).children()[n];
        $(btn).attr("data-pg","page_"+idPage);
        $(btn).attr("data-pos",idPage);

    }

    $("#bloco").append(conjunct);
    $("#conjunct_"+idPage+"").append(btSessions);
    $("#conjunct_"+idPage+"").append(page);
    var separator = $('#separator').clone().removeAttr('id');
    $("#conjunct_"+idPage+"").append(separator);

    idPage++;

}

function delSes(btn) {
    var decision = confirm("Deseja excluir esta sessão?");
    if (decision === true){
        $(btn).parent().remove();
    }
}

//
var i = 0;
var s = 0;
function set(btn) {

    var dataModel = $(btn).data("model");
    var session;

    if(dataModel==="12-0"){
        session = $("#sessao-12").clone().removeAttr('id');
    }else if (dataModel==="6-6"){
        session = $("#sessao-6-6").clone().removeAttr('id');
    }else if (dataModel==="4-4-4"){
        session = $("#sessao-4-4-4").clone().removeAttr('id');
    }else if (dataModel==="12-6-6"){
        session = $("#sessao-12-6-6").clone().removeAttr('id');
    }else if (dataModel==="del"){
        var page = $(btn).data("pg");
        var conj = $("#"+page+"").parent();
        console.log($(conj)[0]);
        $(conj)[0].remove();
        return;
    }

    cont = 0;
    for (n=1;n<session.children().length;n++){
        cont++;
        var editor = session.children()[n];
        //console.log(editor);
        $(editor).attr("id","editor_"+i);
        $(editor).attr("contenteditable","true");

        /*$(editor).attr("class","col-lg-12 editor");*/

        i++;

    }
    i-=cont;
    var pg = $(btn).data("pg");
    $("#"+pg+"").append(session);

    for (n = 0;n<session.children().length;n++){
        CKEDITOR.inline( "editor_"+i, {
            extraPlugins: 'hcard,sourcedialog,justify'
        } );
        i++;
    }

}

function up(btn) {
    var dataPosAt = $(btn).data("pos");

    var bloc = $("#bloco");

    console.log($(bloc).children()[0]);
    console.log($(bloc.children()[0]).data("position"));

    // console.log($(bloc).children());
    var newBloc;
    // console.log(bloc.children().length);
    //   alert(bloc.children().length);
    for (o=0;o<$(bloc).children().length;o++){
        // alert($(bloc.children()[o]).data("position"));

        // console.log(newBloc.html());
        var current = $(bloc.children()[o]).data("position");
        var before = $(bloc.children()[o-1]).data("position");
        var forward = $(bloc.children()[o+1]).data("position");
        //alert(dataPosAt+" e "+ current);
        /*if(dataPosAt === forward){
         //alert(dataPosAt+" e " +current);
         //newBloc[o] = $(bloc).children()[o+1];
         }else */

        if(dataPosAt === current /*&& o>0*/){
            //  alert("y");

            //$(temp).attr("data-position",23);
            $(bloc.children()[o]).attr("data-position",before);
            $(bloc.children()[o-1]).attr("data-position",dataPosAt);
            //console.log($(temp).html());
            var temp = $($(bloc.children()[o]).html());
            $(bloc.children()[o]).html($(bloc.children()[o-1]).html());
            $(bloc.children()[o - 1]).html(temp);

            return;
            //
        }/*else{
         newBloc[o] = $(bloc).children()[o];
         }*/

        //console.log($(bloc).children()[o]);

    }
}

function down(btn) {
    var dataPosAt = $(btn).data("pos");

    var bloc = $("#bloco");
    // console.log($(bloc).children()[0]);

    for (o=0;o<$(bloc).children().length;o++){

        var current = $(bloc.children()[o]).data("position");
        var before = $(bloc.children()[o-1]).data("position");
        var forward = $(bloc.children()[o+1]).data("position");

        if(dataPosAt === current && o<$(bloc).children().length - 1){

            $(bloc.children()[o]).attr("data-position",forward);
            $(bloc.children()[o+1]).attr("data-position",dataPosAt);

            var temp = $($(bloc.children()[o]).html());
            $(bloc.children()[o]).html($(bloc.children()[o+1]).html());
            $(bloc.children()[o + 1]).html(temp);
            return;

        }
    }
}