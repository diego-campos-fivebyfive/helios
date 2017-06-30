$(function () {
    // Initialize grid editor
    $('#myGrid').gridEditor({
        new_row_layouts: [[12], [6, 6], [9, 3], [3, 9], [4, 4, 4]],
        content_types: ['ckeditor'],
        ckeditor: {
            config: {
                on: {
                    instanceReady: function (evt) {
                        var instance = this;
                        // console.log('instance ready', evt, instance);
                        // alert(instance);

                    }
                }
            }
        }
    });
});

document.getElementById('infoList').addEventListener('dragstart', function drag($evt) {

    var evt = {data: {$: $evt}}; // Create CKEditor event.
    var element = $evt.target;
    var content = $(element).data('info');
    if(content == 'project_graph'){
        var clone = $('#graph').clone().removeAttr('id');
        $(clone).removeClass('hidden');
        content = $(clone).html();
        
    }else if(content == 'project_items'){
        var clone = $('#items').clone().removeAttr('id');
        $(clone).removeClass('hidden');
        content = $(clone).html();
    }
    // Crie a fachada de transferência de dados para que possa definir tipos de dados personalizados.
    CKEDITOR.plugins.clipboard.initDragDataTransfer(evt);
    evt.data.dataTransfer.setData('infoList', true);
    // Passa o que vai ser mostrado
    evt.data.dataTransfer.setData('text/html', content);
});


/*

 var cache_width = $('#myGrid').width(); //Criado um cache do CSS
 var a4 = [595.28, 841.89]; // Widht e Height de uma folha a4

 function getPDF() {
 //console.log($("#myGrid"));
 $("#myGrid").removeClass('ge-editing');
 // Setar o width da div no formato a4
 $("#myGrid").width((a4[0] * 1.33333) - 80).css('max-width', 'none');


 */

/*   var canvas = document.getElementById("thecanvas");
 var ctx = canvas.getContext("2d");
 ctx.fillStyle = "rgba(125, 46, 138, 0.5)";
 ctx.fillRect(25,25,100,100);
 ctx.fillStyle = "rgba( 0, 146, 38, 0.5)";
 ctx.fillRect(58, 74, 125, 100);
 canvas = document.getElementById("thecanvas");
 //   $(canvas).html($("#myGrid"));
 // var img = canvas.toDataURL();
 //   Canvas2Image.saveAsPNG(canvas);
 var doc = new jsPDF({unit: 'px', format: 'a4'});
 doc.addImage(canvas, 'png', 20, 20);
 doc.save('Proposta.pdf');
 //Retorna ao CSS normal
 $('#myGrid').width(cache_width);
 $("#myGrid").addClass('ge-editing');
 */
/*
 // Aqui ele cria a imagem e cria o pdf
 html2canvas($('#myGrid'), {
 onrendered: function (canvas) {
 var img = canvas.toDataURL("image/png", 1.0);
 var doc = new jsPDF({unit: 'px', format: 'a4'});
 doc.addImage(img, 'png', 20, 20);
 doc.save('Proposta.pdf');
 //Retorna ao CSS normal
 $('#myGrid').width(cache_width);
 $("#myGrid").addClass('ge-editing');
 }
 });
 }

 var interval = window.setInterval(function () {
 var exist = $('.ge-editing').length ? true : false;
 if (exist == true) {
 $('#btnSavePdf').removeAttr('disabled');
 clearInterval(interval);
 }
 }, 500);

 $('#btnSavePdf').click(function () {
 getPDF();
 });
 */
