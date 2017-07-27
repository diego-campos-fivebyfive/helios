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
    swal({
        title: "Confirma exclusão?",
        text: "Todo o conteúdo da página será perdido",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false
    }, function () {
        var conj = $("#page_"+idPage+"").parent();
        $(conj)[0].remove();
        sweetAlert("Sucesso!", "Página excluída com sucesso!", "success");
        window.setTimeout(function(){
            swal.close();
        }, 1000);
    });
}

function delSessionModal(btThis) {
    swal({
        title: "Confirma exclusão?",
        text: "Todo o conteúdo da sessão será perdido",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false
    }, function () {
        $(btThis).parent().remove();
        sweetAlert("Sucesso!", "Sessão excluída com sucesso!", "success");
        window.setTimeout(function(){
            swal.close();
        }, 1000);
    });
}

var generate_project = false;
var generate_financial = false;

/*coloca os novos dados nas tags drag e no ckEditor*/
function loadDatas() {
    /*só carrega se os dois graficos estiverem gerados*/
    if(generate_project === true && generate_financial === true){
        $('#bloco').removeClass('hidden');
        $('#idNewPage').removeClass('hidden');

        var tagsDrag = document.getElementById("tagsProposal");
        for (n = 0; n < $(tagsDrag).children().length; n++) {
            /*pega o id de cada tag*/
            var idTag = $($(tagsDrag).children()[n]).attr("id");
            /*pega todos os elementos com classe relacionada a essa
             tag na proposta para recebimento dos dados*/
            var contentOfTag = document.getElementsByClassName("d" + idTag);

            if ($("#d" + idTag + "").data("type") === 'chart') {
                for (x = 0; x < $(contentOfTag).length; x++) {
                    var src = $($($(contentOfTag)[x]).children()[0]).attr('src');
                    var newSrc;
                    /*se ele esta no editor só troca o src, else if se tem o id entao é tag drag ai gera a img*/
                    if (!(src === undefined)) {
                        if($("#d"+idTag+"").data("chart") === 'generation'){
                            newSrc = chartGeneration();
                            $($($(contentOfTag)[x]).children()[0]).attr('src',newSrc);
                        }else if($("#d"+idTag+"").data("chart") === 'financial'){
                            newSrc = chartFinancial();
                            $($($(contentOfTag)[x]).children()[0]).attr('src',newSrc);
                        }

                    } else if ((src === undefined) && !($($(contentOfTag)[x]).attr('id') === undefined)) {
                        /*o id é usado apenas para verificar se é tag drag*/
                        $($(contentOfTag)[x]).attr('id','');
                        if($("#d"+idTag+"").data("chart") === 'generation'){
                            newSrc = chartGeneration();
                            var image = generateImage(newSrc);
                            $(contentOfTag[x]).html('');
                            $($(contentOfTag)[x]).attr('data-typeChart','generation');
                            $($($(contentOfTag)[x])).attr('data-color', $('#colorChartGeneration').val());
                            $($(contentOfTag)[x]).append(image);
                        }else if($("#d"+idTag+"").data("chart") === 'financial'){
                            newSrc = chartFinancial();
                            var image = generateImage(newSrc);
                            $(contentOfTag[x]).html('');
                            $($(contentOfTag)[x]).attr('data-typeChart','financial');
                            $($($(contentOfTag)[x])).attr('data-color', $('#colorChartFinancial').val());
                            $($(contentOfTag)[x]).append(image);
                        }
                    }
                }
            } else {
                var content = $("#d" + idTag + "").html();
                /*coloca em todas as tags que encontrou na proposta*/
                for (x = 0; x < $(contentOfTag).length; x++) {
                    $(contentOfTag[x]).html(content);
                }
            }
        }
    }
}

/*
$('#saveProposal').click(function () {
    saveProposal(this);
});
*/

$('#pdfProposal').click(function () {
    var saveBtn = $('#saveProposal');
    $(saveBtn).ladda();
    saveBtn.ladda('start');
    var pdfBtn = $('#pdfProposal').ladda();
    pdfBtn.ladda('start');
    $.ajax({
        url:$(saveBtn).data('url'),
        method:'post',
        data:{content:$('#bloco').html()},
        complete:function (xhr) {
            saveBtn.ladda('stop');
            pdfBtn.ladda('stop');
            alert('Gerar pdf');
        }
    })
});

function saveProposal() {
    var saveBtn = $('#saveProposal');
    $(saveBtn).ladda();
    saveBtn.ladda('start');
    var pdfBtn = $('#pdfProposal').ladda();
    pdfBtn.ladda('start');
    $.ajax({
        url:$(saveBtn).data('url'),
        method:'post',
        data:{content:$('#bloco').html()},
        complete:function (xhr) {
            saveBtn.ladda('stop');
            pdfBtn.ladda('stop');
            setTimeout(function () {
                saveProposal();
            },1000);
        }
    })
}
var aa = 1;
$(document).ready(function(){
    generateChart();
    setTimeout(function () {
        saveProposal();
    },1000);

    //alert(aa);aa++;
    CKEDITOR.on('instanceReady', function (ev) {
        // Prevent drag-and-drop.
        ev.editor.document.on('drop', function (ev) {
           /* console.log(ev.data);*/

            //ev.data.preventDefault(true);
        });
    });
   /* setInterval(function () {
        var btnSave = $('#saveProposal');
        saveProposal(btnSave);
    },60000);*/
});

function generateChart() {

    var colorGeneration = null;
    var imageContainers = $('[data-typeChart="generation"]');
    $.each(imageContainers, function(i, c){
        var container = $(c);
        colorGeneration = container.attr('data-color');
    });
    if(colorGeneration===null){
        colorGeneration = $('#colorChartGeneration').val();
    }
    $('#colorChartGeneration').val(colorGeneration);

    var colorFinancial = null;
    var gimageContainers = $('[data-typeChart="financial"]');
    $.each(gimageContainers, function(i, c){
        var container = $(c);
        colorFinancial = container.attr('data-color');
    });
    if(colorFinancial===null){
        colorFinancial = $('#colorChartFinancial').val();
    }
    $('#colorChartFinancial').val(colorFinancial);

    AppChart.projectChart({
        element:'project_chart',
        data: $('#dgenerationChart').data('json'),
        fillColor: "#"+colorGeneration,
        strokeColor: "#868686",
        animationSteps: 30,
        pointColor: "rgba(26,179,148,0.75)",
        callback: function () {
            generate_project = true;
            loadDatas();
        }
    });
    AppChart.financialChart({
        fillColor: "#"+colorFinancial,
        strokeColor: "#868686",
        pointColor: "#B8C1C3",
        animationSteps: 31,
        data: $('#daccumulatedCashChart').data('json'),
        title: false,
        element: 'financial_chart',
        callback: function () {
            generate_financial = true;
            loadDatas();
        }
    });
}
function chartGeneration() {
    return AppChart.getDataUrl('project_chart');
}
function chartFinancial() {
    return AppChart.getDataUrl('financial_chart');
}

function changeColorGeneration(color){
    var canvas = $('#project_chart').clone().removeAttr('id');
    $(canvas).attr('id','project_chart_2');
    var tempColor = $('#canvasChartGeneration');
    $(tempColor).html('');
    $(tempColor).append(canvas);

    AppChart.projectChart({
        element:'project_chart_2',
        data: $('#dgenerationChart').data('json'),
        fillColor: "#"+color,
        strokeColor: "#868686",
        pointColor: "rgba(26,179,148,0.75)",
        animationSteps: 1,
        callback: function () {
            $('#generationChart').children().html(generateImage(AppChart.getDataUrl('project_chart_2')));
            $('#generationChart').children().attr('data-typeChart','generation');
            $('#generationChart').children().attr('data-color',color);

            var imageContainers = $('[data-typeChart="generation"]');
            $.each(imageContainers, function(i, c){
                var container = $(c);
                var image = container.find('img');
                //console.log(container.attr('data-typeChart'));
                if(image.length){
                    image.attr('src',AppChart.getDataUrl('project_chart_2'));
                    container.attr('data-color',color);
                    //console.log(image.attr('src'));
                }
            });
        }
    });
}

function changeColorFinancial(color){
    var canvas = $('#financial_chart').clone().removeAttr('id');
    $(canvas).attr('id','financial_chart_2');
    var tempColor = $('#canvasChartFinancial');
    $(tempColor).html('');
    $(tempColor).append(canvas);

    AppChart.financialChart({
        fillColor: "#"+color,
        strokeColor: "#868686",
        pointColor: "#B8C1C3",
        animationSteps: 1,
        data: $('#daccumulatedCashChart').data('json'),
        title: false,
        element: 'financial_chart_2',
        callback: function () {
            $('#accumulatedCashChart').children().html(generateImage(AppChart.getDataUrl('financial_chart_2')));
            $('#accumulatedCashChart').children().attr('data-typeChart','financial');
            $('#accumulatedCashChart').children().attr('data-color',color);

            var imageContainers = $('[data-typeChart="financial"]');
            $.each(imageContainers, function(i, c){
                var container = $(c);
                var image = container.find('img');
                if(image.length){
                    image.attr('src',AppChart.getDataUrl('financial_chart_2'));
                    container.attr('data-color',color);
                }
            });
        }
    });
}

function generateImage(src) {
    var image = new Image();
    image.src = src;
    image.setAttribute('style','width: 100%;');
    //$(image).attr('style','width: 100%;');
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
