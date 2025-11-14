      
      <a id="back-to-top" href="#" class="btn btn-outline-warning back-to-top" role="button" aria-label="Scroll to top">
         <i class="fas fa-chevron-up"></i>
      </a>

      </div> <!-- content-wrapper ends -->

      <!-- Page Footer-->
      <footer class="main-footer">
         <strong>Copyright &copy; 2016-<?php echo date('Y'); ?>.</strong>
         All rights reserved.
         <div class="float-right d-none d-sm-inline-block">
         <!--<b>Version</b> 3.2.0-->
         </div>
      </footer>

      <div id="sidebar-overlay"></div>
      
   </div> <!-- wrapper ends -->

   <!-- Reminder Alert Modal - starts -->
   <div id="reminderAlertModal" class="modal fade" data-backdrop="static">
      <div class="modal-dialog modal-confirm">
         <div class="modal-content">
            <div class="modal-header">
               <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body" style="color: dimgray; font-style: italic;"></div>
            <div class="modal-footer justify-content-between">
               <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal" data-target="#reminderAlertModal">Cancel</button>
               <button type="button" id="btn-reminderAlertModal-ok" class="btn btn-danger float-right btn-sm">Yes</button>
            </div>
         </div>
      </div>
   </div>
   <!-- Reminder Alert Modal - starts -->   

   <!-- Error Alert Modal - starts -->
   <div id="errorAlertModal" class="modal fade" data-backdrop="static">
      <div class="modal-dialog modal-confirm">
         <div class="modal-content">
            <div class="modal-header">
               <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body" style="color: dimgray; font-style: italic;"></div>
            <div class="modal-footer">
               <button type="button" class="btn btn-danger float-right btn-sm" data-dismiss="modal" data-target="#errorAlertModal">OK</button>
            </div>
         </div>
      </div>
   </div>
   <!-- Error Alert Modal - starts -->

   <!-- Submission Confirmation Modal - starts -->
   <div id="confirmSubmitModal" class="modal fade" data-backdrop="static">
      <div class="modal-dialog modal-confirm">
         <div class="modal-content">
            <div class="modal-header">
               <h3 class="modal-title">Confirm Submit?</h3>
            </div>
            <div class="modal-body" style="color: dimgray; font-style: italic;"></div>
            <div class="modal-footer justify-content-between">
               <button type="button" id="btn-confirm-no" class="btn btn-outline-secondary btn-sm">No</button>
               <button type="button" id="btn-confirm-yes" class="btn btn-danger btn-sm float-right">Yes</button>
            </div>
         </div>
      </div>
   </div>
   <!-- Submission Confirmation Modal - ends -->

   <!-- Submission Delete Modal - starts -->
   <div id="confirmDeleteModal" class="modal fade" data-backdrop="static">
      <div class="modal-dialog modal-confirm">
         <div class="modal-content">
            <div class="modal-header">
               <h3 class="modal-title">Confirm Delete?</h3>
            </div>
            <div class="modal-body" style="color: dimgray; font-style: italic;"></div>
            <div class="modal-footer justify-content-between">
               <button type="button" id="btn-confirm-delete-no" class="btn btn-outline-secondary btn-sm"  data-dismiss="modal" data-target="#confirmDeleteModal">No</button>
               <button type="button" id="btn-confirm-delete-yes" class="btn btn-danger btn-sm float-right">Yes</button>
            </div>
         </div>
      </div>
   </div>
   <!-- Submission Confirmation Modal - ends -->

   <!-- Logout Modal - starts -->
   <div id="logoutModal" class="modal fade show" data-backdrop="static">
      <div class="modal-dialog modal-confirm">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title">Logout</h4>
            </div>
            <div class="modal-body">
               <p>Are you sure to logout?</p>
            </div>
            <div class="modal-footer justify-content-between">
               <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" data-target="#logoutModal">No</button>
               <a href="/login/signout" class="btn btn-dark btn-sm float-right">Yes</a>
            </div>
         </div>
      </div>
   </div>
   <!-- Logout Modal - ends -->

   <script src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.2.1/browser/overlayscrollbars.browser.es6.min.js" integrity="sha512-f3C0D/1oFo3acuPqEOeWI2k7NNdNbsIVZp5dvparW7pPUZrQvFqz+WfiFAVAFgqb3nzaL5QkcEu9sHzw3d7HlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>   

   <script src="/assets/js/custom.js"></script>

</body>
</html>
