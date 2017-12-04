<div id="mypage-info">
  <div class="title">내 정보</div>

  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-4 control-label">이름</label>
      <div class="col-sm-8">
        <input id="name" type="text" class="form-control" v-model="data.name">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">직급</label>
      <div class="col-sm-8">
        <input type="text" class="form-control" v-model="data.position_name" disabled>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">부서</label>
      <div class="col-sm-8">
        <input type="text" class="form-control" v-model="data.dept_name" disabled>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">이메일</label>
      <div class="col-sm-8">
        <input id="email" type="email" class="form-control" v-model="data.email">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">전화번호</label>
      <div class="col-sm-8">
        <input type="text" class="form-control" v-model="data.tel">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">휴대폰번호</label>
      <div class="col-sm-8">
        <input type="text" class="form-control" v-model="data.phone">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">현재 비밀번호</label>
      <div class="col-sm-8">
        <input id="password" type="password" class="form-control" v-model="data.password">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">새 비밀번호</label>
      <div class="col-sm-8">
        <input type="password" class="form-control" v-model="data.new_passwoord">
      </div>
    </div>
  </div>

  <div class="margin-top-1 text-center">
    <button class="btn btn-primary" @click="save()">저장</button>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#mypage-info',
  data: {
    data: {}
  },
  mounted: function () {
    this.$nextTick(function () {
      vm.init();
    });
  },
  methods: {
    init: function () {
      vm.reload();
    },
    reload: function () {
      vm.getItem();
    },
    getItem: function () {
      axios.get('/api/mypage/info').then(function (response) {
        if (response.status == 200) {
          vm.data = response.data.item ? response.data.item : {};
        }
      });
    },
    save: function () {
      if (!vm.data.name) {
        alert('이름을 입력하세요.');
        $('#name').focus();
        return;
      } else if (!vm.data.email) {
        alert('이메일을 입력하세요.');
        $('#email').focus();
        return;
      } else if (!vm.data.password) {
        alert('정보를 변경하시려면 현재 비밀번호를 입력하세요.');
        $('#password').focus();
        return;
      }

      axios.patch('/api/mypage/info', vm.data).then(function (response) {
        if (response.status == 200) {
          alert('저장되었습니다.');
        }
      }, function (error) {
        if (error.response.status == 403) {
          alert('비밀번호가 잘못되었습니다.');
        }
      });
    }
  }
});
</script>
