
<!-- Image Preview Modal -->
<div id="proInfoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="previewTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center appendProInfo" style="overflow-x: auto;">
 


            </div>
            
        </div>
    </div>
</div>

<script>
function  proInfo(id){

    
    $('#id').val(id);                                           // stash for uploads

    $.post({
        url: "{{ route('getProductInfo') }}",
        data: { _token: CSRF_TOKEN, id },
        dataType: 'json',
        success: function (res) {
            // console.log(res[0].goe_location.latitude);
            if (!res[0].success) { return alert('Invalid ID'); }

           
            const p = res[0].prodcutInfo[0];                               // shorter alias
            // console.log(p);
            const productInfo = {
                proId      : p.productId,
                name       : p.category +' - '+ p.model,
                brand      : p.serial_no,
                votes      : p.votes         ?? 0,
                rating     : p.rating        ?? 0,
                price      : parseFloat(p.price),
                currency   : p.currency      ?? '₹',
                taxNote    : p.tax_note      ?? '',
               
                delivery   : p.delivery_time ?? '',
                certified  : p.certified     ?? '',
                description: p.note   ?? '',
                specs      : (p.specs ?? '').split('|'),
                reviews    : p.reviews       ?? []               // add if you join reviews
            };
 
            // alert(p.vendor_name)
            var address ='No data';
       
            var address = `
                    <table border="1" cellspacing="0" cellpadding="6">
                    <tr><th colspan="2"><h2>Job Card</h2></th></tr>
                    <tr><td>Job Card No</td><td>${p.productId ?? ''}</td></tr>
                    <tr><td>Job Date</td><td>${p.created_at ?? ''}</td></tr>
                    <tr><td>Sales Person</td><td>${p.vendor_name ?? ''}</td></tr>
                    <tr><td>Customer Name</td><td>${p.vendor_name ?? ''}</td></tr>
                    <tr><td>Phone Number</td><td>${p.vendor_mobile1 ?? ''},${p.vendor_mobile2 ?? ''}</td></tr>
                    <tr><td>Place</td><td>${p.vendor_address ?? ''}</td></tr>
                    <tr><td>Master Controller / Serial No</td><td>${p.serial_no ?? ''}</td></tr>
                    <tr><td>IMEI</td><td>NA</td></tr>
                    <tr><td>Module Type</td><td>${p.model ?? ''}</td></tr>
                    <tr><td>SIM Scope</td><td>NA</td></tr>
                    <tr><td>Program Folder</td><td>1</td></tr>
                    <tr><td>Wireless Frequency</td><td>NA</td></tr>
                    <tr><td>Wireless Receiver Controller</td><td>NA</td></tr>
                    <tr><th colspan="2">Given Materials</th></tr>
                    <tr><td>Valve Size</td><td style="color:red">2</td></tr>
                    <tr><td>Valve Quantity</td><td style="color:red">2</td></tr>
                    <tr><td>Valve Fittings</td><td style="color:red">3 Joints</td></tr>
                    <tr><td>Extra Materials (Such as Wire, etc)</td><td style="color:red">-</td></tr>
                    <tr><th colspan="2">Installation</th></tr>
                    <tr><td>Power Source</td><td style="color:red">Transformer – 1 (R&amp;D)</td></tr>
                    <tr><td>High Gain Antenna</td><td>-</td></tr>
                    <tr><td>No. of Actuator Installed with Valve Size</td><td style="color:red">3 Nos Actuator – 2”</td></tr>
                    <tr><td>Panel (Existing/New) with Description</td><td style="color:red">30 Double MCB + Fittings – 1</td></tr>
                    <tr><td>Extra Features (Float, Flow Meter, Level Sensor, etc) with Quantity</td><td style="color:red">Float – 1</td></tr>
                    <tr><td>Customised</td><td >${p.note ?? ''}</td></tr>
                    <tr><td>Installation Done By</td><td style="color:red">1. Elanson N <br> 2. Deepak Kumar</td></tr>
                    <tr><td>Status</td><td style="color:red">Billed / Closed</td></tr>
                    </table>
                    `;
                 
                $('.appendProInfo').html(address);/// html part replacement append
                $('#proInfoModal').modal('show');
        },
        error: () => alert('Server error, please retry')
    });
}
</script>
