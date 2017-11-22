<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<script src="/node_modules/vue/dist/vue.js"></script>
<script src="/node_modules/axios/dist/axios.min.js"></script>
<script src="/node_modules/vuejs-paginate/dist/index.js"></script>

<script src="/node_modules/moment/min/moment.min.js"></script>
<script src="/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="/node_modules/vue-bootstrap-datetimepicker/dist/vue-bootstrap-datetimepicker.min.js"></script>


<script>
Vue.component('paginate', VuejsPaginate);
Vue.component('date-picker', VueBootstrapDatetimePicker.default);
Vue.config.debug = true;
$(document).ready(function () {
  // Set the sidebar .active class automatically
  $('.sidebar .list-group-item[href="' + location.pathname + '"]').addClass('active');

  // $(".sidebar .list-group-item").each(function (i, obj) {
  //   if ($(obj).attr('href') == location.pathname) {
  //     $(obj).addClass('active');
  //     return false;
  //   }
  // });
  // })();
});


//-----------------------------------------------------------------------
// Global helper functions
//-----------------------------------------------------------------------
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
