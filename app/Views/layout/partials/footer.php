<footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">rodapé</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
          Copyright &copy; 2014-2025&nbsp;
          <a href="#" id="teamPhotoLink" class="text-decoration-none">HardWork550</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
      </footer>
      <!--end::Footer-->
      
      <!-- Modal para exibir foto da equipa -->
      <div class="modal fade" id="teamPhotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 position-relative">
              <button type="button" class="btn-close position-absolute top-0 end-0 m-2 bg-white" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1050;"></button>
              <img src="<?= base_url('adminlte/img/team_2025.jpg') ?>" class="img-fluid w-100 rounded" alt="Team 2025">
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
       <script src="<?= base_url('adminlte/js/adminlte.js') ?>"></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
      integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
   
    <!--end::Required Plugin(Bootstrap 5)   <script src="../../../dist/js/adminlte.js"></script>--><!--begin::Required Plugin(AdminLTE)-->
 




  <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Sistema Global de Toasts -->
<script src="<?= base_url('assets/js/toast-notifications.js') ?>"></script>

<!-- Script para abrir modal da foto da equipa -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const teamPhotoLink = document.getElementById('teamPhotoLink');
    if (teamPhotoLink) {
      teamPhotoLink.addEventListener('click', function(e) {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById('teamPhotoModal'));
        modal.show();
      });
    }
  });
</script>
  <!--end::Footer scripts-->



