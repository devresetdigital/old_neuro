$(document).ready(function () {
    
    initView();
    $('#messageContainer').hide();
    $('#bulkFile').change(function(e){
        data = [];
        $('#tbodyreview').empty();
        $('#errors').empty();
        validate();
    });

    $('#advertiser_id_field').change(function(e){
        initView();
        if ($(this).val()!='') {
            $('#fileContainer').show();
        } else {
            $('#fileContainer').hide();
        }
    });


    $('#bulkFile').click(async function(e){
        $('#bulkFile').val('');
        $('#tbodyreview').empty();
        $('#errors').empty();
        $('#ErrorsContainer').hide();
        $('.duplicatedContainer').hide();
        $('#summaryContainer').hide();
        $('#reviewContainer').hide();
        $('#executeButton').hide();
        $('#messageContainer').hide();
    });

    $('#executeButton').click(async function(e){
        let response;
        let body = {
            advertiser: $('#advertiser_id_field').val(),
            strategies: data
        };
        await $.post("/api/execute_bulk_strategies", body,function (data) {response = data;}, 'json');

        if (response.error){
            $('#messageError').show();
            $('#messageSuccess').hide();
        } else {
            $('#messageSuccess').show();
            $('#messageError').hide();
        }
        $('#messageContainer').show();
        $('#advertiser_id_field').val(null).trigger('change');
        initView();
    });
});

const initView = () => {
    data = [];
    $('#bulkFile').val('');
    $('#tbodyreview').empty();
    $('#errors').empty();
    $('#ErrorsContainer').hide();
    $('.duplicatedContainer').hide();
    $('#fileContainer').hide();
    $('#summaryContainer').hide();
    $('#reviewContainer').hide();
    $('#executeButton').hide();   
}

let data = [];

const validate = async () => {
    let validFile = await validateBulkFile();
    if(validFile == true){
        setTimeout(()=>{ validateData();},1000);
    }
}


// This will parse a delimited string into an array of
	// arrays. The default delimiter is the comma, but this
	// can be overriden in the second argument.
    const CSVToArray = ( strData, strDelimiter ) => {
		// Check to see if the delimiter is defined. If not,
		// then default to comma.
		strDelimiter = (strDelimiter || ",");

		// Create a regular expression to parse the CSV values.
		var objPattern = new RegExp(
			(
				// Delimiters.
				"(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +

				// Quoted fields.
				"(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +

				// Standard fields.
				"([^\"\\" + strDelimiter + "\\r\\n]*))"
			),
			"gi"
			);


		// Create an array to hold our data. Give the array
		// a default empty first row.
		var arrData = [[]];

		// Create an array to hold our individual pattern
		// matching groups.
		var arrMatches = null;


		// Keep looping over the regular expression matches
		// until we can no longer find a match.
		while (arrMatches = objPattern.exec( strData )){

			// Get the delimiter that was found.
			var strMatchedDelimiter = arrMatches[ 1 ];

			// Check to see if the given delimiter has a length
			// (is not the start of string) and if it matches
			// field delimnueva campaña de testiter. If id does not, then we know
			// that this delimiter is a row delimiter.
			if (
				strMatchedDelimiter.length &&
				(strMatchedDelimiter != strDelimiter)
				){

				// Since we have reached a new row of data,
				// add an empty row to our data array.
				arrData.push( [] );

			}


			// Now that we have our delimiter out of the way,
			// let's check to see which kind of value we
			// captured (quoted or unquoted).
			if (arrMatches[ 2 ]){

				// We found a quoted value. When we capture
				// this value, unescape any double quotes.
				var strMatchedValue = arrMatches[ 2 ].replace(
					new RegExp( "\"\"", "g" ),
					"\""
					);

			} else {

				// We found a non-quoted value.
				var strMatchedValue = arrMatches[ 3 ];

			}


			// Now that we have our value string, let's add
			// it to the data array.
			arrData[ arrData.length - 1 ].push( strMatchedValue );
		}

		// Return the parsed data.
		return( arrData );
	}

const validateBulkFile = () => {
    var fileUpload = document.getElementById("bulkFile");
    var regex = /.+(.csv)$/;
    if (regex.test(fileUpload.value.toLowerCase())) {
        if (typeof (FileReader) != "undefined") {
            var reader = new FileReader();
            reader.onload = function (e) {
                let rows = CSVToArray(e.target.result, ",");
                let i = 0;
                for (const cells of rows) {
                    if (cells.length > 1) { 
                        if (i==0 &&
                            (  cells[0] !== 'id' 
                            || cells[1] !== 'campaign' 
                            || cells[2] !== 'name' 
                            || cells[3] !== 'date_start' 
                            || cells[4] !== 'date_end'
                            || cells.length != 59
                            ) 
                        ){
                            alert("Invalid Csv format, please check the columns");
                            return false;
                        }
                        if(i>0){
                            data.push({
                                "id":	cells[0],
                                "campaign":	cells[1],
                                "name":	cells[2],
                                "date_start":	cells[3],
                                "date_end":	cells[4],
                                "budget":	cells[5],

                                "goal_type":	cells[6],
                                "goal_amount":	cells[7],
                                "goal_bid_for":	cells[8],
                                "goal_min_bid":	cells[9],
                                "goal_max_bid":	cells[10],
                                "m_type":	cells[11],
                                "m_amount":	cells[12],
                                "m_stype":	cells[13],
                                "i_type":	cells[14],
                                "i_amount":	cells[15],
                                "i_stype":	cells[16],
                                "f_type":	cells[17],
                                "f_amount":	cells[18],
                                "f_stype":	cells[19],

                                "selected_concepts":	cells[20],
                                "country_inc_exc":	cells[21],
                                "country":	cells[22],
                                "region_inc_exc":	cells[23],
                                "region":	cells[24],
                                "city_inc_exc":	cells[25],
                                "city":	cells[26],
                                "lang_inc_exc":	cells[27],
                                "language":	cells[28],
                                "geofencing_inc_exc":	cells[29],
                                "geofencingjson":	cells[30],

                                "sitelists_inc_exc":	cells[31],
                                "sitelists":	cells[32],
                                "iplists_inc_exc":	cells[33],
                                "iplists":	cells[34],
                                "pmps":	cells[35],
                                "open_market":	cells[36],
                                "ssps_inc_exc":	cells[37],
                                "ssps":	cells[38],
                                "ziplists_inc_exc":	cells[39],
                                "ziplists":	cells[40],
                                "keywordslist_inc_exc":	cells[41],
                                "keywordslists":	cells[42],
                                "devices_inc_exc":	cells[43],
                                "device":	cells[44],
                                "inventories_inc_exc":	cells[45],
                                "inventory_type":	cells[46],
                                "isp_inc_exc":	cells[47],
                                "isps":	cells[48],
                                "os_inc_exc":	cells[49],
                                "os":	cells[50],
                                "browser_inc_exc":	cells[51],
                                "browser":	cells[52],
                                "pixels_inc_exc":	cells[53],
                                "pixels":	cells[54],
                                "custom_datas_inc_exc":	cells[55],
                                "custom_datas":	cells[56],
                                "segments_inc_exc":	cells[57],
                                "segments":	cells[58]
                            });
                        }
                    }
                    i = i +1;
                }
            }
            reader.readAsText(fileUpload.files[0]);
        } else {
            alert("This browser does not support HTML5.");
            return false;
        }
    } else {
        alert("Please uploadnueva campaña de test a valid CSV file.");
        return false;
    }
    return true;
}


const validateData = async () => {


    if(data.length == 0){return true}

    let errors='';
    let hasError = false;

    let tableItems;
    let index = 0;
    let campaigns = {
        integers: [],
        names: []
    };
    let strategies = {
        ids:[]
    }
    let verifyFields = {
        country: [],
        region: [],
        city: [],
        language:[]
    };
    let targeting = {
        sitelist:[],
        iplist:[],
        pmps:[],
        ssps:[],
        ziplist:[],
        keywords:[],
        devices:[],
        inventory:[],
        os:[],
        browser:[]
    }
    for (let iterator of data) {

        if(iterator.campaign.length >= 250 || iterator.campaign.length == ''){
            hasError = true;
            errors +=`<p>Campaign must be less than 250 characters and can't be empty(row ${index})</p>`;
            iterator.campaign='<label class="error">error</label>';
        }

        if(iterator.name.length >= 250 || iterator.name.length == ''){
            hasError = true;
            errors +=`<p>Name must be less than 250 characters and can't be empty(row ${index})</p>`;
            iterator.name='<label class="error">error</label>';
        }

        if (iterator.date_start.length != '' || iterator.date_end.length != '') {

            let startDate = iterator.date_start.split("-");
            if(!iterator.date_start.includes('-') || startDate[0].length != 2 
            || startDate[1].length != 2 || startDate[2].length != 4
            || parseInt(startDate[0]) > 12  || parseInt(startDate[1]) > 31) {
                hasError = true;
                errors +=`<p>Creative start date must be mm-dd-yyyy (row ${index})</p>`;
                iterator.date_start='<label class="error">error</label>';
            }
            let endDate = iterator.date_end.split("-");
            if(!iterator.date_end.includes('-') || endDate[0].length != 2 
            || endDate[1].length != 2 || endDate[2].length != 4
            || parseInt(endDate[0]) > 12  || parseInt(endDate[1]) > 31) {
                hasError = true;
                errors +=`<p>Creative end date must be mm-dd-yyyy (row ${index})</p>`;
                iterator.date_end='<label class="error">error</label>';
            }
            startDate = Date.parse(iterator.date_start);
            endDate = Date.parse(iterator.date_end);
            if (endDate < startDate) {
                hasError = true;
                errors +=`<p>End date must be higher or equal than start date (row ${index})</p>`;
                iterator.date_start='<label class="error">error</label>';
                iterator.date_end='<label class="error">error</label>';
            }
        }

        tableItems+=`<tr>
        <th scope="row"> ${iterator.campaign} </th>
        <td>${iterator.name}</td>
        <td>${iterator.date_start}</td>
        <td>${iterator.date_end}</td>
        <td>${iterator.budget}</td>     
        <td>${iterator.goal_amount}</td>
        <td>${iterator.goal_min_bid}</td>
        <td>${iterator.goal_max_bid}</td>
        `;
        tableItems+=` </tr>`;
        index++;

        if(/^\d+$/.test(iterator.campaign)){
            campaigns.integers.push(iterator.campaign);
        }else{
            campaigns.names.push(iterator.campaign);
        }

        if(/^\d+$/.test(iterator.id)){
            strategies.ids.push(iterator.id);
        }

        if(iterator.country !="***"){
            verifyFields.country.push(iterator.country);
        }
        if(iterator.region !="***"){
            verifyFields.region.push(iterator.region);
        }
        if(iterator.city !="***"){
            verifyFields.city.push(iterator.city);
        }
        if(iterator.language !="***"){
            verifyFields.language.push(iterator.language);
        }

        if( iterator.sitelists != "" &&  iterator.sitelists !="***"){
            targeting.sitelist.push(iterator.sitelists);
        }
        if(  iterator.iplists !=""  &&  iterator.iplists !="***"){
            targeting.iplist.push(iterator.iplists);
        }
        if( iterator.pmps !=""  &&  iterator.pmps !="***"){
            targeting.pmps.push(iterator.pmps);
        }
        if (iterator.ssps != '' && iterator.ssps != '***') {
            if (iterator.ssps.toLowerCase() === 'all') {
                let ssps = await get_ssp_by_advertiser(
                $('#advertiser_id_field').val()
                );
                ssps = ssps.map((s) => s.name).join();
                targeting.ssps.push(ssps);
                iterator.ssps = ssps;
            } else {
                targeting.ssps.push(iterator.ssps);
            }
        }
        if(  iterator.ziplists !=""  &&  iterator.ziplists !="***"){
            targeting.ziplist.push(iterator.ziplists);
        }
        if( iterator.keywordslists !=""  &&  iterator.keywordslists !="***"){
            targeting.keywords.push(iterator.keywordslists);
        }
        if(  iterator.device !=""  &&  iterator.device !="***"){
            targeting.devices.push(iterator.device);
        }
        if(  iterator.inventory_type !=""  &&  iterator.inventory_type !="***"){
            targeting.inventory.push(iterator.inventory_type);
        }
        if(   iterator.os !=""  &&  iterator.os !="***"){
            targeting.os.push(iterator.os);
        }
        if( iterator.browser !=""  &&  iterator.browser !="***"){
            targeting.browser.push(iterator.browser);
        }

    }
    $('#tbodyreview').append(tableItems);
    $('#reviewContainer').show();


    if (hasError) {
        $('#ErrorsContainer').show();
        $('#summaryContainer').hide();
       
        $('#executeButton').hide();;
        $('#errors').empty();
        $('#errors').append(errors);

        $('#newStrategiesAmount').empty();
        $('#newStrategiesAmount').append('-');
        $('#newCampaignsAmount').empty();
        $('#newCampaignsAmount').append('-');
    } else {
        let response;
        let body = {
            advertiser: $('#advertiser_id_field').val(),
            campaigns: campaigns,
            strategies: strategies,
            geofencing: verifyFields,
            targeting:targeting
        };
        
        await $.post("/api/check_existance_campaigns/", body,function (data) {response = data;}, 'json');

        if(response.error == true){
            $('#newCampaignsAmount').empty();
            for (const iterator of response.message) {
                errors +=`<p>${iterator}</p>`;
            }
            let coincidences =`<h4>Please use one of the following ids instead the name</h4>`;
            for (const iterator of response.coincidences) {
                for (const dup of iterator) {
                    coincidences+=`<p>${dup.id} - ${dup.name}</p>`
                }
            }
            if(response.coincidences.length){
                $('.duplicatedContainer').show();
                $('#duplicated').empty();
                $('#duplicated').append(coincidences);
            }

            $('#ErrorsContainer').show();
            $('#summaryContainer').hide();
           
            $('#executeButton').hide();
            $('#errors').empty();
            $('#errors').append(errors);

        }else{
            $('#ErrorsContainer').hide();
            $('.duplicatedContainer').hide();

            $('#summaryContainer').show();
            $('#executeButton').show();
            $('#newStrategiesAmount').empty();
            $('#newStrategiesAmount').append(data.length);
            $('#newCampaignsAmount').empty();
            $('#newCampaignsAmount').append(response.new);
        }
    }
}

const validURL = (str) => {
    if(str==""){
        return false;
    }
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
      '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
      '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
      '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
      '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
      '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
    return !!pattern.test(str);
}


const blankAdForm = () => {
    $('#adName').val('');
    $('#adPreview').val('');
   
} 

const sliceArray = (arrayToSplit ,elementsAmount = 1000) => {
    let response = [];
    let arrayLength = arrayToSplit.length;
    let i=0;
    let headers = arrayToSplit.shift();
    while (i < arrayLength) {
        let j = i + elementsAmount;
        response.push([headers , ... arrayToSplit.slice(i, j)]);
        i=j;
    }
    return response;
}

const get_ssp_by_advertiser = async (advertiser) => {
    let response = null;
    await $.get(
      `/api/ssps_by_advertiser/${advertiser}`,
      function(data) {
        response = data;
      },
      'json'
    );
    return response;
}