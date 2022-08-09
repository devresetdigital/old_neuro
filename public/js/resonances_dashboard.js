$(document).ready(function () {
    $.fn.dataTable.ext.errMode = 'none';
    initView();
    $('#campaignSelector').change(async function () {
        $('#filters-container-loading').show();
        $('#filters-container').hide();
       
        $('#topRalatedResonancesContainer-loading').show();
        $('#topRalatedResonancesContainer').hide();
        
        await getData();

        loadSignals();
        loadFilters();
        loadTops();
    });
    $('#filterNetwork').change(async function () {
        $('#topRalatedResonancesContainer-loading').show();
        $('#reportTableContainer-loading').show();
        $('#reportTableContainer').hide();
        $('#topRalatedResonancesContainer').hide();

    });

    $('#filterAd').change(async function () {
        $('#topRalatedResonancesContainer-loading').show();
        $('#reportTableContainer-loading').show();
        $('#reportTableContainer').hide();
        $('#topRalatedResonancesContainer').hide();

        updateSignalsContainer();
        
    });

    $('#filterNetworkType').change(async function () {
        $('#topRalatedResonancesContainer-loading').show();
        $('#reportTableContainer-loading').show();
        $('#reportTableContainer').hide();
        $('#topRalatedResonancesContainer').hide();

    });
    $('#filterProgram').change(async function () {
        $('#topRalatedResonancesContainer-loading').show();
        $('#reportTableContainer-loading').show();
        $('#reportTableContainer').hide();
        $('#topRalatedResonancesContainer').hide();

    });
    $('#filterProgramGenre').change(async function () {
        $('#topRalatedResonancesContainer-loading').show();
        $('#reportTableContainer-loading').show();
        $('#reportTableContainer').hide();
        $('#topRalatedResonancesContainer').hide();
    
    });


    $('#organization').change(async function () {
        await updateAdvertisers();
    });
    $('#advertiser').change(async function () {
        await updateCampaignsSelector();
    });


    //Get the button
    var mybutton = $("#scrollTopBtn");
    
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function () { scrollFunction() };

    function scrollFunction() {
        if (document.body.scrollTop > 40 || document.documentElement.scrollTop > 40) {
            mybutton.fadeIn(600);
        } else {
            mybutton.fadeOut(600);
        }
    }

});

let signals = [];
let filters = {
    signals:[],
    need_states:[],
    soma_semantics:[],
    mythic_narratives:[]
};

let tops = [];


const resize = () => {
setTimeout(() => {
    window.dispatchEvent(new Event('resize'));
}, 200);

}  

const getData = async () => {


    filters.signals = [
       
    ];

    filters.need_states = [
        {id: 1, value: 'Appreciation'},
        {id: 2, value: 'Approval'},
        {id: 3, value: 'Acceptance'},
        {id: 4, value: 'Protection'},
        {id: 5, value: 'Freedom'},
        {id: 6, value: 'Strength'},
        {id: 7, value: 'Respect'},
        {id: 8, value: 'Intelligence'},
        {id: 9, value: 'Pleasure'},
        {id: 10, value: 'Comfort'},
        {id: 11, value: 'Privacy'},
        {id: 12, value: 'Pity'},
        {id: 13, value: 'Caretaker'},
        {id: 14, value: 'Attractiveness'},
        {id: 15, value: 'Uniqueness'},
        {id: 16, value: 'Admiration'},
        {id: 17, value: 'Success'}
    ];

    filters.soma_semantics = [
        {id: 1, value: 'NeuroBias-SysOpen (-1-)/SysClosed (-0-)'},
        {id: 2, value: 'Meta (-0-) Forward (-2-),BackWard (-1-)'},
        {id: 3, value: 'Balance (scale 1-5)'},
        {id: 4, value: 'Flex (scale 1-5)'},
        {id: 5, value: 'AMSR Quotient (Sound effect scale (0-5))'},
    ];


    filters.mythic_narratives = [
        {id: 1, value: 'Leader'},
        {id: 2, value: 'Caregiver'},
        {id: 3, value: 'Seducer'},
        {id: 4, value: 'Castaway'},
        {id: 5, value: 'Rebel'},
        {id: 6, value: 'Wildcard'},
        {id: 7, value: 'Professor'},
        {id: 8, value: 'Warrior'}
    ];

    filters.pathosEthos = [
        {id: 1, value: 'Pathos'},
        {id: 2, value: 'Ethos'}
    ];
    
    
    let campaign = $('#campaignSelector').val();
    if (campaign != '') {
        await $.get("/api/get_signals_by_campaign/"+campaign , function (data) {
            
            signals = { ...data };
            for (const iterator of data) {
                filters.signals.push( {id: iterator.data.id, value: iterator.data.name})
            }
            
        });
    }
    
}

const loadSignals = () => {
    $('#reportWarning').empty();
    $('#signalsCardContainer').empty();
   
    if (Object.keys(signals).length === 0){
        let html = `<h3 style="text-align:center; margin:2em">The report is under review, it will be displayed soon</h3>`;
        $('#reportWarning').append(html);
        return true;
    }
    for (var key in signals) {

        let html=`
            <div class="card-body signals-card-body" style="padding-down: 0 !important;">
                <div class="card " id="adCardContainer-${signals[key].data.id}" style="height: 80%;">
                    <div class="card-body" style="padding: 0.5em;">
                        <p style="margin-left: 1em;"><b>${signals[key].data.name}</b></p>
                        <hr style="margin: 10px;">
                        <div class="col-md-3 form-group text-center vertival-center " >
                            <div class="preview-container" style="">
                                <div style="background-image: url(${signals[key].data.preview_url});
                                background-position: center;
                                background-repeat: no-repeat;
                                background-size: contain;
                                height: 18em !important;
                                padding: 50%;
                                padding-bottom: 100%;
                                border-radius: 10px 10px 10px 10px;
                                -moz-border-radius: 10px 10px 10px 10px;
                                -webkit-border-radius: 10px 10px 10px 10px;
                                border: 2px solid #cbcbcb; margin-bottom: 1em;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9" >
                            <ul class="nav nav-tabs" style="z-index: 1;position: inherit;">
                                <li role="presentation" class="active">
                                    <a data-toggle="tab" href="#needstateChart-${signals[key].data.id}">Ontological Goal States</a>
                                </li>
                                <li role="presentation">
                                    <a data-toggle="tab" href="#somaSemanticChart-${signals[key].data.id}">Soma Semantic</a>
                                </li>
                                <li role="presentation">
                                    <a data-toggle="tab" href="#mythicNarrativeChart-${signals[key].data.id}">Mythic Narrative</a>
                                </li>
                                <li role="presentation">
                                    <a data-toggle="tab" onClick="resize()" href="#pathosEthosChart-${signals[key].data.id}">Pathos/Ethos</a>
                                </li>
                            </ul>
                            <div class="tab-content" style="">
                                <div id="needstateChart-${signals[key].data.id}" class="tab-pane fade in active" >
                                    <canvas class="canvas-graph" id="ns-chart-${signals[key].data.id}" ></canvas>
                                </div>
                                <div id="somaSemanticChart-${signals[key].data.id}" class="tab-pane fade" style="max-width: 77%;">
                                    <canvas class="canvas-graph" id="sose-chart-${signals[key].data.id}" style="position: absolute; top:-3em"></canvas>
                                </div>
                                <div id="mythicNarrativeChart-${signals[key].data.id}" class="tab-pane fade" style="max-width: 54%;">
                                    <canvas class="canvas-graph"id="mn-chart-${signals[key].data.id}"></canvas>
                                </div>
                                <div id="pathosEthosChart-${signals[key].data.id}" class="tab-pane fade" >
                                    <figure class="highcharts-figure">
                                        <div class="canvas-graph" id="pa-chart-${signals[key].data.id}"></div>
                                        <div class="canvas-graph" id="et-chart-${signals[key].data.id}"></div>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;  

        $('#signalsCardContainer').append(html);


        /**
         * 
         * needstate chart
         */

        let labels = signals[key].need_states.map( a => a.name);
        let data = {
            labels: labels,
            datasets: [{
                label: 'Ontological Goal States',
                data:signals[key].need_states.map( a => a.average),
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.4
            }]
        };
        let config = {
            type: 'line',
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        };

        let myChart = new Chart(
            document.getElementById('ns-chart-'+signals[key].data.id),
            config
        );


        /**
         * 
         * soma chart
         */
         
        labels =signals[key].soma_semantics.map( a => a.name);
        data = {
           labels: labels,
           datasets: [
                {
                label: 'Soma Semantic',
                data: signals[key].soma_semantics.map( a => a.average),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgb(75, 192, 192,0.3)',
                tension:0.4
                }
            ]
        };

        config = {
            type: 'radar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            },
          };

        myChart = new Chart(
            document.getElementById('sose-chart-'+signals[key].data.id),
            config
        );

        /**
         * 
         * myhic Narrative
         */
         
         labels =signals[key].mythic_narratives.map( a => a.name);


         let colors = [
            '#FF000073',
            '#FF800073',
            '#FFFF0073',
            '#80FF0073',
            '#00FF0073',
            '#00FF8073',
            '#0080FF73',
            '#0000FF73',
            '#7F00FF73',
            '#e6191973',
            '#e6801973',
            '#e5e61973',
            '#80e61973',
            '#19e61973',
            '#1980e673',
            '#1980e673',
            '#1919e673',
            '#7f19e673'
         ];
  

         data = {
           labels: labels,
           datasets: [
             {
               label: 'Dataset 1',
               data:signals[key].mythic_narratives.map( a => a.score),
                backgroundColor: function(context) {
              
                    const chartArea = context.chart.chartArea;
                    if (!chartArea) {
                      // This case happens on initial chart load
                      return;
                    }
                  
                    const chartWidth = chartArea.right - chartArea.left;
                    const chartHeight = chartArea.bottom - chartArea.top;
                    

                    width = chartWidth;
                    height = chartHeight;
                    const centerX = (chartArea.left + chartArea.right) / 2;
                    const centerY = (chartArea.top + chartArea.bottom) / 2;
                    const r = Math.min(
                      (chartArea.right - chartArea.left) / 2,
                      (chartArea.bottom - chartArea.top) / 2
                    );
                    const ctx = context.chart.ctx;
                    let gradient = ctx.createRadialGradient(centerX, centerY, 0, centerX, centerY, r);


                    // Create a radial gradient
                    // The inner circle is at x=110, y=90, with radius=30
                    // The outer circle is at x=100, y=100, with radius=70
                    //var gradient = ctx.createRadialGradient(110,90,30, 100,100,70);
                    
                    // Add three color stops
                    gradient.addColorStop(0, colors[context.dataIndex]);
                    gradient.addColorStop(.5, colors[context.dataIndex+1]);
                    gradient.addColorStop(1, colors[context.dataIndex+2]);
                    
                    // Set the fill style and draw a rectangle
                    return gradient;
                }
             }
           ]
         };
          config = {
            type: 'polarArea',
            data: data,
            options: {
              responsive: true,
             
            },
        };


        myChart = new Chart(
            document.getElementById('mn-chart-'+signals[key].data.id),
            config
        );


        /*

            PATHOS ETHOS

        */

        var gaugeOptions = {
            chart: {
                type: 'solidgauge'
            },
        
            title: null,
        
            pane: {
                center: ['50%', '85%'],
                size: '140%',
                startAngle: -90,
                endAngle: 90,
                background: {
                    backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || '#EEE',
                    innerRadius: '60%',
                    outerRadius: '100%',
                    shape: 'arc'
                }
            },
        
            exporting: {
                enabled: false
            },
        
            tooltip: {
                enabled: false
            },
        
            // the value axis
            yAxis: {
                stops: [
                    [0.1,'#DF5353'], // red
                    [0.5, '#DDDF0D'], // yellow
                    [0.9,  '#55BF3B'] // green
                ],
                lineWidth: 0,
                tickWidth: 0,
                minorTickInterval: null,
                tickAmount: 2,
                title: {
                    y: -70
                },
                labels: {
                    y: 16
                }
            },
        
            plotOptions: {
                solidgauge: {
                    dataLabels: {
                        y: 5,
                        borderWidth: 0,
                        useHTML: true
                    }
                }
            }
        };
        

        // The speed gauge
        var chartSpeed = Highcharts.chart('pa-chart-'+signals[key].data.id, Highcharts.merge(gaugeOptions, {
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: ''
                }
            },
        
            credits: {
                enabled: false
            },
        
            series: [{
                name: '',
                data: [parseFloat(signals[key].pathos_ethos[0].score)],
                dataLabels: {
                    format:
                        '<div style="text-align:center">' +
                        '<span style="font-size:25px">{y} %</span><br/>' +
                        '<span style="font-size:18px;opacity:0.4">Emotional Appeal</span>' +
                        '</div>'
                },
                tooltip: {
                    valueSuffix: ''
                }
            }]
        
        }));

        
        // The RPM gauge
        var chartRpm = Highcharts.chart('et-chart-'+signals[key].data.id, Highcharts.merge(gaugeOptions, {
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: ''
                }
            },
        
            series: [{
                name: '',
                data: [parseFloat(signals[key].pathos_ethos[1].score)],
                dataLabels: {
                    format:
                        '<div style="text-align:center">' +
                        '<span style="font-size:25px">{y:.1f} %</span><br/>' +
                        '<span style="font-size:18px;opacity:0.4">Ethical Appeal' +
                        '</span>' +
                        '</div>'
                },
                tooltip: {
                    valueSuffix: ''
                }
            }]
        
        }));
        
    }

    const myCanvas = $('.canvas-graph');
    const updateCanvas = () => ({width: window.innerWidth, height: window.innerHeight}); 
    updateCanvas(); 
    window.addEventListener('resize', updateCanvas()); 

    setTimeout(() => {
        window.dispatchEvent(new Event('resize'));
    }, 500);
  
    return true;
}


const loadFilters = () => {
    $('#filters-container').slideUp();

    FillSelector('#filterSignal', filters.signals, true);
    FillSelector('#filterNeedStates', filters.need_states, true);
    FillSelector('#filterSomaSemantic', filters.soma_semantics, true);
    FillSelector('#filterMythicNarrative', filters.mythic_narratives, true);
    FillSelector('#filterPathosEthos', filters.pathosEthos, true);

    $('#filters-container').slideDown(500);

    $('#filters-container-loading').hide();
}

const loadTops = () => {

    
    
    $('#top-ns').empty();
    $('#top-sose').empty();
    $('#top-my').empty();

    let top_personal = [];
    let top_soma = [];
    let top_mythic = [];

    let suitability_name='';
    let suitability_score=0;

    for (var key in signals) {

        if(suitability_score == 0 || suitability_score < signals[key].data.suitability_score){
            suitability_name = signals[key].data.name;
            suitability_score = signals[key].data.suitability_score;
        }

        signals[key].need_states.forEach(element => {
            
            let index = top_personal.findIndex(aux => aux.name == element.name);
            if(index == -1){
                top_personal.push(element);
            }else{
                if(top_personal[index].average < element.average){
                    top_personal[index] = element;
                }
            }
        });

        signals[key].soma_semantics.forEach(element => {
            let index = top_soma.findIndex(aux => aux.name == element.name);
            if(index == -1){
                top_soma.push(element);
            }else{
                if(top_soma[index].average < element.average){
                    top_soma[index] = element;
                }
            }
        });

        signals[key].mythic_narratives.forEach(element => {
            let index = top_mythic.findIndex(aux => aux.name == element.name);
            if(index == -1){
                top_mythic.push(element);
            }else{
                if(top_mythic[index].score < element.score){
                    top_mythic[index] = element;
                }
            }
        });


    }

    $('#best-signal-name').empty();
    $('#best-signal-score').empty();

    $('#best-signal-name').append(suitability_name);
    $('#best-signal-score').append(suitability_score + ' %');

    top_personal.sort(function(a, b){return b.average - a.average})
    top_soma.sort(function(a, b){return b.average - a.average})
    top_mythic.sort(function(a, b){return b.score - a.score})

    if(top_personal[0]==undefined)return;
 
    let li = `
        <li class="list-group-item d-flex justify-content-between align-items-center">
        ${top_personal[0].name }
                    <span class="badge badge-primary badge-circle">${top_personal[0].average } %</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
        ${top_personal[1].name }
                    <span class="badge badge-primary badge-circle">${top_personal[1].average } %</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
        ${top_personal[2].name }
                    <span class="badge badge-primary badge-circle">${top_personal[2].average } %</span>
        </li>
    `;

    $('#top-ns').append(li);

    li = `
    <li class="list-group-item d-flex justify-content-between align-items-center">
    ${ top_soma[0].name }
                <span class="badge badge-primary badge-circle">${top_soma[0].average }</span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
    ${ top_soma[1].name }
                <span class="badge badge-primary badge-circle">${top_soma[1].average }</span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
    ${ top_soma[2].name }
                <span class="badge badge-primary badge-circle">${top_soma[2].average }</span>
    </li>
    `;

    $('#top-sose').append(li);


    li = `
    <li class="list-group-item d-flex justify-content-between align-items-center">
    ${ top_mythic[0].name }
                <span class="badge badge-primary badge-circle">${top_mythic[0].score }</span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
    ${ top_mythic[1].name }
                <span class="badge badge-primary badge-circle">${top_mythic[1].score }</span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
    ${ top_mythic[2].name }
                <span class="badge badge-primary badge-circle">${top_mythic[2].score }</span>
    </li>
    `;
   
    $('#top-my').append(li);

    $('#topRalatedResonancesContainer').show();
    $('#topRalatedResonancesContainer-loading').hide();

}

function getRandomArbitrary(min, max) {
    return parseInt( Math.random() * (max - min) + min);
  }

const initView = () => {
    $('#filters-container-loading').hide();
    $('#topRalatedResonancesContainer-loading').hide();
    $('#topRalatedResonancesContainer').hide();
    $('#groupbyContainer').hide();
    $('#chartByDaypartContainer').hide();
    $('#filters-container').slideUp();
}


const updateAdvertisers = async () => {
    let advertisers = [];
    
    let org = $('#organization').val();
    if (org != '') {
        await $.get("/api/advertisers?organization="+org , function (data) {
            advertisers = { ...data };
        });
    }
    

    let options = []
    for (const iterator in advertisers) {
        options.push({
            value: advertisers[iterator].name,
            id : advertisers[iterator].id
        });
    }

    FillSelector_simple('#advertiser', options, false);

}

const updateCampaignsSelector = async () => {
    
    let advertiser = $('#advertiser').val();
    
    let campaignData = [];
   
    if (advertiser != '') {
        await $.get("/api/rsn_get_signal_campaigns_by_advertiser?advertiser="+advertiser+"&type=hao" , function (data) {
            campaignData = { ...data };
        });
    }
 
    let options = []


    for (const iterator in campaignData) {
        options.push({
            value: campaignData[iterator].name,
            id : campaignData[iterator].id
        });
    }

    FillSelector_simple('#campaignSelector', options, false);
}

const topFunction = () => {
    $('html,body').animate({ scrollTop: 0 }, 'slow');
}



//AUXILIAR FUNCTIONS

const FillSelector = (selector, options, selectAll) => {
    $(selector).multiselect('destroy');
    $(selector).empty();
    for (const option of options) {
        let newOption = new Option(option.value, option.id, selectAll, selectAll);
        $(selector).append(newOption);
    }
    initMultiSelect(selector);
}
const FillSelector_simple = (selector, options) => {
    $(selector).empty();
    $(selector).append( new Option('None', ''));
    for (const option of options) {
        let newOption = new Option(option.value, option.id);
        $(selector).append(newOption);
    }
}


const initMultiSelect = (selector) => {
    $(selector).multiselect({
        enableFiltering: true,
        includeSelectAllOption: true,
        enableCaseInsensitiveFiltering: true,
        buttonText: function (options) {
            if (options.length == 0) {
                return 'None selected';
            } else {
                var selected = 0;
                options.each(function () {
                    selected += 1;
                });
                return selected + ' Selected';
            }
        }
    });
}

/*
	inclusive min (result can be equal to min value)
    exclusive max (result will not be to max value)
*/
const randomizeInteger = (min, max) => {
    if (max == null) {
        max = (min == null ? Number.MAX_SAFE_INTEGER : min);
        min = 0;
    }

    min = Math.ceil(min);  // inclusive min
    max = Math.floor(max); // exclusive max

    if (min > max - 1) {
        throw new Error("Incorrect arguments.");
    }

    return min + Math.floor((max - min) * Math.random());
}


const formatPercentage = (number) => {
    if( typeof number == 'number'){
        return roundTwoDecimal(number) + " %";
    }
    return number;
}

const roundTwoDecimal = (number) => {
    return Math.round((number + Number.EPSILON) * 100) / 100;
}