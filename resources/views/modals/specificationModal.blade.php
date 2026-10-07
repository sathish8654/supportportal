<style>
.ui-front {
    z-index: 5000;
}
</style>


<div class="modal fade" id="addNewSpecModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md ui-front" role="document">
        <!-- <div class="modal-dialog modal-lg ui-front" role="document"> -->
        <div class="modal-content">
            <!-- <Input type="text" id="tbox" /> -->
            <div class="modal-header">
                <h5 class="modal-title">Spec Info</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" role="alert" id="successMsg" style="display: none"> Added successfully!
                </div>
                <div class="row">
                    <div class="col-md-12 col-md-offset-4">
                        <form id="SubmitFormAdd">
                            <fieldset>
                                <!-- Text input-->
                                <div class="col-md-12">
                                    <label class="control-label" for="textinput">Spec Name *</label>
                                    <input type="text" placeholder="Spec Name" id="name" class="form-control" required>
                                </div>
                                <!-- Text input-->
                                <!-- <select class="browser-default custom-select form-control select2 Required" id="partIds" style='width:100%;'>
                                        <option value='' selected>--Select--</option>
                                        @foreach ($spec as $item)
                                        <option value='{{ $item->id }}'>{{ $item->name }}</option>
                                        @endforeach
                                    </select> -->
                                <!-- </div> -->
                                <div class="col-md-12 form-group">
                                    <br>
                                    <div class="modal-footer">
                                        <div class="pull-right">
                                            <input id="addr_id" type="hidden">
                                            <button class="btn btn-success" id="SubmitUpdate"
                                                style="display:none;">Update</button>
                                            <button class="btn btn-success btn-submit" id="submitCreate">Create</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- /.col-lg-12 -->
    </div><!-- /.row -->
</div><!-- /.row -->




<script type="text/javascript">
$('#addNewPartModal').on('hidden.bs.modal', function () {
    $("#SubmitUpdate").hide();
    $("#submitCreate").show();
    $(this).find('form').trigger('reset'); // clear modal
})


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".btn-submit").click(function (e) {

        e.preventDefault();

        var name = $("#name").val();

        if (name === '') {
            alert('Please fill the name!');
            return false;
        }

        $.ajax({
            type: 'POST',
            url: "{{ route('addNewSpec') }}",
            data: {
                        _token: CSRF_TOKEN,
                        name: name,

            },
            success: function (response) {
                $('#successMsg').show();
                $('#SubmitFormAdd').empty();

                console.log(response);
            },
            success: function (response) {
                alert(response.message);
                window.location.reload();
            },
            error: function (response) {
                alert(JSON.parse(response.responseText).message);
                // $('#nameErrorMsg').text(response.responseJSON.errors.name);
                // alert("Duplicate or Invalid entry, please check!")
            },
        });
    });

    // get part into modal
    // CSRF Token
    $(document).ready(function () {
        // get record info
        $('#empTable').on('click', '.getPartInfo', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $("#SubmitUpdate").show();
            $("#submitCreate").hide();

            // AJAX request
            // $.ajax({
            //     url: "{{ route('getPartInfo') }}",
            //     type: 'post',
            //     data: {
            //             _token: CSRF_TOKEN,
            //             id: id
            //     },
            //     dataType: 'json',
            //     success: function (response) {
            //         if (response.success == 1) {
            //             $('#addr_id').val(response.id);
            //             $('#name').val(response.name);


            //         } else {
            //             alert("Invalid ID.");
            //         }
            //     }
            // });
            $('#addr_id').val(id);
            $('#name').val(name);

        });



        // Update record
        $('#SubmitUpdate').click(function (e) {
            e.preventDefault();

            var id = $('#addr_id').val();
            var name = $('#name').val().trim();


            if (id != '' && name != '') {

                // AJAX request
                $.ajax({
                    url: "{{ route('updateProdSpec') }}",
                    type: 'post',
                    data: {
                        id: id,
                        name: name,

                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success == 1) {
                            alert(response.msg);

                            $('#empTable').DataTable().ajax.reload();

                            // reset form
                            document.getElementById("SubmitFormAdd").reset();

                            // Close modal
                            $("#addNewSpecModal .close").trigger("click");

                        } else {
                            //   alert(response.msg);
                            alert('Please verify data.');
                        }
                    },
                    error: function (response) {
                        alert("Duplicate entry, please check name");
                    }

                });

            } else {
                alert('Please fill all fields.');
            }
        });

        var mytable = $('#ticketsList').DataTable();

        // Delete record
        $('#empTable').on('click', '.disableProdSpec', function () {
            var id = $(this).data('id');
            var deleteConfirm = confirm("Are you sure disable from list?");
            if (deleteConfirm == true) {
                // AJAX request
                $.ajax({
                    url: "{{ route('disableProdSpec') }}",
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        id: id
                    },
                    success: function (response) {
                        alert(response.message);
                        $('#empTable').DataTable().ajax.reload();
                    }
                });
            }

        });

    });


</script>







<div class="modal fade" id="addNewSpecSubModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md ui-front" role="document">
        <!-- <div class="modal-dialog modal-lg ui-front" role="document"> -->
        <div class="modal-content">
            <!-- <Input type="text" id="tbox" /> -->
            <div class="modal-header">
                <h5 class="modal-title">Add Sub Spec</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" role="alert" id="successMsg" style="display: none"> Added successfully!
                </div>
                <div class="row">
                    <div class="col-md-12 col-md-offset-4">
                        <form id="submitSpecSub">
                            <fieldset>
                                <!-- Text input-->
                                <div class="col-md-12">
                                <input type="hidden"  id="subId" >
                                    <label class="control-label" for="textinput">Spec Name *</label>
                                    <select class="browser-default custom-select form-control select2 RequiredSub" id="partIdSub" style='width:100%;'>
                                        <option value='' selected>--Select--</option>
                                        @foreach ($spec as $item)
                                        <option value='{{ $item->id }}'>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="control-label" for="textinput">Spec Sub Name *</label>
                                    <input type="text" placeholder="Sub Name" id="Subname" class="form-control RequiredSub">
                                </div>


                                <!-- </div> -->
                                <div class="col-md-12 form-group">
                                    <br>
                                    <div class="modal-footer">
                                        <div class="pull-right">
                                            <button class="btn btn-success" id="SubSubmitUpdate" style="display:none;">Update</button>
                                            <button class="btn btn-success sub-btn-submit-spec" id="SubSubmitCreate">Create</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- /.col-lg-12 -->
    </div><!-- /.row -->
</div><!-- /.row -->




<script type="text/javascript">

$('#partIds').select2({
  dropdownParent: $('#addNewSpecSubModal')
 });

$('#addNewSpecSubModal').on('hidden.bs.modal', function () {
    $("#SubSubmitUpdate").hide();
    $("#SubSubmitCreate").show();
    $(this).find('form').trigger('reset'); // clear modal
    // $('#addNewSpecSubModal').find('form.your-form-class').trigger('reset');
    $(this).find('.select2').val('').trigger('change');

})


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".sub-btn-submit-spec").click(function (e) {
        e.preventDefault();

        var partIds = $("#partIdSub").val();
        var Subname = $("#Subname").val();
        // var partValue = $("#partValue").val();
        var subId = $("#subId").val();

        let allFilled = true;
        $('.RequiredSub').each(function() {
        if ($(this).val() === '') {
            allFilled = false;
            return false; // Exit the loop if any input is empty
        }
        });
        if (allFilled!=true) {
            alert('Please fill in all input fields.');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: "{{ route('addNewSubSpec') }}",
            data: {
                partIds: partIds,
                Subname: Subname,
                // partValue: partValue,
                subId: subId,
            },
            success: function (response) {
                $('#successMsg').show();
                $('#submitSpecSub').empty();

                console.log(response);
            },
            success: function (response) {
                alert(response.message);
                window.location.reload();
            },
            error: function (response) {
                alert(JSON.parse(response.responseText).message);
                // $('#nameErrorMsg').text(response.responseJSON.errors.name);
                // alert("Duplicate or Invalid entry, please check!")
            },
        });
    });

    // get part into modal
    // CSRF Token
    $(document).ready(function () {
        // get record info

        $('#partSubTable').on('click', '.getPartSubInfo', function () {// for update work
            $("#SubSubmitUpdate").show();
            $("#SubSubmitCreate").hide();
            $('#subId').val($(this).data('id'));
            $('#partIdSub').val($(this).data('parent_id')).trigger("change");
            $('#Subname').val($(this).data('name'));
            $('#partValue').val($(this).data('part_value'));

        });



        // Update record
        $('#SubSubmitUpdate').click(function (e) {
            e.preventDefault();

            var id = $('#subId').val();
            var partIds = $('#partIdSub').val();
            var name = $('#Subname').val().trim();
            var partValue = $("#partValue").val();
            if (id != '' && name != '') {

                // AJAX request
                $.ajax({
                    url: "{{ route('addNewSubSpec') }}",
                    type: 'post',
                    data: {
                        parent_id: partIds,
                        subId: id,
                        Subname: name,
                        mode: 2,
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        if (response.success == 1) {
                            // alert(response.msg);

                            $('#partSubTable').DataTable().ajax.reload();

                            // reset form
                            document.getElementById("submitSpecSub").reset();

                            // Close modal
                            $("#addNewSpecSubModal .close").trigger("click");

                        } else {
                            //   alert(response.msg);
                            alert('Please verify data.');
                        }
                    },
                    error: function (response) {
                        alert("Duplicate entry, please check name");
                    }

                });

            } else {
                alert('Please fill all fields.');
            }
        });

        var mytable = $('#ticketsList').DataTable();

        // Delete record
        $('#partSubTable').on('click', '.disableProdSpec', function () {
            var id = $(this).data('id');
            var deleteConfirm = confirm("Are you sure disable from list?");
            if (deleteConfirm == true) {
                // AJAX request
                $.ajax({
                    url: "{{ route('disableProdSpec') }}",
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        id: id
                    },
                    success: function (response) {
                        // alert(response.message);
                        $('#partSubTable').DataTable().ajax.reload();
                    }
                });
            }

        });

    });


</script>
