<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/node_modules/vue/dist/vue.min.js"></script>
<script src="/node_modules/axios/dist/axios.min.js"></script>
<script src="/node_modules/vuejs-paginate/dist/index.js"></script>
<script>
Vue.component('paginate', VuejsPaginate);


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
