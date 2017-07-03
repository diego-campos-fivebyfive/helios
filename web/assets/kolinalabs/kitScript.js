$(document).ready(function () {
    countpos = 1;
    $('#modulo_qtd').mask('00000', {reverse: true});
    $('#inversor_qtd').mask('00000', {reverse: true});
    $('#extra_qtd').mask('00000', {reverse: true});
    $('#inversor_preco').mask('000000000.00', {reverse: true});
    $('#modulo_preco').mask('000000000.00', {reverse: true});
    $('#inversor_preco').mask('000000000.00', {reverse: true});
    $('#extra_preco').mask('000000000.00', {reverse: true});
    $('#servico_valor').mask('000000000.00', {reverse: true});

    $('#modulo_qtd').on("keyup", function () {
        total_md();
    });
    $('#modulo_preco').on("keyup", function () {
        total_md();
    });
    $('#inversor_qtd').on("keyup", function () {
        total_inv();
    });
    $('#inversor_preco').on("keyup", function () {
        total_inv();
    });
    $(document).on("click", ".removebutton", function () {
        remove_row(this);
    });
}
);
function tabela_geral_calc() {
    var table = $('#tabela_geral');
    var total = 0;
    table.find('tr').each(function (indice) {
        $(this).find('td').each(function (indice) {
            if (indice === 6) {
                valor = parseFloat((this.innerText));
                total += valor;
            }
        });
    });
    $("#tabela_geral_total").val(total);
}

function tabela_extra_calc() {
    var table = $('#tabela_extra');
    var total = 0;
    table.find('tr').each(function (indice) {
        $(this).find('td').each(function (indice) {
            if (indice === 3) {
                valor = parseFloat((this.innerText));
                total += valor;
            }
        });
    });

    $("#tabela_extra_total").val(total);
}

function tabela_servico_calc() {
    var table = $('#tabela_servico');
    var total = 0;
    table.find('tr').each(function (indice) {
        $(this).find('td').each(function (indice) {
            if (indice === 2) {
                valor = parseFloat((this.innerText));
                total += valor;
            }
        });
    });

    $("#valor_total_servico").val(total);
}

function extra_add() {

    extra_id = $("#extra_id").val();
    extra_preco = parseFloat($("#extra_preco").val());
    extra_quantidade = parseInt($("#extra_qtd").val());
    tablerow = '<tr>';
    tablerow += '<td>' + extra_id + '</td>';
    tablerow += '<td>' + extra_preco + '</td>';
    tablerow += '<td>' + extra_quantidade + '</td>';
    tablerow += '<td>' + (extra_quantidade * extra_preco).toFixed(2) + '</td>';
    tablerow += '<td>' +
            '<i class="text text-danger fa fa-times-circle-o removebutton"></i>'
            + '</td>';
    tablerow += '</tr>';
    $("#tabela_extra").append(tablerow);
    tablerow = '';
    $("#extra_id").val("");
    $("#extra_preco").val("");
    $("#extra_qtd").val("");
    swal("Produto Adicionado!", "Produto adicionado com sucesso !", "success");
    update_total_geral();
}

function modulo_add() {
    md_fabricante = $("#modulo_fabricante").val();
    md_serie = $("#modulo_serie").val();
    md_id = $("#modulo_id").val();
    md_quantidade = $("#modulo_qtd").val();
    md_preco = $("#modulo_preco").val();
    tablerow = '<tr>';
    tablerow += '<td>' + countpos + '</td>';
    tablerow += '<td>' + "Módulo" + '</td>';
    tablerow += '<td>' + md_fabricante + '</td>';
    tablerow += '<td>' + md_serie + '</td>';
    tablerow += '<td>' + md_id + '</td>';
    tablerow += '<td>' + md_quantidade + '</td>';
    tablerow += '<td>' + (parseInt(md_quantidade) * parseFloat(md_preco)).toFixed(2) + '</td>';
    tablerow += '<td>' +
            '<i class="text text-danger fa fa-times-circle-o removebutton"></i>'
            + '</td>';
    tablerow += '</tr>';
    countpos += 1;
    $("#tabela_geral").append(tablerow);
    tablerow = '';
    swal("Produto Adicionado!", "Produto adicionado com sucesso !", "success");
    $("#modulo_id").val("");
    $("#modulo_qtd").val("");
    $("#modulo_preco").val("");
    update_total_geral();
}

function inversor_add() {
    inv_fabricante = $("#inversor_fabricante").val();
    inv_serie = $("#inversor_serie").val();
    inv_id = $("#inversor_id").val();
    inv_quantidade = $("#inversor_qtd").val();
    inv_preco = $("#inversor_preco").val();
    tablerow = '<tr>';
    tablerow += '<td>' + countpos + '</td>';
    tablerow += '<td>' + "Inversor" + '</td>';
    tablerow += '<td>' + inv_fabricante + '</td>';
    tablerow += '<td>' + inv_serie + '</td>';
    tablerow += '<td>' + inv_id + '</td>';
    tablerow += '<td>' + inv_quantidade + '</td>';
    tablerow += '<td>' + (parseInt(inv_quantidade) * parseFloat(inv_preco)).toFixed(2) + '</td>';
    tablerow += '<td>' +
            '<i class="text text-danger fa fa-times-circle-o removebutton"></i>'
            + '</td>';
    tablerow += '</tr>';
    countpos += 1;
    $("#tabela_geral").append(tablerow);
    tablerow = '';
    $("#inversor_id").val("");
    $("#inversor_qtd").val("");
    $("#inversor_preco").val("");

    update_total_geral();
    swal("Produto Adicionado!", "Produto adicionado com sucesso !", "success");

}

function total_md() {
    md_quantidade = $("#modulo_qtd").val();
    md_preco = $("#modulo_preco").val();
    if (!isNaN(parseInt(md_quantidade) * parseInt(md_preco))) {
        $("#modulo_total").val((parseInt(md_quantidade) * parseFloat(md_preco)).toFixed(2));
    } else {
        $("#modulo_total").val(0);
    }
}
function total_inv() {
    inv_quantidade = $("#inversor_qtd").val();
    inv_preco = $("#inversor_preco").val();
    if (!isNaN(parseInt(inv_quantidade) * parseInt(inv_preco))) {
        $("#inversor_total").val((parseInt(inv_quantidade) * parseFloat(inv_preco)).toFixed(2));
    } else {
        $("#inversor_total").val(0);
    }
}

function remove_row(row) {
    
}

function update_total_geral() {
    tabela_extra_calc();
    tabela_geral_calc();
    tabela_servico_calc();
    //nf_frete_calc();
    final_calc();
}

function add_servico() {
    servico_nome = $("#servico_nome").val();
    servico_tipo = $("#tipo_servico").val();
    servico_valor = $("#servico_valor").val();
    tablerow = '<tr>';
    tablerow += '<td>' + servico_nome + '</td>';
    tablerow += '<td>' + servico_tipo + '</td>';
    tablerow += '<td>' + servico_valor + '</td>';
    tablerow += '<td>' +
            '<i class="text text-danger fa fa-times-circle-o removebutton"></i>'
            + '</td>';
    tablerow += '</tr>';
    $("#tabela_servico").append(tablerow);
    tablerow = '';
    $("#servico_nome").val("");
    $("#servico_valor").val("");
    swal("Serviço Adicionado!", "Serviço adicionado com sucesso !", "success");
    update_total_geral();
}

function final_calc() {
    geral = parseFloat($("#tabela_geral_total").val());
    extra = parseFloat($("#tabela_extra_total").val());
    servico = parseFloat($("#valor_total_servico").val());
    $('#valor_total_kit').val(geral + extra + servico);
}
