<div class="sidebar">
  <p class="title">HAE KWANG ERP</p>
  <p class="welcome">Welcome,</p>
  <p><?=get_user_name()?></p>
  <?php if (isset($sidebar)) $this->load->view($sidebar); ?>
</div>
