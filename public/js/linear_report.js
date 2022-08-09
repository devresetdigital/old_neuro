$(document).ready(function () {
    initView();

    $('#search').click(function(e){
        filterData();
    });

    $('#tab1').click(function(e){
        activeTab(1);
    });
    $('#tab2').click(function(e){
        activeTab(2);
    });
    $('#tab3').click(function(e){
        activeTab(3);
    });
    $('#tab4').click(function(e){
        activeTab(4);
    });
    $('#tab5').click(function(e){
        activeTab(5);
    });


  
});
$("#filtersContainer").hide();
let startdate =  get_vars.from.substr(0,2) + get_vars.from.substr(2,2) +get_vars.from.substr(4,2);
let enddate =  get_vars.until.substr(0,2) + get_vars.until.substr(2,2) +get_vars.until.substr(4,2);

const initView = async () => {
    activeTab(currentTab);

    let networks = await fillTable(1,'body-network');
    initPieChart(networks,'netChart');

    let week = await fillTable(2,'body-weekly');
    initLineChart(week,'weekChart');

    let msu = await fillTable(3,'body-msu');
    initBarChart(msu,'msuChart');

    fillTableSummary(4,'body-dma');
    fillTableReport(5,'body-dma-report');

    $('#filters').click(function () {
        $("#filtersContainer").fadeToggle("slow");
    });


    switch (parseInt(currentTab)) {
        case 1:
            $( "#tab1" ).trigger( "click" )
            break;
        case 2:
            $( "#tab2" ).trigger( "click" )
            break;
        case 3:
            $( "#tab3" ).trigger( "click" )
            break;
        case 4:
            $( "#tab4" ).trigger( "click" )
            break;
        case 5:
            $( "#tab5" ).trigger( "click" )
            break;
        default:
            break;
    }

    
}
const initLineChart = (networks,id) => {
  
    let percentage  = [];
    let percentage_avg  = [];
    let total1 = 0;
    let total2 = 0;
    for (const iterator of Object.keys(networks)) {
        total1 += networks[iterator][0];
        total2 += networks[iterator][1];
        percentage.push( parseInt(networks[iterator][2] * 100));
    }

    for(var i = 0; i < percentage.length; i++){
        percentage_avg.push(parseInt(total2/total1*100));
    }

    var canvas = document.getElementById(id);

    const labels = Object.keys(networks);

      const data = {
        labels: labels,
        datasets: [{
          label: 'Delivery %',
          backgroundColor: 'rgb(47, 191, 160)',
          borderColor: 'rgb(47, 191, 160)',
          data: percentage,
        },
        {
            label: 'Average Percentage %',
            backgroundColor: 'rgb(116, 104, 189)',
            borderColor: 'rgb(116, 104, 189)',
            data: percentage_avg,
        }
        
        ]
      };


      const config = {
        type: 'line',
        data: data,
        options: {
            scales: {
                y: {
                  beginAtZero: true,
                }
            }
        }
      };

      var myChart = new Chart(
        canvas,
        config
      );
      
}
const initBarChart = (networks,id) => {

    let percentage  = [];

    let total1 = 0;
    let total2 = 0;
    for (const iterator of Object.keys(networks)) {
        total1 += networks[iterator][0];
        total2 += networks[iterator][1];
        percentage.push( parseInt(networks[iterator][2] * 100));
    }

 
    const labels = Object.keys(networks);

      const data = {
        labels: labels,
        datasets: [{
          label: 'Delivery %',
          backgroundColor: 'rgb(47, 191, 160)',
          borderColor: 'rgb(47, 191, 160)',
          data: percentage,
        }
        
        ]
      };
      const config = {
        type: 'bar',
        data: data,
        options: {}
      };

      var myChart = new Chart(
        document.getElementById(id),
        config
      );
}


const initPieChart = (networks,id) => {

    let percentage  = [];
    let total1 = 0;
    let total2 = 0;
    for (const iterator of Object.keys(networks)) {
        total1 += networks[iterator][0];
        total2 += networks[iterator][1];
        percentage.push( parseInt(networks[iterator][2] * 100));
    }

    const labels = Object.keys(networks);

      const data = {
        labels: labels,
        datasets: [{
          label: 'Delivery %',
          backgroundColor: Object.values(CHART_COLORS),
          data: percentage,
        }
        
        ]
      };
      const config = {
        type: 'pie',
        data: data,
        options: {}
      };

      var myChart = new Chart(
        document.getElementById(id),
        config
      );
}

const CHART_COLORS = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(75, 192, 192)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(201, 203, 207)'
  };

const activeTab = (number)=>{

    currentTab = number;
    $('#container-networks').hide();
    $('#container-dayparts').hide();
    $('#container-dmas').hide();
    $('#container-creative').hide();
    $('#container-advertisers').hide();
    $('#container-demos').hide();
    $('#container-programs').hide();
    $('#container-search').hide();

    switch (number) {
        case 1:
            $('#container-networks').show();
            $('#container-search').show();
            break;
        case 2:

            break;
        case 3:
            $('#container-dayparts').show();
            $('#container-search').show();
            break;
        case 4:
            $('#container-dmas').show();
            $('#container-search').show();
            break;        
        case 5:
            $('#container-networks').show();
            $('#container-dayparts').show();
            $('#container-creative').show();
            $('#container-dmas').show();
            $('#container-advertisers').show();
            $('#container-demos').show();
            $('#container-programs').show();
            $('#container-search').show();
            break;
        default:
            break;
    }
}



const filterData = () => {
    let networks ='';
    let dayparts = '';
    let dmas = '';
    let creative = '';
    let advertisers = '';
    let demos = '';
    let programs = '';


    switch (currentTab) {
        case 1:
            networks = $('#networks').val().join();
            break;
        case 2:
            break;
        case 3:
            dayparts = $('#dayparts').val().join();
            break;
        case 4:
            dmas = $('#dmas').val().join();
            break;        
        case 5:
            networks = $('#networks').val().join();
            dayparts = $('#dayparts').val().join();
            dmas = $('#dmas').val().join();
            creative = $('#creative').val().join();
            advertisers = $('#advertisers').val().join();
            demos = $('#demos').val().join();
            programs = $('#programs').val().join();

    }

    let url='/linear_report?from='+startdate+'&until='+enddate+'&campaign_id='+campaign_id_query+'&network='+networks+'&daypart='+dayparts
    + '&dma=' + dmas + '&creative=' + creative +'&advertiser=' + advertisers + '&demo=' + demos + '&program=' + programs + '&tab=' + currentTab ;
    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
    window.location.href = baseUrl+url;
}


const getUrlVars = () => 
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

const fillTableSummary = async (type, bodyId) => {
  
    let dma = $('#dmas').val().join();

    let filtersID = 'dmas';

    $.post("/api/linear/get_table/", {
        campaign_id: campaign_id,
        startdate: startdate,
        enddate: enddate,
        type: type,
        dma: dma
    },
    function (data) {
        let html = '';
        let total1 = 0;
        let total2 = 0;

  
        for (const iterator of Object.keys(data)) {

            total1 += parseInt(data[iterator][2]);
            total2 += parseInt(data[iterator][3]);
            html += ` 
            <tr>
                <td>${iterator}</td>
                <td class="filter-clik" data-filterid="${filtersID}" data-filter="${data[iterator][0]}">${data[iterator][0]}</td>
                <td style="text-align: right;" >${numberWithCommas(data[iterator][1])}</td>
                <td style="text-align: right;" >${numberWithCommas(data[iterator][2]) } %</td>
             </tr>
            `;
        }

        html+=` 
        <tr>
            <td style="font-weight: bold">Total</td>
            <td style="font-weight: bold ;text-align: right; "></td>
            <td style="font-weight: bold ;text-align: right; ">${numberWithCommas(total1)}</td>
            <td style="font-weight: bold ;text-align: right; ">${numberWithCommas(total2)} %</td>
        </tr>`;


        $('#'+bodyId).empty();
        $('#'+bodyId).append(html);

    }, 'json');

    $('.filter-clik').click(function(e){
        fillFiler($(this).data('filterid'),$(this).data('filter')); 
    });
 
}

const fillTableReport = async (type, bodyId) => {
    let network = $('#networks').val().join();
    let daypart = $('#dayparts').val().join();
    let creative = $('#creative').val().join();
    let advertiser = $('#advertisers').val().join();
    let demo = $('#demos').val().join();
    let program = $('#programs').val().join();

    $.post("/api/linear/get_table/", {
        campaign_id: campaign_id,
        startdate: startdate,
        enddate: enddate,
        type: type,
        network: network,
        daypart: daypart,
        creative: creative,
        advertiser: advertiser,
        demo: demo,
        program: program
    },
    function (data) {
        let html = '';
  
        for (const iterator of Object.keys(data)) {
            let keys = iterator.split(",");
            html += ` 
            <tr>
                <td class="filter-clik" data-filterid="creative" data-filter="${keys[0]}">${keys[0]}</td>
                <td class="filter-clik" data-filterid="advertisers" data-filter="${keys[1]}">${keys[1]}</td>
                <td>${keys[2]}</td>
                <td>${keys[3]}</td>
                <td class="filter-clik" data-filterid="networks" data-filter="${keys[4]}">${keys[4]}</td>
                <td>${keys[5]}</td>
                <td>${keys[6]}</td>
                <td class="filter-clik" data-filterid="dmas" data-filter="${keys[7]}">${keys[7]}</td>
                <td class="filter-clik" data-filterid="demos" data-filter="${keys[8]}">${keys[8]}</td>
                <td class="filter-clik" data-filterid="programs" data-filter="${keys[9]}">${keys[9]}</td>
                <td class="filter-clik" data-filterid="dayparts" data-filter="${keys[10]}">${keys[10]}</td>
                <td style="text-align: right;">${data[iterator][0]}</td>
                <td style="text-align: right;" >$${roundTwoDecimal(data[iterator][1])}</td>
                <td style="text-align: right;">$${roundTwoDecimal(data[iterator][2])}</td>
            </tr>
            `;
        }

        $('#'+bodyId).empty();
        $('#'+bodyId).append(html);

    }, 'json');

    setTimeout(() => {
        $('.filter-clik').click(function(e){
            fillFiler($(this).data('filterid'),$(this).data('filter')); 
        });
    }, 500);
 
}


const fillTable = async (type, bodyId) => {
    let networks = $('#networks').val().join();
    let dayparts = $('#dayparts').val().join();
    let filtersID = '';

    switch (type) {
        case 1:
            filtersID = 'networks';
            break;
        case 3:
            filtersID = 'dayparts';
            break;
    
        default:
            break;
    }

    let results = await $.post("/api/linear/get_table/", {
        campaign_id: campaign_id,
        startdate: startdate,
        enddate: enddate,
        type: type,
        network: networks,
        daypart: dayparts
    },
    function (data) {
        let html = '';
        let total1 = 0;
        let total2 = 0;

        for (const iterator of Object.keys(data)) {

            total1 += data[iterator][0];
            total2 += data[iterator][1];
            html += ` 
            <tr>
                <td class="filter-clik" data-filterid="${filtersID}" data-filter="${iterator}">${iterator}</td>
                <td style="text-align: right;" >${numberWithCommas(data[iterator][0])}</td>
                <td style="text-align: right;" >${ numberWithCommas(data[iterator][1])}</td>
                <td style="text-align: right;" >${ parseInt(parseFloat(data[iterator][2])*100) } %</td>
             </tr>
            `;
        }

        if(total1 != 0){
        html+=` 
        <tr>
            <td style="font-weight: bold">Total</td>
            <td style="font-weight: bold ;text-align: right; ">${numberWithCommas(total1)}</td>
            <td style="font-weight: bold ;text-align: right; ">${numberWithCommas(total2)}</td>
            <td style="font-weight: bold ;text-align: right; ">${ parseInt(total2/total1*100) } %</td>
        </tr>`;
        }

        $('#'+bodyId).empty();
        $('#'+bodyId).append(html);


        return data;

    }, 'json');

    $('.filter-clik').click(function(e){
        fillFiler($(this).data('filterid'),$(this).data('filter')); 
    });

   return results;
 
}


//AUXILIAR FUNCTIONS

const fillFiler = (id,value) => {
    let values = $('#'+id).val();
    values.pushUnique(value);
    $('#'+id).val(values);
    $('#'+id).trigger('change');
    $("#filtersContainer").fadeIn();
}

Array.prototype.pushUnique = function (item){
    if(this.indexOf(item) == -1) {
    //if(jQuery.inArray(item, this) == -1) {
        this.push(item);
        return true;
    }
    return false;
}


function numberWithCommas(x) {
    if(x == undefined){
        return x;
    }
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

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


const roundTwoDecimal = (number) => {
    return Math.round((number + Number.EPSILON) * 100) / 100;
}