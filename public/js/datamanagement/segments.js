let segmentsData=[];
let currentIndex=0;
let pagesAmount=0;
let pagination=25;

$('document').ready(function () {
    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    $('#search-segments').keyup(function (e) {
        setTimeout(() => {
            loadSegment();
        }, 1000);
    });
    
    $(".tabs-segments").click(function (e) {
        setTimeout(() => {
            loadSegment();
        }, 500);
        
    });

    $(".segments_filters").click(function (e) {
        setTimeout(() => {
            loadSegment();
        }, 500);
    });
   

    const loadSegment = async () => {
        $('#tbody-dmp').empty();
        $('#loading').show();
        
        const tab = $('.tabs-segments.active').data('tab-name');
        const search = $('#search-segments').val();
        
        $('#segmentsPagination').hide();
        
    
        url = '/api/dmpinfo?dmp='+tab+'&name='+search+'&id='+search;
    
        await $.get(url, function (res) {
            segmentsData = [];
            for (const id in res.data) {
                
                if(res.data[id].ANDROID != undefined && $('#segments_target_1').prop("checked")){
                    segmentsData.push({
                    id: id+'-ANDROID',
                    type: 'Android',
                    name: res.data[id].name,
                    reach: res.data[id].ANDROID,
                    price: res.data[id].price,
                    });
                }
                if(res.data[id].IOS != undefined && $('#segments_target_2').prop("checked")){
                    segmentsData.push({
                    id: id+'-IOS',
                    type: 'Ios',
                    name: res.data[id].name,
                    reach: res.data[id].IOS,
                    price: res.data[id].price,
                    });
                }
                if(res.data[id].IP != undefined && $('#segments_target_3').prop("checked")){
                    segmentsData.push({
                    id: id+'-IP',
                    type: 'Ip',
                    name: res.data[id].name,
                    reach: res.data[id].IP,
                    price: res.data[id].price,
                    });
                }
                if(res.data[id].COOKIE != undefined && $('#segments_target_4').prop("checked")){
                    segmentsData.push({
                    id: id+'-COOKIE',
                    type: 'Cookie',
                    name: res.data[id].name,
                    reach: res.data[id].COOKIE,
                    price: res.data[id].price,
                    });
                }
            }    
        });
       
        currentIndex=0;
        fillSegments(currentIndex);
    }


    const fillSegments = (page) => {

        $('#tbody-dmp').empty();

        let resultsAmount = Object.keys(segmentsData).length;

        pagesAmount = Math.ceil(resultsAmount/pagination);

        if(pagesAmount > 1){
            $('#paginationContainer').empty();
            $('#segmentsPagination').show();
            let paginationHtml = `
                    <li class="paginate_button previous" id="dataTable_prev">
                        <a href="#" aria-controls="dataTable" data-index="prev" tabindex="0">Previous</a>
                    </li>`;
            paginationHtml += `
                    <li id="pageNumber0" class="paginate_button"><a href="#" class="pageNumberButton" aria-controls="dataTable" data-index="0" tabindex="0">1</a>
                    </li>
            `;

            for (let index = 1; index < pagesAmount-1; index++) {
                if(index >= (currentIndex - 4 ) && index <= (currentIndex +4 ) ){
                    paginationHtml += `
                    <li id="pageNumber${index}" class="paginate_button ${index==0 ? 'active': ''}"><a href="#" class="pageNumberButton" aria-controls="dataTable" data-index="${index}" tabindex="${index}">${index+1}</a>
                    </li>
                `;
                }
            
            }                    
            paginationHtml += `
                    <li id="pageNumber${pagesAmount-1}" class="paginate_button"><a href="#" class="pageNumberButton" aria-controls="dataTable" data-index="${pagesAmount-1}" tabindex="${pagesAmount-1}">${pagesAmount}</a>
                    </li>
            `;
            paginationHtml += `
                <li class="paginate_button next" id="dataTable_next">
                    <a href="#" aria-controls="dataTable"  data-index="next" tabindex="0">Next</a>
                </li>
            `;
            $('#paginationContainer').append(paginationHtml);
        }



        $(".pageNumberButton").click(function (e) {
            e.preventDefault();
            let page = $(this).data('index');
            currentIndex = page;
            fillSegments(page);
            return false;
        });

        $("#dataTable_next").click(function (e) {
            e.preventDefault();
            currentIndex++;
            fillSegments(currentIndex);
            return false;
        });

        $("#dataTable_prev").click(function (e) {
            e.preventDefault();
            currentIndex--;
            fillSegments(currentIndex);
            return false;
        });

        if(page < 0 ){currentIndex = 0; page = 0;}
        if(page >= pagesAmount){ currentIndex =pagesAmount - 1; page = pagesAmount - 1; }
        currentIndex=page;

        $('.paginate_button').removeClass('active');
        $('#pageNumber'+page).addClass('active');


        let input_audience = $('#audiences_selection').val().replace(' ','').split(',');   
        
        
        let html = '';

        let count=0;

        for (const segment of segmentsData) {
            if(count < (page*pagination)){ count++; continue;}
            if(count >= (page*pagination)+pagination ){break;}

            let included = false;
            if(input_audience.includes(segment.id)){
                included = true;
            }
            html+=`<tr  data-price="${segment.price}">
                    <td style="padding-left: 0.5em;" scope="row">${segment.id}</td>
                    <td style="padding-left: 0.5em;">${segment.name}</td>
                    <td style="padding-left: 0.5em;">${segment.type}</td>
                    <td class="text-right" style="padding-right: 0.5em;">${segment.reach}</td>
                    <td class="text-right" style="padding-right: 0.5em;">$ ${parseFloat(segment.price).toFixed(2)}</td>
                    <td class="text-center">
                        <button ${included ? 'style="display: none;"' : ''  }  id="${segment.id}-add" data-id="${segment.id}" data-name="${segment.name}" data-reach="${segment.reach}" data-price="${segment.price}" data-type="${segment.type}" class="btn btn-small btn-success add_audiece">+</button>
                        <button ${!included ?'style="display: none;"' : ''  }  id="${segment.id}-remove" data-audience-id="${segment.id}" data-price="${segment.price}" class="btn btn-small btn-warning remove_audiece"  >-</button>
                    </td>
                </tr>
            `;
            count++;
        }

        $('#tbody-dmp').append(html);

     
        $('#loading').hide();
        

        $( ".add_audiece").click(function(event) {
            event.preventDefault();
            let audience ={
                id: $(this).data('id'),
                name: $(this).data('name'),
                reach: $(this).data('reach'),
                price: $(this).data('price'),
                type: $(this).data('type') 
            }
    
            if(!$('#selected-row-'+ audience.id).length){

                let input_audience = $('#audiences_selection').val().replace(' ','').split(',');   
    
                if(!input_audience.includes(audience.id)){
                    input_audience.push(audience.id);
                }
                const index = input_audience.indexOf("");
                    if (index > -1) {
                        input_audience.splice(index, 1);
                }

                $('#'+audience.id+'-add').hide();
                $('#'+audience.id+'-remove').show();

                $('#audiences_selection').val(input_audience.join());

                let price = parseFloat($(this).data('price'));
                let audiences_cpm =  parseFloat($('#audiences_cpm').val());
                $('#audiences_cpm').val(audiences_cpm + price);


                let newTr =  `<tr id="selected-row-${audience.id}" data-price="${audience.price}">
                                <td style="padding-left: 0.5em;" scope="row">${audience.id}</td>
                                <td style="padding-left: 0.5em;">${audience.name}</td>
                                <td style="padding-left: 0.5em;" >${audience.type}</td>
                                <td class="text-right" style="padding-right: 0.5em;">${audience.reach}</td>   
                                <td class="text-right" style="padding-right: 0.5em;">$ ${parseFloat(audience.price).toFixed(2)}</td>
                                <td class="text-center"><button data-price="${audience.price}" data-audience-id="${audience.id}" class="btn btn-small btn-warning remove_audiece"  >-</button></td>
                            </tr>`;
                $('#tbodyAudiencesSelected').append(newTr);   


                $(".remove_audiece").click(function(event) {
                    event.preventDefault();
                    let audienceID = $(this).data('audience-id');
                    let price = parseFloat($(this).data('price'));

                    let audiences_cpm =  parseFloat($('#audiences_cpm').val());
                    if(audiences_cpm - price < 0){
                        $('#audiences_cpm').val(0);
                    }  else{
                        $('#audiences_cpm').val(audiences_cpm - price);
                    }

                    
                    let input_audience = $('#audiences_selection').val().split(',');   
                    $('#'+audienceID+'-add').show();
                    $('#'+audienceID+'-remove').hide();
                    const index = input_audience.indexOf(""+audienceID);
                    if (index > -1) {
                        input_audience.splice(index, 1);
                    }
                    $('#audiences_selection').val(input_audience.join());

                    $('#selected-row-' + audienceID).remove();
                    
                    return false;
                });
            }
            return false;
            
        });
    }

    loadSegment();

});