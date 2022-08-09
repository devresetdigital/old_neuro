$(document).ready(function () {
    $.fn.dataTable.ext.errMode = 'none';
    initView();
    $('#campaignSelector').change(async function () {
        $('#filters-container-loading').show();
        $('#filters-container').hide();
       
        $('#topRalatedResonancesContainer-loading').show();
        $('#reportTableContainer-loading').show();
        $('#reportTableContainer').hide();
        $('#topRalatedResonancesContainer').hide();

        $('#verticalChartContainer').hide();
        
        await updateFilters();
        loadAds();
        addLift();
        await updateResonace();
        updateHorizontalChart();
        updateVerticalChart();
    });
    $('#filterNetwork').change(async function () {
        $('#topRalatedResonancesContainer-loading').show();
        $('#reportTableContainer-loading').show();
        $('#reportTableContainer').hide();
        $('#topRalatedResonancesContainer').hide();

        await updateResonace();
        await updateVerticalChart('NETWORK_NAME');
    });

    $('#filterAd').change(async function () {
        $('#topRalatedResonancesContainer-loading').show();
        $('#reportTableContainer-loading').show();
        $('#reportTableContainer').hide();
        $('#topRalatedResonancesContainer').hide();

        updateAdsContainer();
        await updateResonace();
        await updateVerticalChart(true);

    });

    $('#filterNetworkType').change(async function () {
        $('#topRalatedResonancesContainer-loading').show();
        $('#reportTableContainer-loading').show();
        $('#reportTableContainer').hide();
        $('#topRalatedResonancesContainer').hide();

        await updateResonace();
        await updateVerticalChart('NETWORK_TYPE');
    });
    $('#filterProgram').change(async function () {
        $('#topRalatedResonancesContainer-loading').show();
        $('#reportTableContainer-loading').show();
        $('#reportTableContainer').hide();
        $('#topRalatedResonancesContainer').hide();

        await updateResonace();
        await updateVerticalChart('PROGRAM_TITLE');
    });
    $('#filterProgramGenre').change(async function () {
        $('#topRalatedResonancesContainer-loading').show();
        $('#reportTableContainer-loading').show();
        $('#reportTableContainer').hide();
        $('#topRalatedResonancesContainer').hide();

        await updateResonace();
        await updateVerticalChart('PROGRAM_GENRE');
    });
    $('#ChartBy').change(async function () {
        updateVerticalChart();
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

let ReportData;
let netsData
let reporTable;
let adsIdLoaded;
let campaignData;
let daypartsNames  = [];
let daypartsData = [];


const updateAdvertisers = async () => {
    

    let org = $('#organization').val();

    let advertisers = [];
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
        await $.get("/api/rsn_get_campaigns_by_advertiser?&email="+emailLogedin+"&advertiser="+advertiser , function (data) {
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

const updateAdsContainer = () => {
    for (const iterator of adsIdLoaded) {
        $('#adCardContainer-' + iterator).hide();
    }

    let ads_ids = $('#filterAd').val();
    for (const iterator of ads_ids) {
        $('#adCardContainer-' + iterator).show();
    }

}

const updateResonace = async () => {

    let campaingSelected = $('#campaignSelector').val();
    let networks_ids = $('#filterNetwork').val();
    let ads_ids = $('#filterAd').val();
    let network_types_ids = $('#filterNetworkType').val();
    let programs_ids = $('#filterProgram').val();
    let program_genres_ids = $('#filterProgramGenre').val();


    $.post("/api/rsn_resonance/", {
        ads_ids: JSON.stringify(ads_ids),
        networks_ids: JSON.stringify(networks_ids),
        network_types_ids: JSON.stringify(network_types_ids),
        programs_ids: JSON.stringify(programs_ids),
        program_genres_ids: JSON.stringify(program_genres_ids)
    },
    function (data) {
        let resonanceAverage = roundTwoDecimal( parseFloat(data.resonance_average));
        $('#resonancePercentage').text(resonanceAverage + ' %');
    }, 'json');
    

    netsData = [];

    $.post("/api/rsn_get_top_networks/", {
        ads_ids: JSON.stringify(ads_ids),
        networks_ids: JSON.stringify(networks_ids),
        network_types_ids: JSON.stringify(network_types_ids),
        limit:10
    },
    function (data) {
        let auxCount = 0;
        let netIndexes = [];
        $('#topNetworksContainer').empty();
       
        for (const iterator of data) {
            if (auxCount >= 3) { break; }
          
            if(netIndexes.includes(iterator.rsn_networks.name) === false) {
              
                let li = `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                ` + iterator.rsn_networks.name + `
                            <span class="badge badge-primary badge-circle">` + roundTwoDecimal(parseFloat(iterator.net_average)) + ` %</span>
                </li>
                `;
                $('#topNetworksContainer').append(li);
                auxCount += 1;
                netIndexes.push(iterator.rsn_networks.name)
            }
        }
    }, 'json');

    daypartsData = [];
    await $.get("/api/rsn_resonance_by_daypart/" + campaingSelected, function (data) {
        daypartsData = { ...data };
    });


    await $.post("/api/rsn_get_top_programs/", {
        ads_ids: JSON.stringify(ads_ids),
        programs_ids: JSON.stringify(programs_ids),
        program_genres_ids: JSON.stringify(program_genres_ids),
        limit:10,
        type: 'PROGRAM_TITLE'
    },
    function (data) {
        
        $('#topProgramsContainer').empty();
        let auxCount = 0;
        auxCount = 0;
        for (const iterator of data.programs) {
            if (auxCount >= 3) { break; }
            let li = `
            <li class="list-group-item d-flex justify-content-between align-items-center">
            ` + iterator.rsn_programs.name + `
                        <span class="badge badge-primary badge-circle">` + roundTwoDecimal(parseFloat(iterator.net_average)) + ` %</span>
            </li>
            `;
            $('#topProgramsContainer').append(li);
            auxCount += 1;
        }

        //update topDayparts as well


        $('#topDaypartContainer').empty();

        auxCount = 0;
        for (const iterator of data.dayparts) {
            if (auxCount >= 3) { break; }
            let li = `
            <li class="list-group-item d-flex justify-content-between align-items-center">
            ` + iterator.rsn_dayparts.name + `
                        <span class="badge badge-primary badge-circle">` + roundTwoDecimal(parseFloat(iterator.net_average)) + ` %</span>
            </li>
            `;
            $('#topDaypartContainer').append(li);
            auxCount += 1;
        }
          






    }, 'json');

  

    

    UpdateReportTable();
    $('#reportTableContainer').show();
    $('#reportTableContainer-loading').hide();

    $('#topRalatedResonancesContainer').show();
    $('#topRalatedResonancesContainer-loading').hide();
    
}

const UpdateReportTable = async () => {
    appenDataHeaders = [
        'Ads',
        'Network Name',
        'Network Type',
        'Program Genre',
        'Program Title',
    ];

    let dayPartLabels = [];

    for (const iterator of Object.keys(daypartsData.resumedkeys)) {
        dayPartLabels.push(iterator);
    }
    appenDataHeaders = [...appenDataHeaders, ...dayPartLabels];

    appenDataHeaders.push('Tags');
    appenDataHeaders.push('Tags Matched');
    appenDataHeaders.push('Resonance Avg');


    let dataTableCollumns= [];

    for (const iterator of appenDataHeaders) {
        $('#reportTableHeaders').append('<th>' + iterator + '</th>');
        dataTableCollumns.push({ "data": iterator });
    }

    let networks_ids = $('#filterNetwork').val();
    let ads_ids = $('#filterAd').val();
    let network_types_ids = $('#filterNetworkType').val();
    let programs_ids = $('#filterProgram').val();
    let program_genres_ids = $('#filterProgramGenre').val();

    $('#reportTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": false,
        "ajax":{
                 "url": "/api/rsn_resonance_paginated/",
                 "dataType": "json",
                 "type": "POST",
                 "data": {
                        ads_ids: JSON.stringify(ads_ids),
                        networks_ids: JSON.stringify(networks_ids),
                        network_types_ids: JSON.stringify(network_types_ids),
                        programs_ids: JSON.stringify(programs_ids),
                        program_genres_ids: JSON.stringify(program_genres_ids),
                        columns: appenDataHeaders,
                        daypartLabels:dayPartLabels
                    }
               },
        "columns": dataTableCollumns	 
    });

}


const UpdateReportTable_bkp = (ReportData) => {
    if (reporTable) { reporTable.destroy(); }

    $('#groupbyContainer').hide();
    $('#reportTableHeaders').empty();
    $('#reportTableBody').empty();

    let appenDataHeaders = [];

    let groupBy = $('#filterGroupBy').val();
    if (groupBy.length == 0) {

        groupBy = ['ADS', 'NETWORK_NAME', 'NETWORK_TYPE', 'PROGRAM_GENRE', 'PROGRAM_TITLE',];

        appenDataHeaders = [
            'Ads',
            'Network Name',
            'Network Type',
            'Program Genre',
            'Program Title',
        ];
    } else {
        for (const iterator of groupBy) {
            switch (iterator) {
                case 'ADS':
                    appenDataHeaders.push('Ads');
                    break;
                case 'NETWORK_NAME':
                    appenDataHeaders.push('Network Name');
                    break;
                case 'NETWORK_TYPE':
                    appenDataHeaders.push('Network Type');
                    break;
                case 'PROGRAM_GENRE':
                    appenDataHeaders.push('Program Genre');
                    break;
                case 'PROGRAM_TITLE':
                    appenDataHeaders.push('Program Title');
                    break;
                default:
                    break;
            }
        }

    }


    let dayPartLabels = [];
    let dayPartObject = {};


    for (const iterator of campaignData.rsn_ads) {
        for (const daypart of iterator.DaypartsNames) {
            if (dayPartLabels.findIndex(aux => aux == daypart.name) === -1) {
                dayPartLabels.push(daypart.name)
                dayPartObject = { ...dayPartObject, ... { [daypart.name]: false } }
            }
            daypartsNames.push(daypart);
        }
    }



    appenDataHeaders = [...appenDataHeaders, ...dayPartLabels];

    appenDataHeaders.push('Tags');
    appenDataHeaders.push('Tags Matched');
    appenDataHeaders.push('Resonance Avg');


    for (const iterator of appenDataHeaders) {
        $('#reportTableHeaders').append('<th>' + iterator + '</th>');
    }

    let GroupedData = [];


    let AD_data = {};
    for (const iterator of campaignData.rsn_ads) {
        AD_data[iterator.id] = {
            name: iterator.name,
            tags: iterator.tags
        }
    }


    for (const iterator of ReportData) {
    
        let keyGen = '';
        let newRow = { amount: 1 };
        for (const key of groupBy) {
            switch (key) {
                case 'ADS':
                
                    keyGen = keyGen + '_ADS_' + iterator.ad_id;
                    newRow = { ...newRow, ... { ad_name: AD_data[iterator.ad_id].name } };
                    break;
                case 'NETWORK_NAME':
                    keyGen = keyGen + '_NETWORK_NAME_' + iterator.network_id;
                    newRow = { ...newRow, ... { network_name: iterator.network_name } };
                    break;
                case 'NETWORK_TYPE':
                    keyGen = keyGen + '_NETWORK_TYPE_' + iterator.network_type;
                    newRow = { ...newRow, ... { network_type: iterator.network_type } };
                    break;
                case 'PROGRAM_GENRE':
                    keyGen = keyGen + '_PROGRAM_GENRE_' + iterator.program_genre_id;
                    newRow = { ...newRow, ... { program_genre_name: iterator.program_genre_name } };
                    break;
                case 'PROGRAM_TITLE':
                    keyGen = keyGen + '_PROGRAM_TITLE_' + iterator.program_id;
                    newRow = { ...newRow, ... { program_name: iterator.program_name } };
                    break;
                default:
                    break;
            }
        }
        let tagMatched = parseInt(parseFloat(iterator.resonance_score) * parseFloat(AD_data[iterator.ad_id].tags) / 100);

        newRow = {
            ...newRow, ...dayPartObject, ... {
                tags: parseInt(AD_data[iterator.ad_id].tags),
                tags_matched: tagMatched,
                resonance_score: iterator.resonance_score
            }
        };
        let index = GroupedData.findIndex(aux => aux.keyGen == keyGen);
        if (index === -1) {
            GroupedData.push({ keyGen: keyGen, ...newRow });
            index = GroupedData.findIndex(aux => aux.keyGen == keyGen);
        } else {
            GroupedData[index].tags = GroupedData[index].tags + parseInt(iterator.ad_tags);
            GroupedData[index].tags_matched = GroupedData[index].tags_matched + tagMatched;
            GroupedData[index].resonance_score = parseFloat(GroupedData[index].resonance_score) + parseFloat(iterator.resonance_score);
            GroupedData[index].amount += 1;
        }
        let daypartIterator = JSON.parse(iterator.dayparts);
        for (const key of Object.keys(daypartIterator)) {
            if (daypartIterator[key]) {
                let daypartIndex = daypartsNames.findIndex(aux => aux.id == key);
                if (daypartsNames[daypartIndex])
                    GroupedData[index][daypartsNames[daypartIndex].name] = true;
            }
        }
    }

    for (const iterator of GroupedData) {
        let html = `<tr>`;
        for (const key of groupBy) {
            switch (key) {
                case 'ADS':
                    html = html + `<td>` + iterator.ad_name + `</td>`;
                    break;
                case 'NETWORK_NAME':
                    html = html + `<td>` + iterator.network_name + `</td>`;
                    break;
                case 'NETWORK_TYPE':
                    html = html + `<td>` + iterator.network_type + `</td>`;
                    break;
                case 'PROGRAM_GENRE':
                    html = html + `<td>` + iterator.program_genre_name + `</td>`;
                    break;
                case 'PROGRAM_TITLE':
                    html = html + `<td>` + iterator.program_name + `</td>`;
                    break;
                default:
                    break;
            }
        }


        for (const daypart of dayPartLabels) {
            if (iterator[daypart]) {
                html = html + `<td><div class="checked-daypart"></div></td>`;
            } else {
                html = html + `<td></td>`;
            }
        }


        html = html + `<td>` + parseInt(iterator.tags / iterator.amount) + `</td>`;

        html = html + `<td>` + parseInt(iterator.tags_matched / iterator.amount) + `</td>`;

        html = html + `<td>` + roundTwoDecimal(iterator.resonance_score / iterator.amount) + ` %</td></tr>`;

        $('#reportTableBody').append(html);
    }
    $.fn.dataTable.ext.errMode = 'none';



    reporTable = $('#reportTable').DataTable({
        paging: true,
        scrollX: true,
        lengthChange: true,
        searching: true,
        ordering: true
    }
    );
    $('#groupbyContainer').show();

}

const updateFilters = async () => {
    let campaingSelected = $('#campaignSelector').val();
    $('#adsCardContainer').empty();

    
    if (campaingSelected == 0) {
        initView();
        campaignData = null;
        daypartsNames = [];
    } else {
        await $.get("/api/rsn_get_campaign/" + campaingSelected, function (data) {
            campaingSelected = { ...data };
            campaignData = { ...data };
        });
        $('#filters-container').slideUp();



        let networkOptions = [];
        let networkTypeOptions = [];
        let programOptions = [];
        let programGenreOptions = [];
        let adOptions = [];
        adsIdLoaded = [];
        //fill program_genre selector


        let ad =  campaingSelected.rsn_ads[0];
       

        for (const ad of campaingSelected.rsn_ads) {
            adsIdLoaded.push(ad.id);
            //fill ads selector
            adOptions.push({ id: ad.id, value: ad.name })
        }

        //fill networks and network_types selector
        let aux = {};
        let auxtype = {};

        for (const network of ad.rsn_networks) {
            if(aux[network.id] == undefined){
                networkOptions.push({ id: network.id, value: network.name })
                aux[network.id]=1;
            }
            if(auxtype[network.type] == undefined){
                networkTypeOptions.push({ id: network.type, value: network.type })
                auxtype[network.type]=1;
            }
        }
        //fill program_genre selector
        aux = {};
        for (const program_genre of ad.rsn_program_genres) {
            if(aux[program_genre.id] == undefined){
                programGenreOptions.push({ id: program_genre.id, value: program_genre.name })
                aux[program_genre.id]=1;
            }
        }
        //fill program selector
        aux = {};
        for (const program of ad.rsn_programs) {
            if(aux[program.id] == undefined){
                programOptions.push({ id: program.id, value: program.name })
                aux[program.id]=1;
            }
        }
        
        FillSelector('#filterAd', adOptions, true);
        FillSelector('#filterNetwork', networkOptions, true);
        FillSelector('#filterNetworkType', networkTypeOptions, true);
        FillSelector('#filterProgramGenre', programGenreOptions, true);
        FillSelector('#filterProgram', programOptions, true);

        $('#filters-container').slideDown(500);

        $('#filters-container-loading').hide();


    }
}

const loadAds = () =>{
    for (const ad of campaignData.rsn_ads) {
        newAd(ad);
    }
}

const newAd = (ad) => {

    let toggleID = 'ad-' + ad.id;
    let chartID_needstate = 'ad-chart-needstate-' + ad.id;
    let chartID_motivation = 'ad-chart-motivation-' + ad.id;
    
    let chartContainerID = 'ad-chart-container-' + ad.id;

    let html = `<div class="card " id="adCardContainer-` + ad.id + `" style="">
                    <div class="card-body">
                        <p><b>`+ ad.name + `</b></p>
                        <hr>
                        <div class="col-md-3 form-group text-center vertival-center " >
                        <div class="preview-container" style="border-radius: 10px 10px 10px 10px;
                        -moz-border-radius: 10px 10px 10px 10px;
                        -webkit-border-radius: 10px 10px 10px 10px;
                        border: 2px solid #cbcbcb; margin-bottom: 1em;">
                        `+ ad.tag_preview + `
                        </div>
                        <input  data-on="Motivations" data-off="Needstates" type="checkbox" data-ad-togle-id="`+ ad.id + `"
                                checked data-toggle="toggle" class="adstoggle" id="`+ toggleID + `">
                        </div>
                        <div class="col-md-9" style="overflow-x: scroll;">
                            <div id="`+ chartContainerID + `" style = "position: relative;
                            margin: auto;
                            height: 19em;
                            overflow-y: hidden;
                            width: 150em;
                            ">
                            </div>
                        </div>
                    </div>
                </div>`;

    $('#adsCardContainer').append(html);


    let labels_needstate = [];
    let datasets_needstate = [];
    let labels_motivation = [];
    let datasets_motivation = [];

    let colorA = randomizeInteger(50, 171);
    let colorB = randomizeInteger(105, 206);
    let colorC = randomizeInteger(180, 229);
    let backgroundColor = "rgba(" + colorA + ", " + colorB + ", " + colorC + ",0.4)";


    datasets_needstate.push({
        label: ad.name,
        data: [],
        backgroundColor: backgroundColor,
        scaleOverride: true,
        scaleSteps: 9,
        scaleStartValue: 0,
        scaleStepWidth: 100
    });
    datasets_motivation.push({
        label: ad.name,
        data: [],
        backgroundColor: backgroundColor,
        scaleOverride: true,
        scaleSteps: 9,
        scaleStartValue: 0,
        scaleStepWidth: 100
    });


    for (let iterator of ad.rsn_needstates) {
        if (parseFloat(iterator.pivot.value) > 0) {
            if(iterator.type == 'needstate'){
                if (labels_needstate.indexOf(iterator.name) === -1) {
                    labels_needstate.push(iterator.name.substr(0, 25));
                }
                datasets_needstate[0].data.push(iterator.pivot.value);
            }else{
                if (labels_motivation.indexOf(iterator.name) === -1) {
                    labels_motivation.push(iterator.name.substr(0, 25));
                }
                datasets_motivation[0].data.push(iterator.pivot.value);
            }
        }
    }


    $('#' + chartContainerID).append('<canvas id="' + chartID_needstate + '" height="480vw" width="880vw" class="'+toggleID+'-needstate  chartjs-render-monitor" ></canvas>');
    var ctx_needstate = document.getElementById(chartID_needstate).getContext('2d');

    $('#' + chartContainerID).append('<canvas id="' + chartID_motivation + '" height="480vw" width="880vw" class="'+toggleID+'-motivation chartjs-render-monitor" ></canvas>');
    var ctx_motivation = document.getElementById(chartID_motivation).getContext('2d');

    let options = {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            yAxes: [{
                ticks: {
                    min: 0,
                    max: 100,
                    stepSize: 20,
                    callback: function (value) {
                        return roundTwoDecimal(value) + "%"
                    }
                },
                scaleLabel: {
                    display: true,
                    labelString: "Percentage"
                }
            }],
            xAxes: [
                {
                    ticks: {
                        autoSkip: false
                    }
                }
            ]
        }
    };

    let myChart = new Chart(ctx_needstate, {
        type: 'bar',
        data: {
            labels: labels_needstate,
            datasets: datasets_needstate,

        },
        options: options,
    });

    let myChart_motivation = new Chart(ctx_motivation, {
        type: 'bar',
        data: {
            labels: labels_motivation,
            datasets: datasets_motivation,

        },
        options: options,
    });

 


    $('#' + toggleID).bootstrapToggle();
    $('.'+toggleID+'-needstate').hide(500);
    $('#' + toggleID).change(async function () {
        toggleChart(toggleID, $(this).prop("checked"))
    });
}

const toggleChart = (id, isNeedstate) => {
    if (isNeedstate){
        $('.'+id+'-needstate').fadeOut(500);
        $('.'+id+'-motivation').fadeIn(500);
    }else{
        $('.'+id+'-needstate').fadeIn(500);
        $('.'+id+'-motivation').fadeOut(500);
    }
}


const initView = () => {
    $('#filters-container-loading').hide();
    $('#reportTableContainer-loading').hide();
    $('#topRalatedResonancesContainer-loading').hide();
    $('#topRalatedResonancesContainer').hide();
    $('#groupbyContainer').hide();
    $('#chartByDaypartContainer').hide();
    $('#filters-container').slideUp();
    $('#resonancePercentage').text('0 %')
    initMultiSelect('#filterGroupBy');
    $('#filterGroupBy').change(function () {
        UpdateReportTable(ReportData);
    });
}



const toggleChartBy = (type) => {
    if(type == 'DAYPARTS'){
        $('#verticalChartContainer').fadeOut();
        $('#chartByDaypartContainer').show();
        updateHorizontalChart();
    }else{
        $('#verticalChartContainer').show();
        $('#chartByDaypartContainer').fadeOut();
        $('#chartByDaypartContainer').empty();
    }
}

const getNetData = async (chartBy) => {
    let netFormatedData = [];

    let networks_ids = $('#filterNetwork').val();
    let ads_ids = $('#filterAd').val();
    let network_types_ids = $('#filterNetworkType').val();
  
    let dataNets = [];

    await $.post("/api/rsn_get_top_networks/", {
        ads_ids: JSON.stringify(ads_ids),
        networks_ids: JSON.stringify(networks_ids),
        network_types_ids: JSON.stringify(network_types_ids),
        type: chartBy
    },
    function (data) {
        dataNets = [... dataNets, ... data];
    }, 'json');

    if (chartBy == 'NETWORK_NAME' ){
        for (const iterator of dataNets) {
            let index = campaignData.rsn_ads.findIndex(aux => aux.id == iterator.ad_id);
            let auxAd = campaignData.rsn_ads[index];
    
            let net = {
                amount:1,
                ad_name: auxAd.name,
                resonance_score: iterator.net_average,
                NETWORK_NAME: iterator.rsn_networks.name,
                ad_id: iterator.ad_id
            };
            netFormatedData.push(net);
        }
    } else {
        for (const iterator of dataNets) {
            let index = campaignData.rsn_ads.findIndex(aux => aux.id == iterator.ad_id);
            let auxAd = campaignData.rsn_ads[index];
    
            let net = {
                amount:1,
                ad_name: auxAd.name,
                resonance_score: iterator.net_average,
                NETWORK_TYPE: iterator.network_type,
                ad_id: iterator.ad_id
            };
            netFormatedData.push(net);
        }
    }
    
    return netFormatedData;
}

const getProgramData = async (chartBy) => {
    let netFormatedData = [];

    let ads_ids = $('#filterAd').val();
    let programs_ids = $('#filterProgram').val();
    let program_genres_ids = $('#filterProgramGenre').val();

    let dataPrograms = [];

    await $.post("/api/rsn_get_top_programs/", {
        ads_ids: JSON.stringify(ads_ids),
        programs_ids: JSON.stringify(programs_ids),
        program_genres_ids: JSON.stringify(program_genres_ids),
        type: chartBy,
        limit:100
    },
    function (data) {
        dataPrograms = [... dataPrograms, ... data.programs];
    }, 'json');

    if (chartBy == 'PROGRAM_GENRE' ){
        for (const iterator of dataPrograms) {
            let index = campaignData.rsn_ads.findIndex(aux => aux.id == iterator.ad_id);
            let auxAd = campaignData.rsn_ads[index];
    
            let net = {
                amount:1,
                ad_name: auxAd.name,
                resonance_score: iterator.net_average,
                PROGRAM_GENRE: iterator.rsn_program_genres.name,
                ad_id: iterator.ad_id
            };
            netFormatedData.push(net);
        }
    } else {
        for (const iterator of dataPrograms) {
            let index = campaignData.rsn_ads.findIndex(aux => aux.id == iterator.ad_id);
            let auxAd = campaignData.rsn_ads[index];
    
            let net = {
                amount:1,
                ad_name: auxAd.name,
                resonance_score: iterator.net_average,
                PROGRAM_TITLE: iterator.rsn_programs.name,
                ad_id: iterator.ad_id
            };
            netFormatedData.push(net);
        }

    }

    
    return netFormatedData;
}

const getAdData = async (chartBy) => {
    let adData = [];

    let networks_ids = $('#filterNetwork').val();
    let ads_ids = $('#filterAd').val();
    let network_types_ids = $('#filterNetworkType').val();
    let programs_ids = $('#filterProgram').val();
    let program_genres_ids = $('#filterProgramGenre').val();

    let dataAd = [];

    await $.post("/api/rsn_get_top_ad/", {
        ads_ids: JSON.stringify(ads_ids),
        networks_ids: JSON.stringify(networks_ids),
        network_types_ids: JSON.stringify(network_types_ids),
        programs_ids: JSON.stringify(programs_ids),
        program_genres_ids: JSON.stringify(program_genres_ids)
    },
    function (data) {
        dataAd = [... dataAd, ... data];
    }, 'json');


    for (const iterator of dataAd) {
        let index = campaignData.rsn_ads.findIndex(aux => aux.id == iterator.ad_id);
        let auxAd = campaignData.rsn_ads[index];

        let ad = {
            amount:1,
            ad_name: auxAd.name,
            resonance_score: iterator.net_average,
            ADS: auxAd.name,
            ad_id: iterator.ad_id
        };
        adData.push(ad);
    }
    return adData;
}


const updateVerticalChart = async (filterChanged = null) => {

 

    let chartBy = $('#ChartBy').val();

    toggleChartBy(chartBy);

    if(chartBy == 'DAYPARTS'){
        return true;
    }


    let data;
    let em = 71; 
    if(chartBy == 'NETWORK_NAME' || chartBy == 'NETWORK_TYPE'){
        data = await getNetData(chartBy);
    } else if(chartBy == 'PROGRAM_GENRE' || chartBy == 'PROGRAM_TITLE'){
        data = await getProgramData(chartBy);
    } else {
        data = await getAdData();
    }

    if (chartBy == 'NETWORK_NAME' || chartBy == 'PROGRAM_GENRE' || chartBy == 'PROGRAM_TITLE') {
        em = data.length * 1.8; 
        if (em > 4500) {em = 4500 };
    } 

    $('#verticalChartContainer').css('width', em+'em');

    let labels = [];
    let datasets = [];



    for (let iterator of data) {
        if (labels.indexOf(iterator[chartBy]) === -1) {
            labels.push(iterator[chartBy]);
        }
        
        let index = datasets.findIndex(aux => aux.label == iterator.ad_name);
        if (index === -1) {
            let colorA = randomizeInteger(50, 171);
            let colorB = randomizeInteger(105, 206);
            let colorC = randomizeInteger(180, 229);
            let backgroundColor = "rgba(" + colorA + ", " + colorB + ", " + colorC + ",0.4)";
            datasets.push({
                label: iterator.ad_name,
                data: [roundTwoDecimal(iterator.resonance_score / iterator.amount)],
                backgroundColor: backgroundColor,
            });
        } else {
            let dataCount = datasets[index].data.length;
            for (let x = dataCount; x < labels.length; x++) {
                if (iterator[chartBy] == labels[x]) {
                    datasets[index].data.push(roundTwoDecimal(iterator.resonance_score / iterator.amount));
                    break;
                } else {
                    datasets[index].data.push(0);
                }
            }
        }
    }
    if (chartBy == 'ADS') {
        labels = ['Ads Resonance']
    }
    $('#chartjs_barChart').detach();
    $('#verticalChartContainer').append('<canvas id="chartjs_barChart"  class="chartjs-render-monitor" ></canvas>');
    var ctx = document.getElementById("chartjs_barChart").getContext('2d');


    let options = {
        maintainAspectRatio: false,
        legend: {
            position: "left",
        },
        scales: {
            yAxes: [{
                ticks: {
                    callback: function (value) {
                        return roundTwoDecimal(value) + "%"
                    },
                    beginAtZero: true
                },
                scaleLabel: {
                    display: true,
                    labelString: ""
                }
            }],
            xAxes: [
                {
                    ticks: {
                        autoSkip: false
                    }
                }
            ]
        }
    };


    let myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: options,
    });



}


const updateHorizontalChart = async () => {
    let chartData = daypartsData;
 

    let labels = Object.keys(chartData.resumedkeys);
    let datasets = [];

 
    for (let iterator of Object.keys(chartData.resumed)) {

        let colorA = randomizeInteger(50, 171);
        let colorB = randomizeInteger(105, 206);
        let colorC = randomizeInteger(180, 229);

        let backgroundColor = "rgba(" + colorA + ", " + colorB + ", " + colorC + ",0.4)";
        let borderColor = "rgba(" + colorA + ", " + colorB + ", " + colorC + ",0.7)";


        let adDaypart = chartData.resumed[iterator];
        datasets.push({
            label: adDaypart.ad_name,
            data: Object.values(adDaypart.resonances),
            backgroundColor: backgroundColor,
            borderColor: borderColor,
            borderWidth: .6
        });
    }

    $('#chartist_horizontalBar').detach();
    $('#chartByDaypartContainer').append('<canvas id="chartist_horizontalBar"  class="chartjs-render-monitor" ></canvas>');

    var ctx = document.getElementById('chartist_horizontalBar').getContext('2d');

    let options = {
        maintainAspectRatio: false,
        legend: {
            position: "left",
        },
        scales: {
            yAxes: [{
                ticks: {
                    callback: function (value) {
                        return roundTwoDecimal(value) + "%"
                    }
                },
                scaleLabel: {
                    display: true,
                    labelString: ""
                }
            }],
            xAxes: [
                {
                    ticks: {
                        autoSkip: false
                    }
                }
            ]
        }
    };

    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets
        },
        options:options
    });
}


const addLift = () => {
$('#ad_lift').empty();
for (const iterator of campaignData.rsn_ads) {
    let data = JSON.parse(iterator.sales_lift);
    if(data){
        let table = `<h4>`+iterator.name+`<h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>PREDICTED SALES LIFT INCREASE</th>
                        <th>#</th>
                        <th>PREDICTED SALES LIFT INCREASE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">TOP 5 VS. BOTTOM 5</th>
                        <td>`+formatPercentage(data.inc_5)+`</td>
                        <th>TOP 5 VS. AVG.</th>
                        <td>`+formatPercentage(data.inc_avr_5)+`</td>
                    </tr>
                    <tr>
                        <th scope="row">TOP 10 VS. BOTTOM 10</th>
                        <td>`+formatPercentage(data.inc_10)+`</td>
                        <th>TOP 10 VS. AVG.</th>
                        <td>`+formatPercentage(data.inc_avr_10)+`</td>
                    </tr>
                    <tr>
                        <th scope="row">TOP 20 VS. BOTTOM 20</th>
                        <td>`+formatPercentage(data.inc_20)+`</td>
                        <th>TOP 20 VS. AVG.</th>
                        <td>`+formatPercentage(data.inc_avr_20)+`</td>
                    </tr>
                </tbody>
            </table>`;

            $('#ad_lift').append(table); 
    }

}


       
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
const FillSelector_simple = (selector, options, selectAll) => {
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