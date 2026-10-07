<style>
.ui-front {
    z-index: 5000;
}
.select2-container {
    z-index: 99999 !important; /* Ensure it's above the modal */
}
</style>


<div class="modal fade" id="addNewPartSubModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md ui-front" role="document">
        <!-- <div class="modal-dialog modal-lg ui-front" role="document"> -->
        <div class="modal-content">
            <!-- <Input type="text" id="tbox" /> -->
            <div class="modal-header">
                <h5 class="modal-title">Parts Infos</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" role="alert" id="successMsg" style="display: none"> Added successfully!
                </div>
                <div class="row">
                    <div class="col-md-12 col-md-offset-4">
                        <form id="submitPartSub">
                            <fieldset>
                                <!-- Text input-->
                                <div class="col-md-12">
                                <input type="hidden"  id="subId" >
                                    <label class="control-label" for="textinput">Part Name *</label>
                                    <select class="browser-default custom-select form-control select2 Required" id="partIds" style='width:100%;'>
                                        <option value='' selected>--Select--</option>
                                        @foreach ($parts as $item)
                                        <option value='{{ $item->id }}'>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="control-label" for="textinput">Sub Name *</label>
                                    <input type="text" placeholder="Sub Name" id="Subname" class="form-control Required">
                                </div>
                                <div class="col-md-12">
                                    <label class="control-label" for="textinput">Part value *</label>
                                    <input type="text" placeholder="Part value" id="partValue" class="form-control Required">
                                </div>
                                <!-- Text input-->

                                <!-- </div> -->
                                <div class="col-md-12 form-group">
                                    <br>
                                    <div class="modal-footer">
                                        <div class="pull-right">
                                            <button class="btn btn-success" id="SubSubmitUpdate" style="display:none;">Update</button>
                                            <button class="btn btn-success sub-btn-submit" id="SubSubmitCreate">Create</button>
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
  dropdownParent: $('#addNewPartSubModal')
 });

$('#addNewPartSubModal').on('hidden.bs.modal', function () {
    $("#SubSubmitUpdate").hide();
    $("#SubSubmitCreate").show();
    $(this).find('form').trigger('reset'); // clear modal
    // $('#addNewPartSubModal').find('form.your-form-class').trigger('reset');
    $(this).find('.select2').val('').trigger('change');

})


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".sub-btn-submit").click(function (e) {
        e.preventDefault();

        var partIds = $("#partIds").val();
        var Subname = $("#Subname").val();
        var partValue = $("#partValue").val();
        var subId = $("#subId").val();

        let allFilled = true;
        $('.Required').each(function() {
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
            url: "{{ route('addNewSubPart') }}",
            data: {
                partIds: partIds,
                Subname: Subname,
                partValue: partValue,
                subId: subId,
            },
            success: function (response) {
                $('#successMsg').show();
                $('#submitPartSub').empty();

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

        $('#partSubTable').on('click', '.getPartSubInfo, .viewPartSub', function () {// for update work
            var isViewOnly = $(this).hasClass('viewPartSub') || $(this).data('view-only') == 1;

            if (isViewOnly) {
                $('#addNewPartSubModal .modal-title').text('View Details');
                $('#addNewPartSubModal input, #addNewPartSubModal select').prop('disabled', true);
                $("#SubSubmitUpdate").hide();
                $("#SubSubmitCreate").hide();
            } else {
                $('#addNewPartSubModal .modal-title').text('Update');
                $('#addNewPartSubModal input, #addNewPartSubModal select').prop('disabled', false);
                $("#SubSubmitUpdate").show();
                $("#SubSubmitCreate").hide();
            }

            $('#subId').val($(this).data('id'));
            $('#partIds').val($(this).data('parent_id')).trigger("change");
            $('#Subname').val($(this).data('name'));
            $('#partValue').val($(this).data('part_value'));

        });



        // Update record
        $('#SubSubmitUpdate').click(function (e) {
            e.preventDefault();

            var id = $('#subId').val();
            var partIds = $('#partIds').val();
            var name = $('#Subname').val().trim();
            var partValue = $("#partValue").val();
            if (id != '' && name != '') {

                // AJAX request
                $.ajax({
                    url: "{{ route('updatePartInfoSub') }}",
                    type: 'post',
                    data: {
                        parent_id: partIds,
                        id: id,
                        name: name,
                        partValue: partValue,
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success == 1) {
                            alert(response.msg);

                            $('#partSubTable').DataTable().ajax.reload();

                            // reset form
                            document.getElementById("submitPartSub").reset();

                            // Close modal
                            $("#addNewPartSubModal .close").trigger("click");

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
        $('#partSubTable').on('click', '.disablePartSub', function () {
            var id = $(this).data('id');
            var deleteConfirm = confirm("Are you sure disable from list?");
            if (deleteConfirm == true) {
                // AJAX request
                $.ajax({
                    url: "{{ route('disablePart') }}",
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
