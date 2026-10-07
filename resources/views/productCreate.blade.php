@extends('layouts.app')

@section('content')
@include('modals/categoryModal')


<div class="container-flux m-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Production Items') }}</div>
                <!-- <div class="container"> -->
                <div class='container-fluid form-group'>
                    <br>
                    <form name="add_item" id="add_item">

                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class='table table-bordered table-hover' id="tab_logic">
                                        <thead>
                                            <tr class='info'>
                                                <th style='width:7%;'>SL</th>

                                                <th style='width:15%;'>Category&emsp;<a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#addParent"><i class="fa fa-plus-circle"
                                                            style="font-size:24px;color:green"></i></a></th>
                                                <th style='width:15%;'>Model&emsp;<a href="#" id="addSubCat1Modal"><i
                                                            class="fa fa-plus-circle"
                                                            style="font-size:24px;color:green"></i></a></th>
                                                <th style='width:15%;'>Qty</th>
                                                <th style='width:35%;'>Note</th>
                                                <th style='width:20%;'>Technician</th>
                                                <th style='width:10%;'>Action</th>
                                            </tr>
                                        </thead>
                                        <thead>
                                            <tr id="addr0">
                                                <td style="text-align: center" class="custom-tbl">
                                                    <input class='form-control input-sm' style='width:100%;' type="text"
                                                        value="1" id="pr_item0" name="pr_item[]" readonly required>
                                                </td>


                                                <!-- onChange="return categorym('1')" -->
                                                <td class="custom-tbl">
                                                    <select class="browser-default custom-select categorym categorym1"
                                                        data-id="1" name="category[]" style='width:100%;' id=""
                                                        required>
                                                        <option value='' selected disabled>Select category</option>
                                                        @foreach ($categoris as $item)
                                                        <option value='{{ $item->id }}'>{{ $item->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <!-- onChange="(sub1call($(this).val()))" -->
                                                <td class="custom-tbl">
                                                    <select class='browser-default custom-select product1'
                                                        style='width:100%;' type="text" id="product1" name="product[]"
                                                        required></select>
                                                </td>

                                                <td class="custom-tbl"><input class='qtyValidate1 form-control input-sm'
                                                        style='width:100%;' type="text" id="qty0" name="qty[]"
                                                        onkeypress="return /[0-9]/i.test(event.key)" required>
                                                </td>

                                                <td class="custom-tbl">
                                                    <input class='form-control input-sm' style='width:100%;' type="text"
                                                        id="pr_desc0" name="pr_desc[]">
                                                </td>

                                                <td class="custom-tbl">
                                                    <select class="browser-default custom-select" name="assignUserId"
                                                        id="assignUserId" required>
                                                        <option selected disabled>Select Technician</option>
                                                        @foreach ($users as $item)
                                                        <option value='{{ $item->id }}'>{{ $item->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <td style="text-align: center;" class="custom-tbl">
                                                    <button type="button" id="add" class="btn btn-info btn-sm btn_add">
                                                        <span class="fa fa-plus"></span></button></td>
                                            </tr>
                                        </thead>
                                        <tbody id="dynamic_field">
                                        <tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 form-group">
                                    <div class="modal-footer">
                                        <div class="pull-right">
                                            <button type="button" id="submit" name="submit"
                                                class="btn btn-success btn_submit">Create</button>
                                            <button type="button" class="btn btn-secondary"
                                                onclick="resetFormAndReload()">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- https://stackoverflow.com/questions/59814416/how-to-prevent-the-first-row-of-a-table-with-two-dropdown-from-dynamically-chang -->

<!-- Add button function -->
<script type="text/javascript">
function resetFormAndReload() {
    const form = document.getElementById('add_item');
    if (form) form.reset(); // Reset all form fields
    // Clear select dropdowns manually if needed
    const selects = form.querySelectorAll('select');
    selects.forEach(select => select.selectedIndex = 0); // Or null if using placeholders
    // Reload the page
    window.location.reload();
}



$(document).ready(function() {
    var postURL = "<?php echo url('addmore'); ?>";
    var i = 1;
    $('#add').click(function() {
        i++;
        $('#dynamic_field').append('<tr id="row' + i + '" class="dynamic-added">' +
            '<td class="custom-tbl"><input id="pr_item' + i +
            '" class="form-control input-sm"style="width:100%;" type="text" value="' + i +
            '" name="pr_item[]" readonly required></td>' +

            '<td class="custom-tbl"><select class="browser-default custom-select categorym categorym' +
            i + '" data-id="' + i + '" name="category[]" id="" required>' +
            "<option selected>Select category</option>" +
            "@foreach ($categoris as $item)" +
            "<option value='{{ $item->id }}'>{{ $item->name }}</option>" +
            "@endforeach" +
            '</select></td>' +
            '<td class="custom-tbl"><select class="browser-default custom-select product' + i +
            '" onChange="return sub1call(' +
            i + ')" name="product[]" id="product' + i + '" required></select></td>' +
            '<td class="custom-tbl"><input id="qty' + i +
            '"class="qtyValidate' + i +
            ' form-control input-sm" style="width:100%;" type="text" name="qty[]" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)"></td>' +
            '<td class="custom-tbl"><input id="pr_desc' + i +
            '" class="form-control input-sm" style="width:100%;" type="text" name="pr_desc[]"></td>' +

            '<td class="custom-tbl">' +
            '<select class="browser-default custom-select" name="assignUserId' + i +
            '" id="assignUserId' + i + '" required>' +
            '<option selected>Select Technician</option>' +
            "@foreach ($users as $item)" +
            "<option value='{{ $item->id }}'>{{ $item->name }} </option>" +
            "@endforeach" +
            '</select></td>' +

            '<td style="text-align: center;" class="custom-tbl"><button type="button" name="remove" id="' + i +
            '" class="btn btn-danger btn-sm btn_remove">' +
            '<span class="fa fa-minus"></span></button></td>' +
            '</tr>');
        // i++;

        $('.case' + i).select2({});
        $('.categorym' + i).select2({});
        $('.product' + i).select2({});




    });
    // onChange="return sub1call('+i+')"
    $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr("id");
        $('#row' + button_id + '').remove();
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on('click', '#submit', function() {
        $('.btn_submit').attr('disabled', true);
        // Validate required fields
        let isValid = true;
        $('#add_item').find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).css('border', '1px solid red'); // Optional: highlight empty field
            } else {
                $(this).css('border', ''); // Remove highlight
            }
        });

        if (!isValid) {
            alert('Please fill all required fields.');
            $('.btn_submit').attr('disabled', false); // Re-enable the button
            return;
        }

        $.ajax({
            url: "{{ route('submitCreateProducts') }}",
            method: "POST",
            data: $('#add_item').serialize(),
            type: 'json',
            success: function(response) {
                console.log(response);
                if (response.success == 'done') {
                    alert("Ticket Created successfully!");
                    $('#add_item')[0].reset();
                    $('select').val('')
                    window.location.reload();
                }
                if (response.error) {
                    alert(JSON.stringify(response.error));
                }
            },
            error: function(response) {
                alert(JSON.parse(response.responseText).message);
                // alert(response.message);
            },
        }, );
    });
});





// <!-- Script for dropdown menu -->

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


// $(document).ready(function(){

$(document).on('change', ".categorym", function(e) {

    var cat_id = $(this).val();
    var key = $(this).data('id');

    // alert(cat_id);
    $.ajax({
        url: "{{ route('subcat') }}",
        type: "POST",
        data: {
            cat_id: cat_id
        },
        success: function(data) {
            // var $SubC = $('#product' + j).empty();
            // $('.product'+keyValue).empty().append();            
            // $('.product' + key).empty().append($("<option  selected disabled></option>").attr("value", '').text(
            //     'Please Select'));
            // $.each(data.subcategories[0].subcategories, function(index, product) {
            //        $('.product' + key).append('<option value="' + product.id + '">' +
            //         product.name +
            //         '</option>');
            // })


            // Reset the <select>
                const $select = $('.product' + key)
                .empty()
                .append($('<option>', { value: '', selected: true, disabled: true }).text('Please Select'));

                // Add the options
                $.each(data.subcategories[0].subcategories, function (_, product) {
                $select.append(
                    $('<option>', {
                    value: product.id,
                    text:  product.name,
                    // keep the model number with the option itself
                    'data-model-number': product.model_number || ''
                    })
                );
                });

                $select.off('change.modelCheck')            // avoid duplicate handlers if this block reruns
       .on('change.modelCheck', function () {
         const modelNo = $(this).find(':selected').data('model-number');
         if (!modelNo) {
           alert('The selected product has no model number!, please update model number at category menu. Thank you.!');
           // optional: automatically revert to the placeholder
           $(this).val('');   // puts “Please Select” back
           window.location.href = "{{ route('category')}}";
         }
       });
        }
    })

});

var j = 1;
var k = 1;

function sub1call(cid) {
    var cat_id = cid;
    $.ajax({
        url: "{{ route('subcat') }}",
        type: "POST",
        data: {
            cat_id: cat_id
        },
        success: function(data) {
            var $SubC1 = $('#sub2category' + j).empty();
            $SubC1.append($("<option></option>").attr("value", '').text('Please Select'));
            $.each(data.subcategories[0].subcategories, function(index, sub2category) {
                $('#sub2category' + j).append('<option value="' + sub2category.id + '">' +
                    sub2category.name +
                    '</option>');
            })
            j++;
            k++;
        }
    })
};

var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
$(document).ready(function() {

    $('#assignUserId').select2({});
    $('.case').select2({});
    $('.categorym').select2({});
    $('.product1').select2({});

});
</script>
@endsection