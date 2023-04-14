<script src="//code.jquery.com/jquery-3.5.1.js"></script>
<link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>




<table id="table_{{ $id }}" class="display" style="width:100%">
    <thead>
      <tr>
        <th>Url</th>
        <th>Correlation Score</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
let id = '<?php echo $id; ?>';

$(document).ready(function() {
    $("#table_" + id ).DataTable({
      processing: true,
      pageLength: 10,
      serverSide: true,
      deferRender: true,
      ajax: "/api/get_domains_by_item/" + id,
      columns: [{ data: "url" }, { data: "score" }],
    });
});



</script>