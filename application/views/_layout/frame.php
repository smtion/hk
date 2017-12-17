<!DOCTYPE html>
<html lang="ko">
<head>
<?php $this->load->view('_layout/header'); ?>
<?php $this->load->view('_layout/settings'); ?>
<?php $this->load->view('_layout/css'); ?>
<?php $this->load->view('_layout/js'); ?>
</head>
<body>

<body class="nav-md">
  <?php if(is_logged_on()) : ?>
  <div class="container body">
    <div class="main_container">
      <div class="col-md-3 left_col">
        <div class="left_col scroll-view">
          <div class="navbar nav_title" style="border: 0;">
            <a href="/" class="site_title"><span>Hae Kwang ERP</span></a>
          </div>

          <div class="clearfix"></div>

          <!-- menu profile quick info -->
          <div class="profile clearfix">
            <div class="profile_info">
              <span>Welcome,</span>
              <h2><?=get_user_name()?></h2>
            </div>
          </div>
          <br>
          <?php $this->load->view('_layout/sidebar'); ?>
        </div>
      </div>
      <?php $this->load->view('_layout/nav'); ?>

      <!-- page content -->
      <div class="right_col" role="main">
        <?php $this->load->view('_layout/template'); ?>
      </div>
      <?php $this->load->view('_layout/footer'); ?>
    </div>
  </div>
  <?php else : ?>
  <div class="container">
    <?php $this->load->view('_layout/template'); ?>
  </div>
  <?php endif ?>
  <script src="/bower_components/gentelella/build/js/custom.min.js"></script>
</body>
</html>
