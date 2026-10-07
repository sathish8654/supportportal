<style>
.ui-front {
    z-index: 5000;
}
</style>


<div class="modal fade" id="addNewPartModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg ui-front" role="document">
        <!-- <div class="modal-dialog modal-lg ui-front" role="document"> -->
        <div class="modal-content">
            <!-- <Input type="text" id="tbox" /> -->
            <div class="modal-header">
                <h5 class="modal-title">Part Info</h5>
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
                                    <label class="control-label" for="textinput">Name *</label>
                                    <input type="text" placeholder="Part Name" id="name" class="form-control" required>
                                </div>
                                <!-- Text input-->

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
            url: "{{ route('addNewPart') }}",
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
        $('#empTable').on('click', '.getPartInfo, .viewPart', function () {
            var isViewOnly = $(this).hasClass('viewPart') || $(this).data('view-only') == 1;

            if (isViewOnly) {
                $('#addNewPartModal .modal-title').text('View Details');
                $('#addNewPartModal input, #addNewPartModal select').prop('disabled', true);
                $('#SubmitUpdate').hide();
                $('#submitCreate').hide();
            } else {
                $('#addNewPartModal .modal-title').text('Update');
                $('#addNewPartModal input, #addNewPartModal select').prop('disabled', false);
                $('#SubmitUpdate').show();
                $('#submitCreate').hide();
            }

            var id = $(this).data('id');

            // AJAX request
            $.ajax({
                url: "{{ route('getPartInfo') }}",
                type: 'post',
                data: {
                        _token: CSRF_TOKEN,
                        id: id
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success == 1) {
                        $('#addr_id').val(response.id);
                        $('#name').val(response.name);


                    } else {
                        alert("Invalid ID.");
                    }
                }
            });

        });



        // Update record
        $('#SubmitUpdate').click(function (e) {
            e.preventDefault();

            var id = $('#addr_id').val();
            var name = $('#name').val().trim();


            if (id != '' && name != '') {

                // AJAX request
                $.ajax({
                    url: "{{ route('updatePartInfo') }}",
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
                            $("#addNewPartModal .close").trigger("click");

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
        $('#empTable').on('click', '.disablePart', function () {
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
                        alert(response.message);
                        $('#empTable').DataTable().ajax.reload();
                    }
                });
            }

        });

    });


</script>
