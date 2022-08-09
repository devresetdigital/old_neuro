<script>
@if(Auth::user()->role->name=="Datapixels")
    document.location.href="/admin/pixel_analytics";
@else
    document.location.href="/admin/reports";
@endif
</script>