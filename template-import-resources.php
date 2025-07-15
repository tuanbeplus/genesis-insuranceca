<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<div ng-app="app">
  <div ng-controller="UploadController as vm">
    <div class="container">
      <div class="page-header">
        <h1>Import Resources</h1>
      </div>
      <div class="alert alert-info">
        <strong>Note: </strong> Please upload all file PDF to folder resources: uploads/resources/[type resources]/[all file] before import!
      </div>
      <?php if($error_import): ?>
        <div class="alert alert-danger">
            <?php echo $error_import; ?>
        </div>
      <?php endif; ?>
      <form enctype="multipart/form-data" method="post" action="">
        <div class="form-group">
          <label for="fileName">Select a file (xlsx)</label>
          <div class="input-group">
            <input type="file" name="file-import" class="form-control">
          </div>
        </div>
        <div>
          <button type="reset" class="btn btn-default" ng-click="reset()">Reset</button>
          <button type="submit" name="action-import" class="btn btn-primary">Upload</button>
        </div>
      </form>
    </div>
  </div>
</div>
