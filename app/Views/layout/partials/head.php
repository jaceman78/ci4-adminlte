<head>  <!-- head.php -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $this->renderSection('title', true) ?: 'ESJB - Digital' ?></title>

  <!-- Google Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- AdminLTE -->
 


  <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
    />
    <!--end::Fonts-->
      <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
      integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
      crossorigin="anonymous"
    />

    <!--begin::Required Plugin(AdminLTE) head.php-->
     <link rel="stylesheet" href="<?= base_url('adminlte/css/adminlte.css') ?>">
    <!--end::Required Plugin(AdminLTE)-->
 
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">

  
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<!-- Font Awesome (se não estiver incluído) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Custom Styles from Views -->
<?= $this->renderSection('styles') ?>

<!-- ── Tema: Dark Total ── -->
<style>
  :root {
    --bs-primary:              #2a2f5b;
    --bs-primary-rgb:          42, 47, 91;
    --bs-link-color:           #7b8cde;
    --bs-link-hover-color:     #a3afe8;
  }
  /* Navbar escura */
  .app-header.navbar {
    background: #16213e !important;
    border-bottom: 1px solid #0f1629;
  }
  .app-header .nav-link,
  .app-header .nav-link i { color: #c8d0e7 !important; }
  .app-header .nav-link:hover { color: #ffffff !important; }
  /* Sidebar: tom ligeiramente diferente da navbar para hierarquia */
  .app-sidebar { background: #1a1a2e !important; }
  .sidebar-brand { background: rgba(0,0,0,0.25) !important; }
  /* Botões */
  .btn-primary { background-color: #2a2f5b !important; border-color: #2a2f5b !important; }
  .btn-primary:hover, .btn-primary:focus { background-color: #1e2244 !important; border-color: #1e2244 !important; }
  .btn-outline-primary { color: #2a2f5b !important; border-color: #2a2f5b !important; }
  .btn-outline-primary:hover { background-color: #2a2f5b !important; color: #fff !important; }
  /* Cards */
  .card-primary.card-outline { border-top: 3px solid #2a2f5b !important; }
  .card-primary > .card-header { background-color: #2a2f5b !important; }
  .bg-primary { background-color: #2a2f5b !important; }
  /* Tabs & texto */
  .nav-tabs .nav-link.active { border-top: 3px solid #2a2f5b; color: #2a2f5b; }
  .text-primary { color: #2a2f5b !important; }
  .sidebar-menu .nav-link.active { background: rgba(255,255,255,0.15) !important; }
  a { color: #2a2f5b; }
  a:hover { color: #4a56a8; }
</style>

<!-- Preloader -->
<style>
  #esjb-preloader {
    position: fixed;
    inset: 0;
    z-index: 99999;
    margin: 0;
    background: radial-gradient(circle at center, #1a0033, #0a001a, #000);
    perspective: 1200px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-family: Arial, sans-serif;
    transition: opacity 0.5s ease, visibility 0.5s ease;
  }
  #esjb-preloader.esjb-preloader-hide {
    opacity: 0;
    visibility: hidden;
  }
  #esjb-preloader .spinner-container {
    position: relative;
    width: 180px;
    height: 180px;
  }
  #esjb-preloader .logo-3d {
    width: 100%;
    height: 100%;
    animation: spin3D 2.8s linear infinite, pulse3D 2.2s ease-in-out infinite;
    transform-style: preserve-3d;
    filter: drop-shadow(0 0 30px rgba(180, 80, 255, 0.9));
  }
  #esjb-preloader .text {
    margin-top: 40px;
    color: #d0a0ff;
    font-size: 20px;
    letter-spacing: 4px;
    text-transform: uppercase;
    animation: textGlow 1.8s ease-in-out infinite;
    text-shadow: 0 0 20px rgba(180, 80, 255, 0.8);
  }
  #esjb-preloader .light-particle {
    position: absolute;
    width: 6px;
    height: 6px;
    background: rgba(200, 150, 255, 0.9);
    border-radius: 50%;
    box-shadow: 0 0 12px #fff;
    animation: particleFloat 6s linear infinite;
  }
  @keyframes spin3D {
    from { transform: rotateX(15deg) rotateY(0deg) rotateZ(0deg); }
    to   { transform: rotateX(15deg) rotateY(360deg) rotateZ(360deg); }
  }
  @keyframes pulse3D {
    0%, 100% { transform: scale(1) rotateX(15deg); }
    50%       { transform: scale(1.12) rotateX(25deg); }
  }
  @keyframes textGlow {
    0%, 100% { opacity: 0.75; text-shadow: 0 0 15px rgba(180, 80, 255, 0.6); }
    50%       { opacity: 1;    text-shadow: 0 0 30px rgba(200, 120, 255, 1); }
  }
  @keyframes particleFloat {
    0%   { transform: translateY(0)   translateX(0)   scale(1);    opacity: 0.8; }
    50%  { transform: translateY(-60px) translateX(20px) scale(1.3); opacity: 0.4; }
    100% { transform: translateY(-120px) translateX(-10px) scale(0.7); opacity: 0; }
  }
</style>

</head>
