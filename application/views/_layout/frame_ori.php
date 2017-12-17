<!DOCTYPE html>
<html lang="ko">
<head>
<?php $this->load->view('_layout/header'); ?>
<?php $this->load->view('_layout/settings'); ?>
<?php $this->load->view('_layout/css'); ?>
<?php $this->load->view('_layout/js'); ?>
</head>
<body>
<?php if(is_logged_on()) : ?>
  <div class="content-wrapper">
    <?php $this->load->view('_layout/sidebar'); ?>
    <div class="content">
      <?php $this->load->view('_layout/nav'); ?>
      <div class="container-fluid">

        <?php $this->load->view('_layout/template'); ?>
      </div>
    </div>

  </div>
<?php else : ?>
  <div class="container">
    <?php $this->load->view('_layout/template'); ?>
  </div>
<?php endif ?>
<?php $this->load->view('_layout/footer'); ?>
</body>
</html>
