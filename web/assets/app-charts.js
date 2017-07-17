var AppChart = {
    getCanvas: function(id){
        return document.getElementById(id);
    },
    getContext: function(id){
        var canvas = AppChart.getCanvas(id);
        return canvas.getContext('2d');
    },
    getDataUrl: function(id){
        return AppChart.getCanvas(id).toDataURL();
    },
    Line: function (id, data, options) {
        return new Chart(AppChart.getContext(id)).Line(data, options);
    },
    Bar: function(id, data, options){
        return new Chart(AppChart.getContext(id)).Bar(data, options);
    },
    projectChart: function(options){

        var dataSettings = {
            labels: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
            datasets: [options]
        };

        var range = AppChart.utils.range(options.data);
        var endValue = Math.ceil(range.max / 100) * 100;
        var stepValue = endValue / 10;
        var animationSteps = options.hasOwnProperty('animationSteps')?options.animationSteps:25;
        
        var chartOptions = {
            animationStartWithDataset : 1,
            animationStartWithData : 1,
            animationSteps: animationSteps,
            scaleOverride: true,
            scaleSteps: endValue / stepValue,
            scaleStepWidth: stepValue,
            scaleStartValue: 0,
            scaleEndValue: endValue,
            canvasBorders : false,
            graphTitle : "Geração Mensal (kWh)",
            graphTitleSpaceAfter: 25,
            legend : false,
            inGraphDataShow : true,
            annotateDisplay : false,
            graphTitleFontSize: 18,
            footNote: "",
            annotateLabel: "<%=(v3)%>"
        };

        if(options.hasOwnProperty('callback')){
            chartOptions.onAnimationComplete = options.callback;
        }

        AppChart.Bar(options.element, dataSettings, chartOptions);
    },
    financialChart: function(options){

        var data = options.data;
        var range = AppChart.utils.range(data);

        var labels = [0];
        for (var j = 1; j < data.length; j++) {
            labels[j] = '';
            if (data[j] < range.min) {
                range.min = data[j];
            }
            if (data[j] > range.max) {
                range.max = data[j];
            }
            labels[j] = j == data.length - 1 ? data.length - 1 : '';
        }

        var dataSettings = {
            labels: labels,
            datasets: [options]
        };

        var animationSteps = options.hasOwnProperty('animationSteps')?options.animationSteps:100;

        var chartOptions = {
            animationStartWithDataset: 1,
            animationStartWithData: 1,
            animationLeftToRight: true,
            animationByDataset: true,
            animationSteps: animationSteps,
            animationEasing: "linear",
            canvasBorders: false,
            canvasBordersWidth: 0,
            canvasBordersColor: "black",
            graphTitle: "Caixa Acumulado",
            legend: false,
            inGraphDataShow: false,
            annotateDisplay: true,
            graphTitleFontSize: 18,
            scaleOverride: true,
            scaleSteps: 9,
            scaleStartValue: range.min,
            scaleEndValue: range.max,
            scaleStepWidth: ((range.max - range.min) / 9).toFixed(0),
            scaleLabel: "<%=AppChart.utils.formatCurrency(value)%>",
            annotateLabel: "<%=AppChart.utils.formatTooltip(v3)%>",
            yAxisLabel: "Valor",
            xAxisLabel: "Anos",
            footNote: ""
        };

        if(options.hasOwnProperty('callback')){
            chartOptions.onAnimationComplete = options.callback;
        }

        AppChart.Line(options.element, dataSettings, chartOptions);

        //return new Chart(document.getElementById("chart_analysis").getContext("2d")).Line(dataSettings, chartOptions);
        //Financial.analysis_chart.Line(data, options);
    },
    utils: {
        range: function(data){
            var range = { min: data[0],  max: data[0] };
            for(var i=0; i<data.length; i++) {
                if (data[i] < range.min) {
                    range.min = data[i];
                }
                if (data[i] > range.max) {
                    range.max = data[i];
                }
            }
            return range;
        },
        addCommas: function(nStr) {
            nStr += '';
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + '.' + '$2');
            }
            return x1 + x2;
        },
        formatCurrency: function(value) {
            return 'R$ ' + AppChart.utils.addCommas(value);
        },
        formatTooltip: function(value) {
            return AppChart.utils.formatCurrency(value.toFixed(0));
        }
    }
}; 