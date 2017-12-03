<div id="login" class="text-center">
  <div class="well">
    <form class="form-horizontal" @submit.prevent="login()">
      <div class="form-group">
        <div class="col-sm-12">
          <input type="text" name="email" class="form-control" placeholder="Email" v-model="data.email" autofocus required>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-12">
          <input type="password" name="password" class="form-control" placeholder="Password" v-model="data.password" required>
        </div>
      </div>
      <button class="btn btn-danger btn-block">Login</button>
    </form>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#login',
  data: {
    data: {}
  },
  methods: {
    login: function () {
      axios.post('api/auth/login', vm.data).then(function (response) {
        if (response.status == 200) {
          location.reload();
        }
     }, function (response) {
       alert('잘못된 로그인 정보입니다.');
     });
    }
  }
});
</script>
