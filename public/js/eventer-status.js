var params = {
  id: '01',
  groupBy: 'ssp'
};

$(document).ready(function() {
  $('.eventers')
    .select2({
      width: 'resolve',
      minimumResultsForSearch: Infinity,
    })
    .on('select2:select', function(e) {
      params.id = $('.eventers').select2('data')[0].id;
      getData(params);
    });

  $('.agrupadores')
    .select2({
      multiple: true,
    })
    .on('change', function(e) {
      params.groupBy = $('.agrupadores')
        .select2('data')
        .map((d) => d.id).join(',');
      getData(params);
    });
});

async function getData(params) {
  $('table').addClass('loading');
  $.ajax({
    type: 'get',
    dataType: 'JSON',
    url: '/admin/eventerstatus_v1.0',
    data: params,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    success: function(response) {
      $('table').removeClass('loading');
      populateTable(response);
    },
    error: function(qXHR, textStatus, errorThrown) {
      console.log('AJAX error: ' + textStatus + ' : ' + errorThrown);
      $('table').removeClass('loading');
      alert('Failed to get information. Please try again.');
    },
  });
}

function populateTable(data) {
  var r = new Array();
  var j = -1;
  var i = 1;
  r[++j] =
    '<thead><tr><th scope="col"></th><th scope="col">SSP</th><th scope="col">0</th><th scope="col">1</th><th scope="col">2</th><th scope="col">3</th><th scope="col">4</th><th scope="col">5</th></tr></thead><tbody';
  for (const [key, value] of Object.entries(data)) {
    r[++j] = '<tr><td scope="row">';
    r[++j] = i
    r[++j] = '</td><th scope="row">';
    r[++j] = key;
    r[++j] = '</th>';
    for (let i = 0; i <= 5; i++) {
      r[++j] = '<td scope="row" class="text-right">';
      r[++j] = value[i];
      r[++j] = '</td>';
    }
    i++;
  }
  r[++j] = '</td></tr></tbody>';

  $('#results-table').html(r.join(''));
}
