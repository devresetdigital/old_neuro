let colors = [
  "#0080FF73",
  "#0000FF73",
  "#7F00FF73",
  "#e6191973",
  "#e6801973",
  "#e5e61973",
  "#80e61973",
  "#19e61973",
  "#1980e673",
  "#1980e673",
  "#1919e673",
  "#7f19e673",
  "#FF000073",
  "#FF800073",
  "#FFFF0073",
  "#80FF0073",
  "#00FF0073",
  "#00FF8073",
];
$("#reportlabel").hide();
$(document).ready(function () {
  $("#loading").hide();
  $.fn.dataTable.ext.errMode = "none";
  initView();
  $("#campaignSelector").change(async function () {
    $("#filters-container-loading").show();
    $("#filters-container").hide();
    $("#signalsCardContainer").empty();
    $("#topRalatedResonancesContainer-loading").show();
    $("#topRalatedResonancesContainer").hide();

    await getData();

    loadSignals();
    // loadFilters();
    // loadTops();
  });
  $("#filterNetwork").change(async function () {
    $("#topRalatedResonancesContainer-loading").show();
    $("#reportTableContainer-loading").show();
    $("#reportTableContainer").hide();
    $("#topRalatedResonancesContainer").hide();
  });

  $("#filterAd").change(async function () {
    $("#topRalatedResonancesContainer-loading").show();
    $("#reportTableContainer-loading").show();
    $("#reportTableContainer").hide();
    $("#topRalatedResonancesContainer").hide();

    updateSignalsContainer();
  });

  $("#filterNetworkType").change(async function () {
    $("#topRalatedResonancesContainer-loading").show();
    $("#reportTableContainer-loading").show();
    $("#reportTableContainer").hide();
    $("#topRalatedResonancesContainer").hide();
  });
  $("#filterProgram").change(async function () {
    $("#topRalatedResonancesContainer-loading").show();
    $("#reportTableContainer-loading").show();
    $("#reportTableContainer").hide();
    $("#topRalatedResonancesContainer").hide();
  });
  $("#filterProgramGenre").change(async function () {
    $("#topRalatedResonancesContainer-loading").show();
    $("#reportTableContainer-loading").show();
    $("#reportTableContainer").hide();
    $("#topRalatedResonancesContainer").hide();
  });

  $("#organization").change(async function () {
    await updateAdvertisers();
  });
  $("#advertiser").change(async function () {
    await updateCampaignsSelector();
  });

  //Get the button
  var mybutton = $("#scrollTopBtn");

  // When the user scrolls down 20px from the top of the document, show the button
  window.onscroll = function () {
    scrollFunction();
  };

  function scrollFunction() {
    if (
      document.body.scrollTop > 40 ||
      document.documentElement.scrollTop > 40
    ) {
      mybutton.fadeIn(600);
    } else {
      mybutton.fadeOut(600);
    }
  }

  updateAdvertisers();
});

let signals = [];
let campaign = [];
let assets = [];

let filters = {
  signals: [],
  need_states: [],
  soma_semantics: [],
  mythic_narratives: [],
};

let tops = [];

const getData = async () => {
  $("#loading").show();

  $("#signalPreview").empty();

  let campaign = $("#campaignSelector").val();

  if (campaign != "") {
    await $.get("/api/get_signals_by_campaign/" + campaign, function (data) {
      signals = [...data];
    });
  }
};

const loadSignals = () => {
  $("#signalsCardContainer").empty();

  for (const [index, iterator] of signals.entries()) {
    let preview = "";
    let preview_url = iterator.preview;

    if (
      preview_url.indexOf(".mp4") >= 0 ||
      preview_url.indexOf(".webm") >= 0 ||
      preview_url.indexOf(".ogg") >= 0 ||
      preview_url.indexOf(".avi") >= 0 ||
      preview_url.indexOf(".mov") >= 0
    ) {
      preview = `
           
                    <video style="width:100%; height:auto" controls>
                        <source src="${preview_url}" type="video/mp4">
                        <source src="${preview_url}" type='video/webm'>
                        <source src="${preview_url}" type='video/ogg'>
                        <source src="${preview_url}" type='video/avi'>
                        <source src="${preview_url}" type='video/mov'>
                        Your browser does not support the video tag. <!-- Text to be shown incase browser doesnt support html5 -->
                    </video>
            `;
    } else if (
      preview_url.toLowerCase().indexOf(".jpeg") >= 0 ||
      preview_url.toLowerCase().indexOf(".jpg") >= 0 ||
      preview_url.toLowerCase().indexOf(".png") >= 0 ||
      preview_url.toLowerCase().indexOf(".gif") >= 0 ||
      preview_url.toLowerCase().indexOf(".svg") >= 0
    ) {
      preview = `
            <div style="
            background-image : url(${preview_url});
            width:auto; height:400px; background-repeat: no-repeat; background-size: contain;
            "></div>
        `;
    } else {
      preview = `
            <a  href="${preview_url}" download> <div class="icon voyager-file-text"></div>Download Files</a>
        `;
    }

    $("#signalsCardContainer").append(`
            <div class='row' style='padding-left:2em; padding-right:2em; '>
                <h4 style="border-bottom: 3px solid #ccc; padding-left: 2em;" >${iterator.name}</h4>
                <div class="col-sm-5" >
                    ${preview}
                </div>
                <div class="col-sm-7">
                    <div class="container">
                      <ul class="nav nav-tabs">
                        <li class="active"><a href="#tabs-1_${iterator.id}" data-toggle="tab">Chart</a></li>
                        <li><a href="#tabs-2_${iterator.id}" data-toggle="tab">Domains</a></li>
                      </ul>
                      <div class="tab-content">
                        <div id="tabs-1_${iterator.id}" class="tab-pane fade in active containerTab">
                          <!-- Contenido del tab 1 -->
                          <canvas id="Chart_radar_${iterator.id}" width="600" height="400"></canvas>
                        </div>
                        <div id="tabs-2_${iterator.id}" class="tab-pane fade containerTab">
                            <!-- Contenido del tab 2 -->
                            <table id="table_${iterator.id}" class="display" style="width:100%">
                              <thead>
                                <tr>
                                  <th>Url</th>
                                  <th>Correlation Score</th>
                                </tr>
                              </thead>
                              <tbody>
                              </tbody>
                            </table>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        `);

    $("#table_" + iterator.id).DataTable({
      processing: true,
      pageLength: 10,
      serverSide: true,
      deferRender: true,
      ajax: {
        url: "/api/get_domains_by_item/" + iterator.id,
        type: "GET",
      },
      columns: [
        { data: "url", className: "text-left" },
        { data: "score", className: "text-right" },
      ],
      columnDefs: [
        { className: "text-left", targets: "_all" },
        { targets: 1, className: "text-right" },
      ],
    });

    loadSignalChart(
      iterator.rsn_x_two_items_data,
      `Chart_radar_${iterator.id}`
    );
  }

  $("#loading").hide();
};

const loadSignalChart = (chartData, id) => {
  let datasetRadar = [];
  let entriesLabels = [];

  let keyToShow = [];
  for (const [index, iterator] of chartData.entries()) {
    let data = JSON.parse(iterator.data);
    delete data.name;
    let totalScore = 0;
    let count = 0;
    let maxValue = 0;
    for (let [name, value] of Object.entries(data)) {
      if (parseFloat(value) > 0) {
        totalScore += parseFloat(value);
        count++;
        if (parseFloat(value) > maxValue) {
          maxValue = parseFloat(value);
        }
      }
    }
    let average = totalScore / count;
    let limitToShow = average + (maxValue - average) / 4;
    for (let [name, value] of Object.entries(data)) {
      if (Object.keys(data).length <= 10 || parseFloat(value) >= limitToShow) {
        keyToShow.push(name);
      }
    }
  }

  for (const [index, iterator] of chartData.entries()) {
    let data = JSON.parse(iterator.data);

    delete data.name;

    let series = [];
    let labels = [];
    let totalScore = 0;
    let count = 0;
    for (let [name, value] of Object.entries(data)) {
      if (value != 0) {
        totalScore += parseFloat(value);
        count++;
      }
    }
    for (let [name, value] of Object.entries(data)) {
      if (keyToShow.includes(name)) {
        labels.push(name);
        series.push(((parseFloat(value) * 100) / totalScore).toFixed(2));
        //series.push((parseFloat(value)*100/totalScore).toFixed(2));
      }
    }

    datasetRadar.push({
      label: iterator.name,
      backgroundColor: colors[index], // "rgba(200,0,0,0.2)",
      data: series,
    });
    entriesLabels = labels;
  }

  $("#reportlabel").show();

  if (entriesLabels.length > 0) {
    var marksCanvas = document.getElementById(id);

    var marksData = {
      labels: entriesLabels,
      datasets: datasetRadar,
    };

    var options = {
      responsive: true,
      maintainAspectRatio: false,
      scale: {
        ticks: {
          beginAtZero: true,
          min: 0,
        },
      },
      scales: {
        r: {
          pointLabels: {
            font: {
              size: 18,
            },
          },
        },
      },
    };

    new Chart(marksCanvas, {
      type: "radar",
      data: marksData,
      options: options,
    });
  }

  return true;
};

const loadFilters = () => {
  $("#filters-container").slideUp();

  FillSelector("#filterSignal", filters.signals, true);
  FillSelector("#filterNeedStates", filters.need_states, true);
  FillSelector("#filterSomaSemantic", filters.soma_semantics, true);
  FillSelector("#filterMythicNarrative", filters.mythic_narratives, true);
  FillSelector("#filterPathosEthos", filters.pathosEthos, true);

  $("#filters-container").slideDown(500);

  $("#filters-container-loading").hide();
};

function getRandomArbitrary(min, max) {
  return parseInt(Math.random() * (max - min) + min);
}

const initView = () => {
  $("#filters-container-loading").hide();
  $("#topRalatedResonancesContainer-loading").hide();
  $("#topRalatedResonancesContainer").hide();
  $("#groupbyContainer").hide();
  $("#chartByDaypartContainer").hide();
  $("#filters-container").slideUp();
};

const updateAdvertisers = async () => {
  let advertisers = [];

  let org = $("#organization").val();
  if (org != "") {
    await $.get("/api/advertisers?organization=" + org, function (data) {
      advertisers = { ...data };
    });
  }

  let options = [];
  for (const iterator in advertisers) {
    options.push({
      value: advertisers[iterator].name,
      id: advertisers[iterator].id,
    });
  }

  FillSelector_simple("#advertiser", options, false);
};

const updateCampaignsSelector = async () => {
  let advertiser = $("#advertiser").val();

  let campaignData = [];

  if (advertiser != "") {
    await $.get(
      "/api/rsn_get_signal_campaigns_by_advertiser?advertiser=" +
        advertiser +
        "&type=x2",
      function (data) {
        campaignData = { ...data };
      }
    );
  }

  let options = [];

  for (const iterator in campaignData) {
    options.push({
      value: campaignData[iterator].name,
      id: campaignData[iterator].id,
    });
  }

  FillSelector_simple("#campaignSelector", options, false);
};

const topFunction = () => {
  $("html,body").animate({ scrollTop: 0 }, "slow");
};

//AUXILIAR FUNCTIONS

const FillSelector = (selector, options, selectAll) => {
  $(selector).multiselect("destroy");
  $(selector).empty();
  for (const option of options) {
    let newOption = new Option(option.value, option.id, selectAll, selectAll);
    $(selector).append(newOption);
  }
  initMultiSelect(selector);
};
const FillSelector_simple = (selector, options) => {
  $(selector).empty();
  $(selector).append(new Option("None", ""));
  for (const option of options) {
    let newOption = new Option(option.value, option.id);
    $(selector).append(newOption);
  }
};

const initMultiSelect = (selector) => {
  $(selector).multiselect({
    enableFiltering: true,
    includeSelectAllOption: true,
    enableCaseInsensitiveFiltering: true,
    buttonText: function (options) {
      if (options.length == 0) {
        return "None selected";
      } else {
        var selected = 0;
        options.each(function () {
          selected += 1;
        });
        return selected + " Selected";
      }
    },
  });
};

/*
    inclusive min (result can be equal to min value)
    exclusive max (result will not be to max value)
*/
const randomizeInteger = (min, max) => {
  if (max == null) {
    max = min == null ? Number.MAX_SAFE_INTEGER : min;
    min = 0;
  }

  min = Math.ceil(min); // inclusive min
  max = Math.floor(max); // exclusive max

  if (min > max - 1) {
    throw new Error("Incorrect arguments.");
  }

  return min + Math.floor((max - min) * Math.random());
};

const formatPercentage = (number) => {
  if (typeof number == "number") {
    return roundTwoDecimal(number) + " %";
  }
  return number;
};

const roundTwoDecimal = (number) => {
  return Math.round((number + Number.EPSILON) * 100) / 100;
};
