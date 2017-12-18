<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<script src="/node_modules/vue/dist/vue.js"></script>
<script src="/node_modules/axios/dist/axios.min.js"></script>
<script src="/node_modules/vuejs-paginate/dist/index.js"></script>
<script src="/bower_components/es6-promise/es6-promise.auto.min.js"></script>

<script src="/node_modules/moment/min/moment.min.js"></script>
<script src="/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="/node_modules/vue-bootstrap-datetimepicker/dist/vue-bootstrap-datetimepicker.min.js"></script>
<script src="/bower_components/chart.js/dist/Chart.min.js"></script>
<script src="/bower_components/excellentexport/dist/excellentexport.js"></script>

<script src="/assets/js/html2canvas.min.js"></script>
<script src="/assets/js/jspdf.min.js"></script>

<!-- <script src="/assets/js/pdf.worker.js"></script> -->
<script>
Vue.component('paginate', VuejsPaginate);
Vue.component('date-picker', VueBootstrapDatetimePicker.default);
Vue.config.debug = true;
Vue.filter('product_type', function (value) {
  var data = ['', 'Per\'f', 'Grating', 'Blind'];
  return data[value];
});
Vue.filter('number', function (value) {
  value = parseInt(value);
  if (isNaN(value)) return 0;
  else if (value === null) return 0;
  else if (value) return value.toLocaleString();
  else return value;
});
Vue.filter('nbsp', function (value) {
  if (!value) return '<span></span>';
  else return value;
});
Vue.filter('date', function (value) {
  return value.substr(0, 10);
});
Vue.filter('jsdate', function (diff) {
  var d = null;
  if (diff) {
    d = new Date(new Date().setDate(new Date().getDate() + diff));
  } else {
    d = new Date();
  }

  return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).substr(-2) + '-' + ('0' + d.getDate()).substr(-2);
})

$(document).ready(function () {
  // Set the sidebar .active class automatically
  // var path = location.pathname.match(/(\/([^\/.]*)(\/([^\/.]*)))/)[0];
  // $('.sidebar .list-group-item[href^="' + path + '"]').addClass('active');

  $(".sidebar .list-group-item").each(function (i, obj) {
    if (location.pathname.indexOf($(obj).attr('href')) == 0) {
      $(obj).addClass('active');
      return false;
    }
  });
});


//-----------------------------------------------------------------------
// Global helper functions
//-----------------------------------------------------------------------
function today() {
  return new Date().toLocaleDateString('ko-KR').replace(/\. /g, '-').replace(/\./g, '');
}
function makeParams(items) {
  var arr = [];

  // Object.keys(items).map(function (key, index) {
  //   var value = items[key];
  //   arr.push(key + '=' + value);
  // });
  for(var key in items) {
    if (items[key]) arr.push(key + '=' + items[key]);
  }

  return arr.join('&');
}
</script>
