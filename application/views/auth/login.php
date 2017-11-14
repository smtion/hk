<div id="login" class="text-center">
  <div class="well">
    <div class="form-horizontal">
      <div class="form-group">
        <div class="col-sm-12">
          <input type="email" name="email" class="form-control" placeholder="Email" v-model="data.email">
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-12">
          <input type="password" name="password" class="form-control" placeholder="Password" v-model="data.password">
        </div>
      </div>
    </div>

    <button class="btn btn-danger btn-block" @click="login()">Login</button>
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
          alert('Welcome');
          location.reload();
        } else {
          console.error('Unauthorized');
        }
     });
    }
  }
});
</script>
