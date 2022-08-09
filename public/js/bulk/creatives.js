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
            creatives: data
        };
        await $.post("/api/execute_bulk_creatives", body,function (data) {response = data;}, 'json');

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
    if(validFile){
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
			// field delimiter. If id does not, then we know
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
                            || cells[1] !== 'creative_type_id' 
                            || cells[2] !== 'name' 
                            || cells[3] !== 'click_url' 
                            || cells[4] !== '3pas_tag_id'
                            || cells.length != 16
                        ) 
                        ){
                            alert("Invalid Csv format");
                            return false;
                        }
                        if(i>0){
                            data.push({
                                'id':	cells[0],
                                'creative_type_id':	cells[1],
                                'name'	:	cells[2],
                                'click_url':		cells[3],
                                '3pas_tag_id':		cells[4],
                                'landing_page'	:	cells[5],
                                'start_date'	:	cells[6],
                                'end_date'	:	cells[7],
                                'concept'	:	cells[8],
                                'ad_height'	:	cells[9],
                                'ad_width'	:	cells[10],
                                'tag_code'	:	cells[11].replaceAll('""', "'").replaceAll('"', ""),
                                '3rd_tracking'	:	cells[12],
                                'creative_attributes':	cells[13].replaceAll('"', ""),
                                'vast_code':	cells[14],
                                'skippable':	cells[15]
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
        alert("Please upload a valid CSV file.");
        return false;
    }
    return true;
}


const validateData = async () => {
    
    let errors='';
    let hasError = false;

    let tableItems;
    let index = 0;
    let concepts = {
        integers: [],
        names: []
    };
    let creatives = {
        ids: []
    };
    let newCreativeAmount = 0;
    for (let iterator of data) {

        if(! /^\d+$/.test(iterator.id) && iterator.id != '' ){
            hasError = true;
            errors +=`<p>Id must be an integer or empty(row ${index})</p>`;
            iterator.id='<label class="error">error</label>';
        }
        let showPreview = true;

        if(iterator.id == ''){
            newCreativeAmount++;

            if(parseInt(iterator.creative_type_id) != 1 && parseInt(iterator.creative_type_id) != 2){
                hasError = true;
                errors +=`<p>Creative type must be a number between 1-2 (row ${index})</p>`;
                iterator.creative_type_name='error';
            } else {
                iterator.creative_type_name = parseInt(iterator.creative_type_id) == 1 ? "Display" : "Video";
            }
    
            if(iterator.name.length >= 250) {
                hasError = true;
                errors +=`<p>Creative name must be less than 250 characters (row ${index})</p>`;
                iterator.name='<label class="error">error</label>';
            }
    
            if(iterator.concept.length >= 250 || iterator.concept.length == '') {
                hasError = true;
                errors +=`<p>Creative concept must be less than 250 characters and cannot be empty (row ${index})</p>`;
                iterator.concept='<label class="error">error</label>';
            }
    
            if(iterator.creative_attributes.length >  2 && ! iterator.creative_attributes.includes(',')) {
                hasError = true;
                errors +=`<p>Creative Attributes separator must be a coma(row ${index})</p>`;
            }
    
            if(iterator.start_date.length != '' || iterator.end_date.length != '') {

                let startDate = iterator.start_date.split("-");
        
                if(!iterator.start_date.includes('-') || startDate[0].length != 2 
                || startDate[1].length != 2 || startDate[2].length != 4
                || parseInt(startDate[0]) > 12  || parseInt(startDate[1]) > 31) {
                    hasError = true;
                    errors +=`<p>Creative start date must be mm-dd-yyyy (row ${index})</p>`;
                    iterator.start_date='<label class="error">error</label>';
                }
                let endDate = iterator.end_date.split("-");
                if(!iterator.end_date.includes('-') || endDate[0].length != 2 
                || endDate[1].length != 2 || endDate[2].length != 4
                || parseInt(endDate[0]) > 12  || parseInt(endDate[1]) > 31) {
                    hasError = true;
                    errors +=`<p>Creative end date must be mm-dd-yyyy (row ${index})</p>`;
                    iterator.end_date='<label class="error">error</label>';
                }
                startDate = Date.parse(iterator.start_date);
                endDate = Date.parse(iterator.end_date);
                if (endDate < startDate) {
                    hasError = true;
                    errors +=`<p>End date must be higher or equal than start date (row ${index})</p>`;
                    iterator.start_date='<label class="error">error</label>';
                    iterator.end_date='<label class="error">error</label>';
                }
            }
            
            if(!validURL(iterator.click_url)){
                hasError = true;
                errors +=`<p>Click Url is not a valid url (row ${index})</p>`;
            }
    
            if(!validURL(iterator.landing_page)){
                hasError = true;
                errors +=`<p>Landing page is not a valid url (row ${index})</p>`;
            }
    
    
        
    
            if(parseInt(iterator.creative_type_id) == 1){
                if(! /^\d+$/.test(iterator.ad_width)){
                    hasError = true;
                    showPreview = false;
                    errors +=`<p>Width must be an integer number (row ${index})</p>`;
                    iterator.ad_width='<label class="error">error</label>';
                }
        
                if(! /^\d+$/.test(iterator.ad_height)){
                    hasError = true;
                    showPreview = false;
                    errors +=`<p>Width must be an integer number (row ${index})</p>`;
                    iterator.ad_height='<label class="error">error</label>';
                }
            }
    
            if(parseInt(iterator.creative_type_id) == 2){
                if(iterator.vast_code == ''){
                    hasError = true;
                    errors +=`<p>Vast code is mandatory for type video (row ${index})</p>`;
                }
                if(parseInt(iterator.skippable) != 0 && parseInt(iterator.skippable) != 1){
                    hasError = true;
                    errors +=`<p>Skippable values allowed 0/1 (row ${index})</p>`;
                }
                iterator.ad_width = "-";
                iterator.ad_height = "-";
            }


        } else {

            if(iterator.creative_type_id != ''){
                if(parseInt(iterator.creative_type_id) != 1 && parseInt(iterator.creative_type_id) != 2){
                    hasError = true;
                    errors +=`<p>Creative type must be a number between 1-2 (row ${index})</p>`;
                    iterator.creative_type_name='error';
                } else {
                    iterator.creative_type_name = parseInt(iterator.creative_type_id) == 1 ? "Display" : "Video";
                }
            }
            
            if(iterator.name != ''){
                if(iterator.name.length >= 250) {
                    hasError = true;
                    errors +=`<p>Creative name must be less than 250 characters (row ${index})</p>`;
                    iterator.name='<label class="error">error</label>';
                }
            }
            
            if(iterator.concept != ''){
                if(iterator.concept.length >= 250 || iterator.concept.length == '') {
                    hasError = true;
                    errors +=`<p>Creative concept must be less than 250 characters (row ${index})</p>`;
                    iterator.concept='<label class="error">error</label>';
                }
            }
            
            if(iterator.creative_attributes != ''){
                if(iterator.creative_attributes.length >  2 && ! iterator.creative_attributes.includes(',')) {
                    hasError = true;
                    errors +=`<p>Creative Attributes separator must be a coma(row ${index})</p>`;
                }
            }

   
            if(iterator.start_date != '' || iterator.end_date != '') {
                if(iterator.start_date != '' && iterator.end_date != '') {
                    let startDate = iterator.start_date.split("-");
           
                    if(!iterator.start_date.includes('-') || startDate[0].length != 2 
                    || startDate[1].length != 2 || startDate[2].length != 4
                    || parseInt(startDate[0]) > 12  || parseInt(startDate[1]) > 31) {
                        hasError = true;
                        errors +=`<p>Creative start date must be mm-dd-yyyy (row ${index})</p>`;
                        iterator.start_date='<label class="error">error</label>';
                    }
                    let endDate = iterator.end_date.split("-");
                    if(!iterator.end_date.includes('-') || endDate[0].length != 2 
                    || endDate[1].length != 2 || endDate[2].length != 4
                    || parseInt(endDate[0]) > 12  || parseInt(endDate[1]) > 31) {
                        hasError = true;
                        errors +=`<p>Creative end date must be mm-dd-yyyy (row ${index})</p>`;
                        iterator.end_date='<label class="error">error</label>';
                    }
                    startDate = Date.parse(iterator.start_date);
                    endDate = Date.parse(iterator.end_date);
                    if (endDate < startDate) {
                        hasError = true;
                        errors +=`<p>End date must be higher or equal than start date (row ${index})</p>`;
                        iterator.start_date='<label class="error">error</label>';
                        iterator.end_date='<label class="error">error</label>';
                    }
                } else {
                    hasError = true;
                    errors +=`<p You must indicate start and end date in order to validate the period (row ${index})</p>`;
                }
            }
            
            if(iterator.click_url != ''){
                if(!validURL(iterator.click_url)){
                    hasError = true;
                    errors +=`<p>Click Url is not a valid url (row ${index})</p>`;
                }
            }
            if(iterator.landing_page != ''){
                if(!validURL(iterator.landing_page)){
                    hasError = true;
                    errors +=`<p>Landing page is not a valid url (row ${index})</p>`;
                }
            }
    
            showPreview = false;
            if(parseInt(iterator.creative_type_id) == 1){
                if(iterator.ad_width != ''){
                    if(! /^\d+$/.test(iterator.ad_width)){
                        hasError = true;
                        showPreview = false;
                        errors +=`<p>Width must be an integer number (row ${index})</p>`;
                        iterator.ad_width='<label class="error">error</label>';
                    }
                }
                if(iterator.ad_height != ''){
                    if(! /^\d+$/.test(iterator.ad_height)){
                        hasError = true;
                        showPreview = false;
                        errors +=`<p>Width must be an integer number (row ${index})</p>`;
                        iterator.ad_height='<label class="error">error</label>';
                    }
                }
            }
    
            if(parseInt(iterator.creative_type_id) == 2){
                if(iterator.skippable != ''){
                    if(parseInt(iterator.skippable) != 0 && parseInt(iterator.skippable) != 1){
                        hasError = true;
                        errors +=`<p>Skippable values allowed 0/1 (row ${index})</p>`;
                    }
                }
                iterator.ad_width = "-";
                iterator.ad_height = "-";
            }
        }

        tableItems+=`<tr>
        <th scope="row"> ${ iterator.id == '' ? 'new' : iterator.id } </th>
        <td>${iterator.creative_type_name}</td>
        <td>${iterator.name}</td>
        <td>${iterator.concept}</td>
        <td>${iterator.start_date}</td>
        <td>${iterator.end_date}</td>
        <td>${iterator.ad_width}</td>
        <td>${iterator.ad_height}</td>`;

        if(showPreview) {
            if(parseInt(iterator.creative_type_id) == 1) {
                tableItems+=` <td><a href="#" class="btn btn-info previewMarkup" 
                data-height="${iterator.ad_height}"
                data-width="${iterator.ad_width}"
                data-markup="${iterator.tag_code}">Preview</a></td>`;
            } else {
                tableItems+=`<td></td>`;
            }
        } else {
            if(iterator.id==''){
                tableItems+=`<td><label class="error">Review width/height</label></td>`;
            }else{
                tableItems+=`<td></td>`;
            }
           
        }
        tableItems+=` </tr>`;
        index++;

        if(/^\d+$/.test(iterator.concept)){
            concepts.integers.push(iterator.concept);
        }else{
            concepts.names.push(iterator.concept);
        }

        if(/^\d+$/.test(iterator.id)){
            creatives.ids.push(iterator.id);
        }
    }
    $('#tbodyreview').append(tableItems);
    $('#reviewContainer').show();
    $('.previewMarkup').click(function(e){
        e.preventDefault();
      
        $("#modalBody").append();

        var w = window.open('','Ad Preview','"height='+$(this).data('height')+',width='+$(this).data('width')+'"');
        var html = $(this).data('markup');

        w.document.write(html);
        w.resizeTo(parseInt($(this).data('width'))+60, parseInt($(this).data('height'))+100);

    });

    if (hasError) {
        $('#ErrorsContainer').show();
        $('#summaryContainer').hide();
       
        $('#executeButton').hide();;
        $('#errors').empty();
        $('#errors').append(errors);

        $('#newCreativesAmount').empty();
        $('#newCreativesAmount').append('-');
        $('#newConceptsAmount').empty();
        $('#newConceptsAmount').append('-');
    } else {
        let response;
        let body = {
            advertiser: $('#advertiser_id_field').val(),
            concepts: concepts,
            creatives: creatives
        };
        
        await $.post("/api/check_existance/", body,function (data) {response = data;}, 'json');

        if(response.error == true) {
            $('#newConceptsAmount').empty();
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
           
            $('#executeButton').hide();;
            $('#errors').empty();
            $('#errors').append(errors);

        }else{
            $('#ErrorsContainer').hide();
            $('.duplicatedContainer').hide();

            $('#summaryContainer').show();
            $('#executeButton').show();
            $('#newCreativesAmount').empty();
            $('#newCreativesAmount').append(newCreativeAmount);
            $('#newConceptsAmount').empty();
            $('#newConceptsAmount').append(response.new);
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