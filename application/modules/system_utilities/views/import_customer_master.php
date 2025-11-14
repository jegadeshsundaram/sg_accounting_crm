<section class="content-header">
  <?php
    $list = ['active' => 'Import Customer Master'];
    echo breadcrumb($list);
  ?>
</section><br />
<section class="content">
<?php echo get_flash_message('message'); ?>
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Import Customer Master</h3>
      </div>
      <div style="padding-top: 15px;">
        <form autocomplete="off" class="form-horizontal validate" enctype="multipart/form-data" method="post" action="<?php echo $save_url; ?>">
          <div class="form-group" style="margin-left:0;">
            <label for="db_file" class="col-sm-2 control-label">Database File (.dbf)</label>
            <div class="col-sm-8 error_block">
              <input class="form-control" name="db_file" id="db_file" placeholder="DBF File" type="file" style="height: 39px;">
            </div>
          </div>
          <div class="box-footer">
            <a href="/dashboard" class="btn btn-default">Cancel</a>
            <button id="submitbtn" type="submit" class="btn btn-info pull-right">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</section>
<script type="text/javascript">

$('document').ready(function() {
    $('#loading_modal').modal('hide');
    $cansend = false;
    $(".form-horizontal").submit(function(e) {
      var form = $(this);
      $.confirm({
        title: "<i class='fa fa-info'></i> Do backup before PROCEED. (PROCESS WILL OVER-WRITE CORRESPONDING MASTER TABLE IN CRM SYSTEM)",
        text: "Confirm?",
        confirmButton: 'Yes',
        confirmButtonClass: 'btn-success',
        confirm: function(button) {
          $('#loading_modal').modal();
          $cansend = true;
          form.submit();                
        },
        cancelButton: 'No',
        cancelButtonClass: 'btn-danger',
        cancel: function(button) {
          $cansend = false;
        }
      });
      
      if ($cansend == true) {
        $cansend = false;
        return true;
      } else {
        return false;
      }

    });
  });
</script>
