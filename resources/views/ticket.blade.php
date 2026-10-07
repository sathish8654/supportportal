<x-app-layout>
  <x-slot name="header">
    <h2 class="mb-4">Create Ticket</h2>
  </x-slot>

  <Br>
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" /> -->
  <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

  <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
  <div class='container mt-1'>
<br>
    @include('modals/addCustomerAddress')

    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNewAddressModal">
      Add Address
    </button>
    <Br>
    <div class="col-md-3 form-group">
      <label class=" control-label" for="textinput">Check Mobile Number</label>
      <select class=" browser-default custom-select" name="categoryMobile1" id="categoryMobile1">
        <option selected>Select Number</option>
        @foreach ($mobileNumber as $item)
        <option value="{{ $item->id }}">{{ $item->mobile1 }}</option>
        @endforeach
      </select>
    </div>
    <Br>

    <!-- <div class="container"> -->

    <div class="d-flex justify-content-center">

      <form name="add_item" id="add_item">

        <div class="row">
          <div class="col-md-10 col-md-offset-4">
            {{ csrf_field() }}
            <fieldset>
              <!-- Text input-->
              <input id="ct_addr_id" name="ct_addr_id" type="hidden">

              <div class="form-group">
                <label class=" control-label" for="textinput">Name</label>
                <input type="text" placeholder="Contact Person Name" id="ct_name" class="typeaheadname form-control"
                  readonly>
              </div>
              <!-- Text input-->
              <div class="col-md-12 row form-group">
                <label class=" control-label" for="textinput">Mobile 1</label>
                <div class="col-sm-5">
                  <input type="text" placeholder="Mobile 1" id="ct_mobile1" name="ct_mobile1[]" class="form-control"
                    readonly>
                </div>

                <label class=" control-label" for="textinput">Mobile 2</label>
                <div class="col-sm-4">
                  <input type="text" placeholder="Mobile 2" id="ct_mobile2" name="ct_mobile2[]" class="form-control"
                    readonly>
                </div>
              </div>
              <!-- Text input-->
              <div class="form-group">
                <label class=" control-label" for="textinput">Company Name</label>
                <input type=" text" placeholder="Company Name" id="ct_companyname" class="typeaheadcompany form-control"
                  readonly>
              </div>
              <!-- Text input-->
              <div class="form-group">
                <label class=" control-label" for="textinput">Address</label>
                <input type=" text" placeholder="Company Full Address" id="ct_address" name="ct_address[]"
                  class="form-control" readonly>
              </div>
              <!-- Text input-->
              <div class="col-md-12 row form-group">
                <label class=" control-label" for="textinput">City</label>
                <div class="col-sm-5">
                  <input type="text" placeholder="City" id="ct_city" name="ct_city[]" class="form-control" readonly>
                </div>

                <label class=" control-label" for="textinput">District</label>
                <div class="col-sm-5">
                  <input type="text" placeholder="District" id="ct_district" name="ct_district[]" class="form-control"
                    readonly>
                </div>
              </div>

              <!-- Text input-->
              <div class="col-md-12 row form-group">
                <label class=" control-label" for="textinput">State</label>
                <div class="col-sm-5">
                  <input type="text" placeholder="State" id="ct_state" name="ct_state[]" class="form-control" readonly>
                </div>

                <label class=" control-label" for="textinput">Postcode</label>
                <div class="col-sm-4">
                  <input type="text" placeholder="Post Code" id="ct_pincode" name="ct_pincode[]" class="form-control"
                    readonly>
                </div>
              </div>

              <!-- Text input-->
              <div class="form-group">
                <label class=" control-label" for="textinput">GST Number</label>
                <input type="text" placeholder="GST number" id="ct_gstno" name="ct_gstno[]" class="form-control"
                  readonly>
              </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-body">

              <div class="table-responsive">
                <table class='table table-bordered table-hover' id="tab_logic">
                  <thead>
                    <tr class='info'>
                      <th style='width:10%;'>SL.NO.</th>
                      <th style='width:40%;'>Category</th>
                      <th style='width:40%;'>Sub1category</th>
                      <th style='width:40%;'>Sub2category</th>
                      <th style='width:40%;'>Serial</th>
                      <th style='width:40%;'>Note</th>
                      <th style='width:10%;'>Action</th>
                    </tr>
                  </thead>
                  <thead>
                    <tr id="addr0">
                      <td class="custom-tbl"><input class='form-control input-sm' style='width:100%;' type="text"
                          value="1" id="pr_item0" name="pr_item[]" readonly required></td>

                      <td class="custom-tbl">
                        <select class="browser-default custom-select" name="category[]" id="categorym">
                          <option selected>Select category</option>
                          @foreach ($categoris as $item)
                          <option value='{{ $item->id }}'>{{ $item->name }}</option>
                          @endforeach
                        </select></td>

                      <td class="custom-tbl"><select class='browser-default custom-select' style='width:100%;'
                          type="text" onChange="(sub1call($(this).val()))" id="sub1category1"
                          name="sub1category[]"></select></td>

                      <td class="custom-tbl"><select class='browser-default custom-select' style='width:100%;'
                          type="text" id="sub2category1" name="sub2category[]"></select></td>

                      <td class="custom-tbl"><input class='form-control input-sm' style='width:100%;' type="text"
                          id="pr_unit0" name="pr_unit[]"></td>

                      <td class="custom-tbl"><input class='form-control input-sm' style='width:100%;' type="text"
                          id="pr_desc0" name="pr_desc[]"></td>

                      <td class="custom-tbl"><button type="button" id="add" class="btn btn-info btn-sm"><span
                            class="glyphicon glyphicon-plus">+</span></button></td>
                    </tr>
                  </thead>
                  <tbody id="dynamic_field">
                  <tbody>
                </table>
              </div>

              <div class="col-md-12 form-group">
                <div class="modal-footer">
                  <div class="pull-right">
                    <button type="button" id="submit" name="submit" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                      onclick="javascript:window.location.reload()">Reset</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>

</x-app-layout>

<!-- Type search by name -->
<script type="text/javascript">
  var path = "{{ route('autocompleteaddress') }}";
  $('input.typeaheadname').typeahead({
    source: function(query, process) {
      return $.get(path, {
        queryname: query
      }, function(data) {
        $('#ct_addr_id').val(data.id);
        // $('#ct_name').val(data.name);
        $('#ct_companyname').val(data.companyname);
        companyname.disabled = true;
        $('#ct_mobile1').val(data.mobile1);
        mobile1.disabled = true;
        $('#ct_mobile2').val(data.mobile2);
        mobile2.disabled = true;
        $('#ct_address').val(data.address);
        address.disabled = true;
        $('#ct_city').val(data.city);
        city.disabled = true;
        $('#ct_district').val(data.district);
        district.disabled = true;
        $('#ct_state').val(data.state);
        state.disabled = true;
        $('#ct_gstno').val(data.gstno);
        gstno.disabled = true;
        $('#ct_pincode').val(data.pincode);
        pincode.disabled = true;
        // alert();
        return process(data);
      });
    }
  });
</script>

<!-- Type search by company name -->
<script type="text/javascript">
  var path = "{{ route('autocompleteaddress') }}";
  $('input.typeaheadcompany').typeahead({
    source: function(query, process) {
      return $.get(path, {
          querycompany: query
        },
        function(data) {
          $('#ct_addr_id').val(data.id);
          $('#ct_name').val(data.name);
          name.disabled = true;
          $('#ct_mobile1').val(data.mobile1);
          mobile1.disabled = true;
          $('#ct_mobile2').val(data.mobile2);
          mobile2.disabled = true;
          $('#ct_address').val(data.address);
          address.disabled = true;
          $('#ct_city').val(data.city);
          city.disabled = true;
          $('#ct_district').val(data.district);
          district.disabled = true;
          $('#ct_state').val(data.state);
          state.disabled = true;
          $('#ct_gstno').val(data.gstno);
          gstno.disabled = true;
          $('#ct_pincode').val(data.pincode);
          pincode.disabled = true;
          return process(data);
        });
    }
  });
</script>

<!-- https://stackoverflow.com/questions/59814416/how-to-prevent-the-first-row-of-a-table-with-two-dropdown-from-dynamically-chang -->

<!-- Add button function -->

<script type="text/javascript">
  $(document).ready(function() {
    var postURL = "<?php echo url('addmore'); ?>";
    var i = 1;
    $('#add').click(function() {
      i++;
      $('#dynamic_field').append('<tr id="row' + i + '" class="dynamic-added">' +
        '<td class="custom-tbl"><input id="pr_item' + i +
        '" class="form-control input-sm"style="width:100%;" type="text" value="' + i +
        '" name="pr_item[]" readonly required></td>' +
        '<td class="custom-tbl"><select class="browser-default custom-select" name="category[]" id="categorym">' +
        "<option selected>Select category</option>" +
        "@foreach ($categoris as $item)" +
        "<option value='{{ $item->id }}'>{{ $item->name }}</option>" +
        "@endforeach" +
        '</select></td>' +
        '<td class="custom-tbl"><select class="browser-default custom-select" onChange="return sub1call(' +
        i + ')" name="sub1category[]" id="sub1category' + i + '"></select></td>' +
        '<td class="custom-tbl"><select class="browser-default custom-select" name="sub2category[]" id="sub2category' +
        i + '"></select></td>' +
        '<td class="custom-tbl"><input id="pr_unit' + i +
        '"class="form-control input-sm" style="width:100%;" type="text" name="pr_unit[]"></td>' +
        '<td class="custom-tbl"><input id="pr_desc' + i +
        '" class="form-control input-sm" style="width:100%;" type="text" name="pr_desc[]"></td>' +
        '<td class="custom-tbl"><button type="button" name="remove" id="' + i +
        '" class="btn btn-danger btn-sm btn_remove">' +
        '<span class="glyphicon glyphicon-remove">-</span></button></td>' +
        '</tr>');
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
      let name = document.forms["add_item"]["ct_addr_id"].value;
      $.ajax({
        url: "{{ route('submitCreateTickets') }}",
        method: "POST",
        data: $('#add_item').serialize(),
        type: 'json',
        success: function(response) {
          if (response.success == 'done') {
            alert("Ticket Created successfully!");
            window.location.reload();
          }
          if (response.error) {
            alert(JSON.stringify(response.error));
          }
        },
      }, );
    });
  });
</script>

<!-- Script for dropdown menu -->

<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  var j = 1;
  $(document).ready(function() {
    // $('select[name="category[]"]').on('change', function(e) {
    // $("#categorym"+i).on('change', function(e) {
    // $(document).on('change', "#categorym"+j, function(e) {
  });
  $(document).on('change', "#categorym", function(e) {
    // alert('Change Happened');
    var cat_id = e.target.value;
    $.ajax({
      url: "{{ route('subcat') }}",
      type: "POST",
      data: {
        cat_id: cat_id
      },
      success: function(data) {
        var $SubC = $('#sub1category' + j).empty();
        $SubC.append($("<option></option>").attr("value", '').text('Please Select'));
        $.each(data.subcategories[0].subcategories, function(index, sub1category) {
          $('#sub1category' + j).append('<option value="' + sub1category.id + '">' + sub1category.name +
            '</option>');
        })
      }
    })
    // j++;
  });
  var k = 1;

  function sub1call(cid) {
    //   alert(cid);
    // };
    var cat_id = cid;
    // var cat_id = cid.target.value;
    $.ajax({
      url: "{{ route('subcat') }}",
      type: "POST",
      data: {
        cat_id: cat_id
      },
      success: function(data) {
        // alert("sucess");
        var $SubC1 = $('#sub2category' + j).empty();
        $SubC1.append($("<option></option>").attr("value", '').text('Please Select'));
        $.each(data.subcategories[0].subcategories, function(index, sub2category) {
          $('#sub2category' + j).append('<option value="' + sub2category.id + '">' + sub2category.name +
            '</option>');
        })
        j++;
        k++;
      }
    })
  };
</script>

<script type="text/javascript">
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  $(document).ready(function() {
    $('#categoryMobile1').select2({});
  });
  $(document).ready(function() {
    $('#categoryMobile1').change(function() {
      var mobile1id = $(this).val();
      if (mobile1id != '') {
        // AJAX request
        $.ajax({
          url: "{{ route('getVendorInfo') }}",
          type: 'post',
          data: {
            _token: CSRF_TOKEN,
            mobile1id: mobile1id,
          },
          dataType: 'json',
          success: function(response) {
            // console.log(response);
            if (response.success == 1) {
              $('#ct_addr_id').val(response.id);
              $('#ct_name').val(response.name);
              $('#ct_mobile1').val(response.mobile1);
              $('#ct_mobile2').val(response.mobile2);
              $('#ct_companyname').val(response.companyname);
              $('#ct_address').val(response.address);
              $('#ct_city').val(response.city);
              $('#ct_district').val(response.district);
              $('#ct_state').val(response.state);
              $('#ct_pincode').val(response.pincode);
              $('#ct_gstno').val(response.gstno);
            } else {
              alert("Invalid ID.");
            }
          }
        });
      } else {
        alert('Please fill all fields.');
      }
    });
  });
</script>