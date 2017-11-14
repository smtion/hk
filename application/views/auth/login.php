<div id="login" class="text-center">
  <div class="well">
    <form name="frm">
      <input type="email" name="email" class="form-control" v-model="data.email">
      <input type="password" name="password" class="form-control" v-model="data.password">
    </form>

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
