<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <!-- <a class="navbar-brand" href="/main">HK</a> -->
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <? if (has_permission('sales')) : ?><li><a href="/sales">영업팀</a></li><? endif ?>
        <? if (has_permission('purchase')) : ?><li><a href="/purchase">구매팀</a></li><? endif ?>
        <? if (has_permission('production')) : ?><li><a href="/production">생산팀</a></li><? endif ?>
        <? if (has_permission('finance')) : ?><li><a href="/finance">재무팀</a></li><? endif ?>
        <? if (has_permission('admin')) : ?><li><a href="/admin">관리자</a></li><? endif ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=get_session_data('name') . ' (' . get_session_data('email') . ')'?> <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/mypage">마이페이지</a></li>
            <? if (get_role() == 'admin') : ?><li><a href="#">관리자</a></li><? endif ?>
            <li role="separator" class="divider"></li>
            <li><a href="/auth/logout">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
