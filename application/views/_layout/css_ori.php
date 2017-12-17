<link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" href="/bower_components/gentelella/build/css/custom.min.css">

<style>
html, body, .content-wrapper {
  height: 100%;
  background-color: #F7F7F7;
  color: #73879C;
}
* {
  word-break: keep-all;
}
[v-cloak] {
  display:none;
}

.navbar {
  /*margin-bottom: 0;*/
  background: #EDEDED;
  border-bottom: 1px solid #D9DEE4;
  margin-bottom: 10px;
  border-radius: 0;
}
.navbar-default .navbar-nav>li>a {
  color: #5A738E;
}
.content-wrapper {
  position: relative;

}
.sidebar {
  position: absolute;
  width: 250px;
  top: 0;
  left: 0;
}
.content {
  position: relative;
  margin-left: 250px;
}

/* Common */
.pointer {
  cursor: pointer;
}
.margin-top-1  {
  margin-top: 15px;
}
.margin-bottom-1 {
  margin-bottom: 15px;
}
.margin-top-between-1 + .margin-top-between-1 {
  margin-top: 15px;
}
.margin-top-2  {
  margin-top: 30px;
}
.margin-bottom-2 {
  margin-bottom: 30px;
}

/* Sidebar */
.sidebar {
  background-color: #2A3F54;
  color: #ECF0F1;
  height: 100%;
}
.sidebar p {
  margin: 15px;
}
.sidebar .title {
  font-size: 22px;
}
.sidebar .welcome {
  margin-top: 45px;
  color: #BAB8B8;
}
.sidebar .list-group-item {
  padding: 10px 15px 10px 45px;
  border-radius: 0;
  border: 0;
  background-color: #2A3F54;
}
.sidebar a.list-group-item, .sidebar label.list-group-item {
  color: #ECF0F1;
}
.sidebar a.list-group-item.active {
  text-shadow: rgba(0,0,0,.25) 0 -1px 0;
  background: linear-gradient(#334556,#2C4257),#2A3F54;
  box-shadow: rgba(0,0,0,.25) 0 1px 0, inset rgba(255,255,255,.16) 0 1px 0;
}
.sidebar a.list-group-item:hover {
  background-color: rgba(255,255,255,.06);
}
.sidebar .list-group-item-success {
  padding: 10px 15px;
}
.sidebar .list-group-item-info {
  padding: 10px 15px 10px 30px;
}


/* Content */
.content .title {
  font-size: 18px;
  margin-top: 15px;
  margin-bottom: 30px;
}

/* Search */
.search-form {
  text-align: center;
}
.search-form {

}

/* Login */
#login {
  margin: 300px auto;
  width: 400px;
}

/* Purchase */
.table tr, th {
  text-align: center;
  vertical-align: middle !important;
}

.table .outer-td {
  padding: 0;
}
.table .inner-td div {
  /*padding: 8px;*/
  padding: 12px;
}
.table .inner-td > div + div {
  border-top: 1px solid #ddd;
}
.table .inner-td + .inner-td div {
  border-left: 1px solid #ddd;
}
</style>
