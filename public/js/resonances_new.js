$(document).ready(function () {
    
    $('#needstatesFile').change(function(e){
        validateNeedStates();
    });
    $('#daypartsFile').change(function(e){
        validateDayparts();
    });
    $('#resonanceFile').change(function(e){
        validateResonance();
    });
    $('#motivationFile').change(function(e){
        validateMotivation();
    });

    $('#liftFile').change(function(e){
        validateLift();
    });

    $('#organization').change(async function () {
       await updateAdvertisers();
    });

    initView();
});


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

    FillSelector('#advertiser', options, false);

}





const initView = () => {
   if(campaignEdition!== undefined && campaignEdition != false) {
        fillForm();
        blankAdForm();
   } else {
        $('#adsCard').hide();
        blankAdForm();
   }

}

let adIdtoDelete = 0;

let newAd;

let campaign = {
    name: '',
    id: 0
}

const fillForm = () => {
    $('#saveCampaign').prop("disabled", true);
    $('#campaignName').prop("disabled", true);
    campaign.name = campaignEdition.name;
    campaign.id = campaignEdition.id;
    $('#campaignName').val(campaign.name);

    $('#organization').val(campaignEdition.organization_id).trigger('change');

    $('#organization').prop("disabled", true);
    $('#advertiser').prop("disabled", true);
    $('#saveCampaign').hide();

    for (const iterator of campaignEdition.RsnAds) {
        insertAdInView(iterator);
    }
}

const saveCampaign = async () => {

    let response;

    $('#saveCampaign').prop("disabled", true);
    $('#campaignName').prop("disabled", true);
    $('#organization').prop("disabled", true);
    $('#advertiser').prop("disabled", true);

    campaign.name =  $('#campaignName').val();
    campaign.organization =  $('#organization').val();
    campaign.advertiser =  $('#advertiser').val();

    await $.post("/api/rsn_campaign/", campaign,function (data) {response = data;}, 'json');

    if (response.error) {
        flashCampaignMessage('error',response.message);
        $('#saveCampaign').prop("disabled", false);
        $('#campaignName').prop("disabled", false);
        $('#organization').prop("disabled", false);
        $('#advertiser').prop("disabled", false);
    } else {
        campaign.id = response.data.id;
        flashCampaignMessage('success',response.message);
        $('#saveCampaign').hide();
        $('#adsCard').fadeIn(600);
        $('#adsContainer').fadeIn(600);
        $('#newAd').fadeIn(600);
    }

}   


const saveNewAd = async () => {
    if(validateNewAd()){
        $('#loadingAd').show();
        $('#saveAd').prop("disabled", true);
        let response;
        let responseResonance;

        let body =  {
            adName: newAd.adName,
            adPreview: newAd.adPreview,
            campaingId: newAd.campaingId,
            needstateData:  newAd.needstateData,
            motivationData: newAd.motivationData,
            daypartData:  newAd.daypartData,
            liftData:  newAd.liftData,
        }
        
        await $.post("/api/rsn_ad/", body,function (data) {response = data;}, 'json');

        if (response.error) {
            alert('something went wrong, please try again')
            return;
        } else {

            let resonanceBody = sliceArray(newAd.resonanceData);
            for (const rsnBody of resonanceBody) {
                await $.post("/api/rsn_ad_resonance/"+response.data.id, {resonance: JSON.stringify(rsnBody)} ,function (data) {responseResonance = data;}, 'json');
            
                if (responseResonance.error) {
                    alert(responseResonance.message);
                    $('#loadingAd').hide();
                    return false;
                }
            }
            $('#saveAd').prop("disabled", false);
            blankAdForm();

            $('#loadingAd').hide();
        }
        insertAdInView(responseResonance.data)
    }
    return false;
}

const insertAdInView = (ad) => {

let html = `<div class="col-md-4" id="ad-`+ad.id+`">
<div class="jumbotron ads-jumbotron">
    <h2>`+ad.name+`</h2>
    `;
if (ad.tag_preview != undefined){
    html += ad.tag_preview;
}    
html += `
    <a onClick="modalDeleteAd(`+ad.id+`)" class="btn btn-danger btn-large">Delete</a>
</div>
</div>`;

$('#adsAdded').append(html);

}

const modalDeleteAd = (adId) => {
    adIdtoDelete = adId;
    $('#modalDelete').modal();
}

const deleteAD = async () => {
    await $.post("/api/rsn_delete_ad/"+adIdtoDelete, adIdtoDelete,function (data) {response = data;}, 'json');
    if (response.error) {
        alert('something went wrong, please try again')
        return;
    } else {
        $('#ad-'+adIdtoDelete).remove();
        adIdtoDelete = 0;
        $('#modalDelete').modal('hide');
    }
}


const showPreview = (showModal = true) => {
    let preview = $('#adPreview').val();
    
    if(preview == ''){
        $('#adPreviewEmpty').fadeIn(600);
        newAd.adPreview ='';
        return '';
    } else {
        $('#adPreviewEmpty').fadeOut(600);
    }

    let htmlToAppend = preview;
    if (validURL(preview)){
        htmlToAppend = buildImagePreview(preview)
    }
    newAd.adPreview = htmlToAppend;
    if (showModal){
        $('#modalAdPreview').empty()
        $('#modalAdPreview').append(htmlToAppend)
        $('#modalPreview').modal('show')
    }
    return htmlToAppend;
}

const validateNeedStates = () => {
    var fileUpload = document.getElementById("needstatesFile");
    var regex = /^([a-zA-Z0-9\s_\\.\-+:])+(.csv)$/;
    if (regex.test(fileUpload.value.toLowerCase())) {
        if (typeof (FileReader) != "undefined") {
            var reader = new FileReader();
            reader.onload = function (e) {
                var rows = e.target.result.split("\n");
                for (var i = 0; i < rows.length; i++) {
                    var cells = rows[i].split("|");
                    if (cells.length > 1) { 
                        if (i==2 && cells[1] !== 'Need State') {
                            newAd.needstateValidated = false;
                            newAd.needstateData = [];
                            validateFile('needstate',false);
                            alert("Invalid Csv format");
                            return false;
                        }
                        if (i >= 3) {
                            newAd.needstateData.push({needstate: cells[1], value: cells[2]})
                        }
                    }
                }
            }
            reader.readAsText(fileUpload.files[0]);
        } else {
            alert("This browser does not support HTML5.");
            return false;
        }
    } else {
        alert("Please upload a valid CSV file.");
        newAd.needstateValidated = false;
        newAd.needstateData = [];
        validateFile('needstate',false);
        return false;
    }
    newAd.needstateValidated = true;
    validateFile('needstate',true);
    return true;
}


const validateMotivation = () => {
    var fileUpload = document.getElementById("motivationFile");
    var regex = /^([a-zA-Z0-9\s_\\.\-+:])+(.csv)$/;
    if (regex.test(fileUpload.value.toLowerCase())) {
        if (typeof (FileReader) != "undefined") {
            var reader = new FileReader();
            reader.onload = function (e) {
                var rows = e.target.result.split("\n");
                let validHeaders = false;
                newAd.motivationData = [];
                var headerName = "MOTIVATIONAL"
                for (var i = 0; i < rows.length; i++) {
                    var cells = rows[i].split("|");
                   
                    if (cells.length > 1) { 
                        if (i <= 5 && validHeaders==false) {
                            validHeaders = cells[1].toUpperCase().indexOf(headerName) >= 0 
                        }
                        if (cells[2].indexOf('%') >= 0 && parseFloat(cells[2].replace('%', '')) >= 0 ) {
                            newAd.motivationData.push({name: cells[1], value: cells[2]})
                        }
                    }
                    if(i == 5  && validHeaders === false) { 
                        newAd.motivationValidated = false;
                        newAd.motivationData = [];
                        validateFile('motivation',false);
                        alert("Invalid Csv format");
                        return false;
                    }
                }
            }
            reader.readAsText(fileUpload.files[0]);
        } else {
            alert("This browser does not support HTML5.");
            return false;
        }
    } else {
        alert("Please upload a valid CSV file.");
        newAd.motivationValidated = false;
        newAd.motivationData = [];
        validateFile('motivation',false);
        return false;
    }
    newAd.motivationValidated = true;
    validateFile('motivation',true);
    return true;
}

const validateDayparts = () => {
    var fileUpload = document.getElementById("daypartsFile");
    var regex = /^([a-zA-Z0-9\s_\\.\-+:])+(.csv)$/;
    if (regex.test(fileUpload.value.toLowerCase())) {
        if (typeof (FileReader) != "undefined") {
            var reader = new FileReader();
            reader.onload = function (e) {
                var rows = e.target.result.split("\n");
                for (var i = 0; i < rows.length; i++) {
                    var cells = rows[i].split("|");
                    if (cells.length > 1) { 
                        if (i==2 && (cells[2] !== 'Network Name' || cells[3] !== 'Dayparts Name' ) ){
                            newAd.daypartValidated = false;
                            newAd.daypartData = [];
                            validateFile('daypart',false);
                            alert("Invalid Csv format");
                            return false;
                        }
                        if (i >= 3) {
                            newAd.daypartData.push({
                                network: cells[2], 
                                daypart: cells[3],
                                value: cells[4]
                            })
                        }
                    }
                }
            }
            reader.readAsText(fileUpload.files[0]);
        } else {
            alert("This browser does not support HTML5.");
            return false;
        }
    } else {
        alert("Please upload a valid CSV file.");
        newAd.daypartValidated = false;
        newAd.daypartData = [];
        validateFile('daypart',false);
        return false;
    }
    newAd.daypartValidated = true;
    validateFile('daypart',true);
    return true;
}


const validateLift = () => {
    newAd.liftData={
            'inc_5' : 0,
            'inc_10' : 0,
            'inc_20' : 0,
            'inc_avr_5' : 0,
            'inc_avr_10' : 0,
            'inc_avr_20' : 0
    };
    var fileUpload = document.getElementById("liftFile");
    var regex = /^([a-zA-Z0-9\s_\\.\-+:])+(.csv)$/;
    if (regex.test(fileUpload.value.toLowerCase())) {
        if (typeof (FileReader) != "undefined") {
            var reader = new FileReader();
            reader.onload = function (e) {
                var rows = e.target.result.split("\n");
                let atleastone = false;
                for (var i = 0; i < rows.length; i++) {
                    var cells = rows[i].split("|");
                 
                    if (cells.length > 1) { 
                        atleastone= true;
                        if (i==1 && 
                              !cells[0].includes('PREDICTED CONTEXT')  
                        ){
                            newAd.liftValidated = false;
                            newAd.liftData={
                                'inc_5' : 0,
                                'inc_10' : 0,
                                'inc_20' : 0,
                                'inc_avr_5' : 0,
                                'inc_avr_10' : 0,
                                'inc_avr_20' : 0
                            };
                            validateFile('lift',false);
                            alert("Invalid Csv format");
                            return false;
                        }

                         if (i==4){
                            newAd.liftData.inc_5 = cells[1].replace('\r', '');
                            newAd.liftData.inc_avr_5 = cells[3].replace('\r', '');
                        }
                        if (i==5){
                            newAd.liftData.inc_10 = cells[1].replace('\r', '');
                            newAd.liftData.inc_avr_10 = cells[3].replace('\r', '');
                        }
                        if (i==6){
                            newAd.liftData.inc_20 = cells[1].replace('\r', '');
                            newAd.liftData.inc_avr_20 = cells[3].replace('\r', '');
                        }
                    }
                }
                console.log(newAd.liftData)
                if(!atleastone){
                    alert("Please upload a valid CSV file.");
                    newAd.liftValidated = false;
                    newAd.liftData = [];
                    validateFile('lift',false);
                    return false;
                }
            }
            reader.readAsText(fileUpload.files[0]);
        } else {
            alert("This browser does not support HTML5.");
            return false;
        }
    } else {
        alert("Please upload a valid CSV file.");
        newAd.liftValidated = false;
        newAd.liftData = [];
        validateFile('lift',false);
        return false;
    }

    newAd.liftValidated = true;
    validateFile('lift',true);
    return true;
}

const validateResonance = () => {
    var fileUpload = document.getElementById("resonanceFile");
    var regex = /^([a-zA-Z0-9\s_\\.\-+:])+(.csv)$/;
    if (regex.test(fileUpload.value.toLowerCase())) {
        if (typeof (FileReader) != "undefined") {
            var reader = new FileReader();
            reader.onload = function (e) {
                var rows = e.target.result.split("\n");
                for (var i = 0; i < rows.length; i++) {
                    var cells = rows[i].split("|");
                    if (cells.length > 1) { 
                        if (i==7 && 
                            (  cells[0] !== 'UniqueID' 
                            || cells[1] !== 'Program Title ' 
                            || cells[2] !== 'Network' 
                            || cells[3] !== 'Network Type') 
                        ){
                            newAd.resonanceValidated = false;
                            newAd.resonanceData = [];
                            validateFile('resonance',false);
                            alert("Invalid Csv format");
                            return false;
                        }
                        if (i == 7) {
                            newAd.resonanceData.push(cells)
                        }
                        if (i >= 12 && parseInt(cells[0]) > 0 ) {
                            newAd.resonanceData.push(cells)
                        }
                    }
                }
            }
            reader.readAsText(fileUpload.files[0]);
        } else {
            alert("This browser does not support HTML5.");
            return false;
        }
    } else {
        alert("Please upload a valid CSV file.");
        newAd.resonanceValidated = false;
        newAd.resonanceData = [];
        validateFile('resonance',false);
        return false;
    }
    newAd.resonanceValidated = true;
    validateFile('resonance',true);
    return true;
}


/***
 * auxiliar functions
 */

const flashCampaignMessage = (type, message) => {
    let target = '';
    if (type =='success') {
        target = '#campaignMessageSuccess';
    }else{
        target = '#campaignMessageDanger';
    }
    $(target).text(message);
    $(target).fadeIn(600);
    setTimeout(() => {
        $(target).fadeOut(1200);
    }, 5000);
}    


const validURL = (str) => {
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
      '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
      '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
      '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
      '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
      '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
    return !!pattern.test(str);
}


const buildImagePreview = (url) => {
return `<div style="background-image: url('` + url + `');
        background-position: top;
        background-repeat: no-repeat;
        background-size: contain;
        height: 15em;
        width: 100%;"></div>`;
}

const validateFile = (type, isValid) => {
    switch (type) {
        case 'needstate':
            if (isValid) {
                $('#needstateValid').fadeIn(600);
                $('#needstateInvalid').hide();
            } else {
                $('#needstateInvalid').fadeIn(600);
                $('#needstateValid').hide();
            }
            break;   
        case 'motivation':
            if (isValid) {
                $('#motivationValid').fadeIn(600);
                $('#motivationInvalid').hide();
            } else {
                $('#motivationInvalid').fadeIn(600);
                $('#motivationValid').hide();
            }
            break;   
        case 'daypart':
            if (isValid) {
                $('#daypartsValid').fadeIn(600);
                $('#daypartsInvalid').hide();
            } else {
                $('#daypartsInvalid').fadeIn(600);
                $('#daypartsValid').hide();
            }
            break;        
        case 'resonance':
            if (isValid) {
                $('#resonanceValid').fadeIn(600);
                $('#resonanceInvalid').hide();
            } else {
                $('#resonanceInvalid').fadeIn(600);
                $('#resonanceValid').hide();
            }
            break;
        case 'lift':
            if (isValid) {
                $('#liftValid').fadeIn(600);
                $('#liftInvalid').hide();
            } else {
                $('#liftInvalid').fadeIn(600);
                $('#liftValid').hide();
            }
            break;
        default:
            break;
    }

}


const validateNewAd = () => {
    newAd.adName =  $('#adName').val();
    newAd.campaingId = campaign.id;

    showPreview(false);

    let valid = true; 
    if (newAd.name == '') {
        valid = false; 
        $('#adNameEmpty').fadeIn(600);
    } else {
        $('#adNameEmpty').fadeOut(600);
    }
    if (newAd.needstateValidated) {
        validateFile('needstate', true)
    } else {
        valid = false; 
        validateFile('needstate', false)
    }
    if (newAd.daypartValidated) {
        validateFile('daypart', true)
    } else {
        validateFile('daypart', false)
        valid = false; 
    }
    if (newAd.resonanceValidated) {
        validateFile('resonance', true)
    } else {
        valid = false; 
        validateFile('resonance', false)
    }

    if (newAd.liftValidated) {
        validateFile('lift', true)
    } else {
        valid = false; 
        validateFile('lift', false)
    }

    return valid;

}


const blankAdForm = () => {
    $('#adName').val('');
    $('#adPreview').val('');
    $('#needstatesFile').val('');
    $('#motivationFile').val('');
    $('#daypartsFile').val('');
    $('#resonanceFile').val('');
    $('#needstateValid').hide();
    $('#needstateInvalid').hide();
    $('#daypartsValid').hide();
    $('#daypartsInvalid').hide();
    $('#resonanceInvalid').hide();
    $('#resonanceValid').hide();
    $('#liftInvalid').hide();
    $('#liftValid').hide();
    $('#motivationValid').hide();
    $('#motivationInvalid').hide();

    newAd = {
        adName:'',
        adPreview:'',
        campaingId:0,
        needstateData: [],
        motivationData: [],
        daypartData: [],
        resonanceData: [],
        liftData: {
            'inc_5' : 0,
            'inc_10' : 0,
            'inc_20' : 0,
            'inc_avr_5' : 0,
            'inc_avr_10' : 0,
            'inc_avr_20' : 0
        },
        needstateValidated: false,
        daypartValidated: false,
        motivationValidated: false,
        resonanceValidated: false,
        liftValidated: false
    };
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


//AUXILIAR FUNCTIONS

const FillSelector = (selector, options, selectAll) => {
    $(selector).empty();
    $(selector).append( new Option('None', ''));
    for (const option of options) {
        let newOption = new Option(option.value, option.id);
        $(selector).append(newOption);
    }
    $(selector).val(campaignEdition.advertiser_id).trigger('change');
}

