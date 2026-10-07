    <!-- Modal addCase -->
    <!-- https://www.codermen.com/submit-laravel-form-using-ajax-post-method-with-validation/ -->
    <div class="modal " id="addCase" tabindex="-1" role="dialog" aria-labelledby="addCaseModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addCaseModalLabel">Add New Case</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-success" role="alert" id="successMsg" style="display: none"> Added successfully!
            </div>
            <span class="text-danger" id="nameErrorMsg"></span>
            <form id="SubmitFormCaseAdd">
              <div class="mb-3">
                <label for="CaseName" class="form-label">New Category</label>
                <input type="text" class="form-control" id="CaseName">
              </div>
              <div class="modal-footer">
                <div class="form-group">
                  <button class="btn btn-success" id="SubmitCaseAdd">Submit</button>
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
      $(document).ready(function () {

        // Submit to add case category function
        $('#SubmitFormCaseAdd').on('click', '#SubmitCaseAdd', function (e) {
          e.preventDefault();

          let name = $('#CaseName').val();

          $.ajax({
            url: "{{ route('submitAddCase') }}",
            type: "POST",
            data: {
              "_token": "{{ csrf_token() }}",
              name: name,
            },
            success: function (response) {
              $('#successMsg').show();
              $('#SubmitFormCaseAdd').empty();

              console.log(response);
            },
            error: function (response) {
              // $('#nameErrorMsg').text(response.responseJSON.errors.name);
              alert("Duplicate or Invalid entry, please check!")
            },
          });
        });


      });
    </script>