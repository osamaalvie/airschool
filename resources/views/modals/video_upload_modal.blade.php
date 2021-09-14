<!-- Modal -->
<div class="modal fade" id="videoUploadModal" tabindex="-1" role="dialog" aria-labelledby="videoUploadModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input class="form-control" type="text" id="title" name="title"  required/>
                    </div>
                    <div class="form-group">
                        <input type="file" name="video" id="video" accept="video/*" required>
                    </div>


                    <div class="progress">
                        <div class="progress-bar"></div>
                    </div>

                    <!-- Display upload status -->
                    <div id="uploadStatus"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>