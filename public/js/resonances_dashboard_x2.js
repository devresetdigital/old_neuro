let colors = [
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
    '#7f19e673',
    '#FF000073',
    '#FF800073',
    '#FFFF0073',
    '#80FF0073',
    '#00FF0073',
    '#00FF8073',
 ];
 $('#reportlabel').hide();
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
        // loadFilters();
        // loadTops();
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
let campaign = [];
let assets =[];

let filters = {
    signals: [],
    need_states: [],
    soma_semantics: [],
    mythic_narratives: []
};

let tops = [];

const getData = async () => {
    $('#signalPreview').empty();

    let campaign = $('#campaignSelector').val();

    if (campaign != '') {
        await $.get("/api/get_signals_by_campaign/" + campaign, function (data) {
            signals = [ ...data ];
        });
        await $.get("/api/get_neuro_campaign/" + campaign, function (data) {
            campaign =  data ;
            assets = JSON.parse(data.assets);

            if(data.assets.length){
                let preview = JSON.parse(data.assets);

                if(preview[0] != undefined){
                    let preview_url = '/storage/'+preview[0].download_link;
                    let html='';
                    if (
                        preview_url.indexOf('.mp4')>= 0 ||
                        preview_url.indexOf('.webm')>= 0 ||
                        preview_url.indexOf('.ogg')>= 0 ||
                        preview_url.indexOf('.avi')>= 0 ||
                        preview_url.indexOf('.mov')>= 0 
                    ){
                        html = `
                            <h4 style="border-bottom: 3px solid #ccc;">Preview: </h4>
                            <div class="">
                                <video style="width:50%; height:auto" controls>
                                    <source src="${preview_url}" type="video/mp4">
                                    <source src="${preview_url}" type='video/webm'>
                                    <source src="${preview_url}" type='video/ogg'>
                                    <source src="${preview_url}" type='video/avi'>
                                    <source src="${preview_url}" type='video/mov'>
                                    Your browser does not support the video tag. <!-- Text to be shown incase browser doesnt support html5 -->
                                </video>
                            </div>
                        `;
                    }else if (
                        preview_url.indexOf('.jpeg')>= 0 ||
                        preview_url.indexOf('.jpg')>= 0 ||
                        preview_url.indexOf('.png')>= 0 ||
                        preview_url.indexOf('.svg')>= 0
                    ){
                        html = `
                        <h4 style="border-bottom: 3px solid #ccc;">Preview: </h4>
                        <div style="
                        background-image : url(${preview_url});
                        width:auto; height:400px; background-repeat: no-repeat; background-size: contain;
                        "></div>
                    `;
                    }else{
                        html = `
                        <h4 style="border-bottom: 3px solid #ccc;">Preview: </h4>
                        <a  href="${preview_url}" download> <div class="icon voyager-file-text"></div>Download Files</a>
                    `;
                    }
                    $('#signalPreview').append(html);
                }
            }
        });

    }

}

const loadSignals = () => {

    $('#signalsCardContainer').empty();

    let datasetRadar = [];
    let entriesLabels = [];

    let keyToShow = [];
    for (const  [index, iterator]  of signals.entries()) {
        let data = JSON.parse(iterator.data)
        delete data.name;
        let totalScore = 0;
        let count=0;
        for (let [name,value] of Object.entries(data)) {
            if (parseFloat(value) > 0){
                totalScore += parseFloat(value);
                count++;
            }
        }
        for (let [name,value] of Object.entries(data)) {
            //if (parseFloat(value) >= (totalScore/count)){
            if (parseFloat(value) > 0){
                keyToShow.push(name);
            }
        }

    }
    console.log(signals);
    for (const  [index, iterator]  of signals.entries()) {
  
        let data = JSON.parse(iterator.data);

        delete data.name;

        let series = [];
        let labels= [];
        let totalScore = 0;
        let count=0;
        for (let [name,value] of Object.entries(data)) {
            if (value !=0){
                totalScore += parseFloat(value);
                count++;
            }
        }
        for (let [name,value] of Object.entries(data)) {
            if (keyToShow.includes(name)){
                labels.push(name);
                series.push((parseFloat(value)*100/totalScore).toFixed(2));
                //series.push((parseFloat(value)*100/totalScore).toFixed(2));
            }
        }

        datasetRadar.push({
            label: iterator.name,
            backgroundColor: colors[index],// "rgba(200,0,0,0.2)",
            data: series
        });
        entriesLabels=labels;

    }


    $('#reportlabel').show();
    console.log(entriesLabels);
    if(entriesLabels.length>0){

        let html = `<canvas id="Chart_radar" width="600" height="400"></canvas>`;
    
        $('#signalsCardContainer').append(html);

        var marksCanvas =  document.getElementById("Chart_radar");

        var marksData = {
            labels: entriesLabels,
            datasets: datasetRadar
        };
    
        var options = {
            scale: {
                ticks: {
                    beginAtZero: true,
                    min: 0
                }
            },
            scales: {
                r: {
                    pointLabels: {
                        font: {
                            size: 18
                        }
                    }
                }
            }
        };
    
        new Chart(marksCanvas, {
            type: 'radar',
            data: marksData,
            options: options
        });

    }else{
        let html = `<h3 style="text-align:center; margin:2em">The report is under review, it will be displayed soon</h3>`;
        $('#signalsCardContainer').append(html);
    }

   


   

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



function getRandomArbitrary(min, max) {
    return parseInt(Math.random() * (max - min) + min);
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
        await $.get("/api/advertisers?organization=" + org, function (data) {
            advertisers = { ...data };
        });
    }


    let options = []
    for (const iterator in advertisers) {
        options.push({
            value: advertisers[iterator].name,
            id: advertisers[iterator].id
        });
    }

    FillSelector_simple('#advertiser', options, false);

}

const updateCampaignsSelector = async () => {

    let advertiser = $('#advertiser').val();

    let campaignData = [];

    if (advertiser != '') {
        await $.get("/api/rsn_get_signal_campaigns_by_advertiser?advertiser=" + advertiser + "&type=x2", function (data) {
            campaignData = { ...data };
        });
    }

    let options = []


    for (const iterator in campaignData) {
        options.push({
            value: campaignData[iterator].name,
            id: campaignData[iterator].id
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
    $(selector).append(new Option('None', ''));
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
    if (typeof number == 'number') {
        return roundTwoDecimal(number) + " %";
    }
    return number;
}

const roundTwoDecimal = (number) => {
    return Math.round((number + Number.EPSILON) * 100) / 100;
}
