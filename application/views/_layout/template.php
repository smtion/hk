<?php
  if (is_array($template)) foreach($template as $t) $this->load->view($t);
  else if (isset($template)) $this->load->view($template);
?>
<div class="clear"></div>
