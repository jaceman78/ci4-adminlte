<?= $this->extend('layout/master') ?>

<?= $this->section('pageHeader') ?>
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Dashboard</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="<?= route_to('user/index') ?>">Homeestanodash10</a></li>
            <li class="breadcrumb-item active"><?= $title ?? 'Dashboard' ?></li>
        </ol>
           </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
              <!--begin::Col-->
              <div class="col-lg-3 col-6">
                <!--begin::Small Box-->
                <div class="small-box bg-info">
                  <div class="inner">
                    <h3>150</h3>
                    <p>New Orders</p>
                  </div>
                  <div class="icon"><i class="bi bi-bag"></i></div>
                  <a href="#" class="small-box-footer">More info <i class="bi bi-arrow-right-circle"></i></a>
                </div>
                <!--end::Small Box-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->

                <div class="col-lg-3 col-6">
                  <!--begin::Small Box-->
                  <div class="small-box bg-success">
                    <div class="inner">
                      <h3>53<sup style="font-size: 20px">%</sup></h3>
                      <p>Bounce Rate</p>
                    </div>
                    <div class="icon"><i class="bi bi-sticky"></i></div>
                    <a href="#" class="small-box-footer">More info <i class="bi bi-arrow-right-circle"></i></a>
                  </div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-lg-3 col-6">
                  <!--begin::Small Box-->
                  <div class="small-box bg-warning">
                    <div class="inner">
                      <h3>44</h3>
                      <p>User Registrations</p>
                    </div>
                    <div class="icon"><i class="bi bi-person-add"></i></div>
                    <a href="#" class="small-box-footer">More info <i class="bi bi-arrow-right-circle"></i></a>
                  </div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-lg-3 col-6">
                  <!--begin::Small Box-->
                  <div class="small-box bg-danger">
                    <div class="inner">
                      <h3>65</h3>
                      <p>Unique Visitors</p>
                    </div>
                    <div class="icon"><i class="bi bi-pie-chart"></i></div>
                    <a href="#" class="small-box-footer">More info <i class="bi bi-arrow-right-circle"></i></a>
                  </div>
                </div> 

                
                <br>
                <div class="col-lg-3 col-6">
                  <!--begin::Small Box-->
                  <div class="small-box bg-primary">
                    <div class="inner">
                      <h3>75</h3>
                      <p>Testando Nível</p>
                       <p><?= session()->get("LoggedUserData")['name'] ?? ""; ?><br>
                       <?= session()->get("LoggedUserData")['level'] ?? ""; ?></p>
                    </div>
                    <div class="icon"><i class="bi bi-people"></i></div>
                    <a href="#" class="small-box-footer">More info <i class="bi bi-arrow-right-circle"></i></a>
                  </div>

</div>
                  <!--end::Small Box-->

<?= $this->endSection() ?>

