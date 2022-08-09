$(document).ready(function () {
  
    $('#organization').change(async function () {
        if($('#organization').val() == ''){
            $('.fileContainer').hide();
        }

       await updateAdvertisers();
    });

    $('#advertiser').change(async function () {
        if($('#advertiser').val() != ''){
            $('.fileContainer').show();
        }else{
            $('.fileContainer').hide();
        }
     });

     $('.custom-file-input').change(function(e){
        validateFile($(this).attr('id'));
    });

    $('#saveCampaign').click(function(e){
        saveCampaign();
    });

    $("#newCampaign").submit(function(e){
        e.preventDefault();
    });

    initView();
});

let submmited = false;

const validateFile = (id) => {
    var ext = $('#'+id).val().split('.').pop().toLowerCase();
    if($.inArray(ext, ['xlsx']) == -1) {
        alert("File extension must be .xlsx");
        $('#fileInvalid-'+ id).fadeIn(600);
        $('#fileValid-'+ id).hide();
        $('#' + id).val('');
    }else{
        $('#fileValid-'+ id).fadeIn(600);
        $('#fileInvalid-'+ id).hide();
    }
}

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
   
   } else {
        $('.fileContainer').hide();
   }
   blankForm();
}

let adIdtoDelete = 0;



const fillForm = () => {
    setTimeout(() => {
        $('#campaign_id').val(campaignEdition.campaign_id);
        $('#campaignName').val(campaign.name);
        $('#organization').val(campaignEdition.organization_id).trigger('change');
        $('#campaign').val(campaignEdition.campaign_id).trigger('change');
        $('#organization').prop("disabled", true);
        $('#campaign').prop("disabled", true);
        $('#advertiser').prop("disabled", true);
        $('#campaignName').prop("disabled", true);
    }, 500);
 
}

const saveCampaign = async () => {
  
    if(submmited==false){
        submmited=true;
        let response;
        // this is the id of the form
        await $("#newCampaign").submit(function(e) {
            e.preventDefault();
             // avoid to execute the actual submit of the form.
    
            var form = $(this);
            var url = '/api/linear/import';
            
            $.ajax({
                type: "POST",
                url: url,
                data: new FormData( this ),
                processData: false,
                contentType: false,
                success: function(data)
                {
                    response = data;
                    if (response.error) {
                        flashCampaignMessage('error',response.message);
             
                    } else {
                        flashCampaignMessage('success',response.message);
                        initView();
                    }
                }
                });
        });

    }
   
    submmited=false;

}   


const modalDeleteAd = (adId) => {
    adIdtoDelete = adId;
    $('#modalDelete').modal();
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



const blankForm = () => {
    $('#campaignName').val('');
    $('#file').val('');
    $('#fileValid').hide();
    $('#fileInvalid').hide();
    $('.fileContainer').hide();

    $('#organization').val(null);
    $('#advertiser').val(null);
    $('#campaign').val(null);

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

