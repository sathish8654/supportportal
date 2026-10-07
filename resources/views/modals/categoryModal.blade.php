    <!-- Modal addParent -->
    <!-- https://www.codermen.com/submit-laravel-form-using-ajax-post-method-with-validation/ -->
    <div class="modal fade" id="addParent" tabindex="-1" role="dialog" aria-labelledby="addParentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                   <h5 class="modal-title" id="addParentModalLabel">Add New Category</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success" role="alert" id="successMsg" style="display: none"> Added
                        successfully!
                    </div>
                    <span class="text-danger" id="nameErrorMsg"></span>
                    <input type="hidden" id="cust_Id" name="cust_Id">
                    <form id="SubmitFormAdd">
                        <div class="mb-3" id="InputCatField">
                            <label for="ParentCat" class="form-label">Category</label>
                            <input type="text" class="form-control" id="ParentCat">
                        </div>
<!-- style="display:none;" -->
                        <div class="mb-3" id="InputNameField" >
                            <label for="InputName" class="form-label">Model</label>
                            <input type="text" class="form-control" id="InputName" required>
                        </div>
                        <div class="mb-3" id="InputModelNameField" >
                            <label for="InputmodelNumber" class="form-label">Model Number</label>
                            <input type="text" class="form-control" id="InputmodelNumber" required>
                        </div>
                        <div class="modal-footer">
                            <div class="form-group">
                                <input type="hidden" id="parent_id" value="0">
                                <button class="btn btn-success" id="SubmitAdd">Create Category</button>
                                <button class="btn btn-success" id="SubmitAddSub1" style="display:none;">Create Modal</button>
                                <button class="btn btn-success" id="SubmitUpdate" style="display:none;">Update Category</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).ready(function() {

    $("#InputNameField").hide();
    $("#InputModelNameField").hide();
    $("#SubmitUpdate").hide();

    $('#addParent').on('hidden.bs.modal', function () { 
    location.reload();
    $('#SubmitFormAdd')[0].reset();

});


    // Submit to add parent category function
    $('#SubmitFormAdd').on('click', '#SubmitAdd', function(e) {
        e.preventDefault();
        // $("#InputNameField").hide();
        // $("#InputModelNameField").hide();

        let parent_id = $('#parent_id').val();
        let name = $('#ParentCat').val();

        $.ajax({
            url: "{{ route('submitAddCat') }}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                name: name,
                parent_id: parent_id,
            },
            success: function(response) {
                $('#successMsg').show();
                $('#SubmitFormAdd').empty();

                console.log(response);
            },
            error: function(response) {
                // $('#nameErrorMsg').text(response.responseJSON.errors.name);
                alert("Duplicate or Invalid entry, please check!");
            },
        });
    });



    // Submit to add sub category function
    $('#SubmitFormAdd').on('click', '#SubmitAddSub1', function(e) {
        e.preventDefault();
        // $("#InputNameField").hide();
        // $("#InputModelNameField").hide();

        let parent_id = $('.categorym').val();
        let name = $('#InputName').val();
        let modelNumber = $('#InputmodelNumber').val();

        $.ajax({
            url: "{{ route('submitAddCat') }}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                name: name,
                modelNumber: modelNumber,
                parent_id: parent_id,
            },
            success: function(response) {
                $('#successMsg').show();
                $('#SubmitFormAdd').empty();

                console.log(response);
            },
            error: function(response) {
                // $('#nameErrorMsg').text(response.responseJSON.errors.name);
                alert("Duplicate or Invalid entry, please check!")
            },
        });
    });

  


    //  <!-- addSubCat1Modal record -->

    $('#addSubCat1Modal').click(function() {

        $('.modal-title').text('Add New Model');

        var cat_id = $('.categorym').val();

        $.ajax({
            url: "{{ route('getSubCat1') }}",
            type: "POST",
            data: {
                cat_id: cat_id
            },
            success: function(data) {
                if (data.success == 0) {
                    alert("Please select main category!");
                } else {
                    $("#ParentCat").attr('readonly', 'readonly');
                   // $("#addParent").modal('show');
                    $('#addParent').modal('toggle');

                    $("#InputNameField").show();
                    $("#InputModelNameField").show();
                    $("#SubmitAddSub1").show();
                    $("#SubmitAdd").hide();
                    $('#ParentCat').val(data.name);
                    $("#SubmitUpdate").hide();
                }
                // $('#sub1category1').empty();
            }
        })
    });


    
    $(document).on('click', '.addSubCat1Modal', function () {
        $("#SubmitAdd").hide();
        $("#SubmitAddSub1").show();

        $('.modal-title').text('Add New Model');
        
        let cat_id = $(this).data('id');
 
        $.ajax({
            url: "{{ route('getSubCat1') }}",
            type: "POST",
            data: {
                cat_id: cat_id
            },
            success: function(data) {
                if (data.success == 0) {
                    alert("Please select main category!");
                } else {
                    $("#ParentCat").attr('readonly', 'readonly');
                    // $("#addParent").modal('show');
                    $('#addParent').modal('toggle');

                    $("#InputNameField").show();
                    $("#InputModelNameField").show();
                    $("#SubmitAddSub1").show();
                    $("#SubmitAdd").hide();
                    $('#ParentCat').val(data.name);
                    $("#SubmitUpdate").hide();
                }
                // $('#sub1category1').empty();
            }
        })
    });

// for update, get existing model data
     $(document).on('click', '.updateSubCat1Modal', function () {
        $('.modal-title').text('Update Model Number');
        
        let cat_id = $(this).data('id');
 
        $.ajax({
            url: "{{ route('getSubCat1') }}",
            type: "POST",
            data: {
                cat_id: cat_id
            },
            success: function(data) {
                if (data.success == 0) {
                    alert("Please select main category!");
                } else {
                    $("#InputName").attr('readonly', 'readonly');
                    // $("#addParent").modal('show');
                    $('#addParent').modal('toggle');

                    $("#SubmitAddSub1").hide();
                    $("#InputCatField").hide();
                    $("#InputNameField").show();
                    $("#SubmitUpdate").show();
                    $("#InputModelNameField").show();
                    $("#SubmitAdd").hide();
                    $('#cust_Id').val(data.id);
                    $('#InputName').val(data.name);
                    $('#InputmodelNumber').val(data.modelNumber);
                }
                // $('#sub1category1').empty();
            }
        })
    });


    
    // Submit to update model function
    $('#SubmitFormAdd').on('click', '#SubmitUpdate', function(e) {
        e.preventDefault();
        // $("#InputNameField").hide();
        // $("#InputModelNameField").hide();
        let id = $('#cust_Id').val();
        let modelNumber = $('#InputmodelNumber').val();
        $.ajax({
            url: "{{ route('submitUpdate') }}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                id: id,
                modelNumber: modelNumber,
            },
            success: function(response) {
                $('#nameErrorMsg').hide();
                $('#successMsg').show();
                $('#SubmitFormAdd').empty();

                console.log(response);
            },
            error: function(response) {
                $('#nameErrorMsg').text(response.responseJSON.errors.modelNumber[0]);
                if(!(response.responseJSON.errors.modelNumber[0])){
                alert("Duplicate or Invalid entry, please check!")
                } else {
                    alert(response.responseJSON.errors.modelNumber[0]);
                }
            },
        });
    });




    //  <!-- addSubCat2Modal record -->

    $('#addSubCat2Modal').click(function() {

        var cat_id = $('#sub1category1').val();

        $.ajax({
            url: "{{ route('getSubCat2') }}",
            type: "POST",
            data: {
                cat_id: cat_id
            },
            success: function(data) {
                if (data.success == 0) {
                    alert("Please select main category!");
                } else {
                    $("#ParentCat").attr('readonly', 'readonly');
                    // $("#addParent").modal('show');
                    $('#addParent').modal('toggle');

                    $("#InputNameField").show();
                    $("#InputModelNameField").show();
                    $("#SubmitAdd").hide();
                    $('#ParentCat').val(data.name);


                }
                // $('#sub1category1').empty();
            }
        })
    });

});
    </script>