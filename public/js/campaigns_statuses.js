$(document).ready(function () {
    $('#speedWarning').hide();
    $('#search').keyup(function (e) {
        $('.searchrow-campaign').hide();
        $('.searchrow-strategy').hide();
        let key = this.value.toUpperCase();
        if(key==''){
            clearSearch();
        }else{
            let result = $("tr[data-key-search*='"+key+"']");
            if(result.length>0){
                result.show();
            }
        }
    });
    $('#copy').click(function (e) {
        $.get("/api/exportPacing?json=true", function (data) {
            var csv = convert(data.data);
             // Create a dummy input to copy the string array inside it
            var dummy = document.createElement("textarea");

            // Add it to the document
            document.body.appendChild(dummy);

            // Set its ID
            dummy.setAttribute("id", "dummy_id");

            // Output the array into it
            document.getElementById("dummy_id").value=csv;

            // Select it
            dummy.select();

            // Copy its contents
            document.execCommand("copy");

            // Remove it as its not needed anymore
            document.body.removeChild(dummy);
        });
    });


    $('#separator').change(function (e) {
        if($(this).val()=='comma'){
            $('.period').hide()
            $('.comma').show()
        }else{
            $('.comma').hide()
            $('.period').show()
        }
    });


    calculateVelocity();
});



const clearSearch = () => {
    $('#search').val('');
    $('.searchrow-campaign').show();
    $('.searchrow-strategy').show();
}

const calculateVelocity = async () => {

    try {

        let data1 = [];
        let data2 = [];

        await $.get("/api/get_real_time_spent", function (data) {
            data1 = { ...data.data };
        });
    
        await timeout(30000);
    
        await $.get("/api/get_real_time_spent", function (data) {
            data2 = { ...data.data };
        });


        if (data2.seconds < data1.seconds){
            alert('Please Reload the page to calculate impressions/spent per minute');
            return true;
        } 
    
      
        for (const campaign of Object.keys(idShown)) {
            let spentCampaign = 0;
            let impressionsCampaign = 0;
    
            for (const strategy of Object.keys(idShown[campaign])) {

                let strategyId =idShown[campaign][strategy];

                if(data2.campagins[campaign+','+ strategyId] == undefined || data1.campagins[campaign+','+ strategyId] == undefined){continue;}
               
                let secondsLaps = parseInt(data2.seconds) - parseInt(data1.seconds);
    
                let spentLaps = parseFloat(data2.campagins[campaign+','+ strategyId].spent) -  parseFloat(data1.campagins[campaign+','+ strategyId].spent);
                let impressionsLaps = parseInt(data2.campagins[campaign+','+ strategyId].impression) -  parseInt(data1.campagins[campaign+','+ strategyId].impression);
    
                let SpentPerMinute = spentLaps * 60 / secondsLaps;
                let ImpressionsPerMinute = parseInt(impressionsLaps * 60 / secondsLaps);
                
                spentCampaign += SpentPerMinute;
                impressionsCampaign += ImpressionsPerMinute;
    
                $('#strategy-spent-'+strategyId).text(formatSpent(SpentPerMinute)); 
                $('#strategy-impressions-'+strategyId).text(ImpressionsPerMinute);
    
    
                if($('#strategy-'+strategyId).data('status')!= 0 && ImpressionsPerMinute < 1 && SpentPerMinute < 0.005 && !$('#strategy-'+strategyId).hasClass('table-danger')){
                    $('#strategy-'+strategyId).removeClass("table-info");
                    $('#strategy-'+strategyId).removeClass("table-success");
                    $('#strategy-'+strategyId).removeClass("table-danger");
                    $('#strategy-'+strategyId).addClass("table-warning");
    
                }
            }
            $('#campaign-spent-'+campaign).text(formatSpent(spentCampaign));
            $('#campaign-impressions-'+campaign).text(impressionsCampaign);
       
            if($('#campaign-'+campaign).data('status')!= 0 && impressionsCampaign < 1 && spentCampaign < 0.005 && !$('#campaign-'+campaign).hasClass('table-danger')){
                $('#campaign-'+campaign).removeClass("table-info");
                $('#campaign-'+campaign).removeClass("table-success");
                $('#campaign-'+campaign).removeClass("table-danger");
                $('#campaign-'+campaign).addClass("table-warning");
            }
        }   
    } catch (error) {
        alert('not enough data to calculate speed, pelase reload the page');
     
    }
 
}   

const formatSpent = (spent) => {
    return "$"+(Math.round(spent * 100) / 100).toFixed(2);
}

function timeout(ms) {
    ProgressCountdown(ms);
    return new Promise(resolve => setTimeout(resolve, ms));
}

function ProgressCountdown(timeleft) {
  
    timeleft /=1000;
    timeleft++
    return new Promise((resolve, reject) => {
      var countdownTimer = setInterval(() => {
        timeleft--;
        $('#countdown_number').text(timeleft);
        $('#speedWarning').fadeIn();
        if (timeleft <= 1) {
        $('#speedWarning').fadeOut();
          clearInterval(countdownTimer);
          resolve(true);
        }
      }, 1000);
    });
  }

/**
 * 
 *  JSON TO CSV CONVERTER
 * 
 * 
 */

var errorMissingSeparator = 'Missing separator option.',
		  errorEmpty = 'JSON is empty.',
		  errorEmptyHeader = 'Could not detect header. Ensure first row cotains your column headers.',
		  errorNotAnArray = 'Your JSON must be an array or an object.',
		  errorItemNotAnObject = 'Item in array is not an object: {0}';


  function isObject(o) {
    return o && typeof o == 'object';
  }

  function getKeys(o) {
    if (!isObject(o)) return [];
    return Object.keys(o);
  }

	function convert(data, options) {
		options || (options = {});
		
    if (!isObject(data)) throw errorNotAnArray;
    if (!Array.isArray(data)) data = [data];
		
    var separator = options.separator || ',';
		if (!separator) throw errorMissingSeparator;

    var flatten = options.flatten || false;
    if (flatten) data = flattenArray(data);

    var allKeys = [],
        allRows = [];
    for (var i = 0; i < data.length; i++) {
    	var o = data[i],
    			row = {};
    	if (o !== undefined && o !== null && (!isObject(o) || Array.isArray(o)))
    		throw errorItemNotAnObject.replace('{0}', JSON.stringify(o));
    	var keys = getKeys(o);
    	for (var k = 0; k < keys.length; k++) {
    		var key = keys[k];
    		if (allKeys.indexOf(key) === -1) allKeys.push(key);
    		var value = o[key];
    		if (value === undefined && value === null) continue;
        if (typeof value == 'string') {
          row[key] = '"' + value.replace(/"/g, options.output_csvjson_variant ? '\\"' : '""') + '"';
          if (options.output_csvjson_variant) row[key] = row[key].replace(/\n/g, '\\n');
        } else {
          row[key] = JSON.stringify(value);
          if (!options.output_csvjson_variant && (isObject(value) || Array.isArray(value)))
            row[key] = '"' + row[key].replace(/"/g, '\\"').replace(/\n/g, '\\n') + '"';
        }
    	}
    	allRows.push(row);
    }

    keyValues = [];
    for (var i = 0; i < allKeys.length; i++) {
      keyValues.push('"' + allKeys[i].replace(/"/g, options.output_csvjson_variant ? '\\"' : '""') + '"');
    }

    var csv = keyValues.join(separator)+'\n';
    for (var r = 0; r < allRows.length; r++) {
    	var row = allRows[r],
    			rowArray = [];
    	for (var k = 0; k < allKeys.length; k++) {
    		var key = allKeys[k];
    		rowArray.push(row[key] || (options.output_csvjson_variant ? 'null' : ''));
    	}
    	csv += rowArray.join(separator) + (r < allRows.length-1 ? '\n' : '');
    }
    
    return csv;
	}
	