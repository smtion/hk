<div id="admin-company">
  <div class="title">사용자 접속 정보</div>

  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-4 control-label">회사이름</label>
      <div class="col-sm-8">
        <input id="corp_name" type="text" class="form-control" v-model="data.corp_name">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">회사이름 영문</label>
      <div class="col-sm-8">
        <input type="text" class="form-control" v-model="data.corp_name_en">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">대표자 이름</label>
      <div class="col-sm-8">
        <input id="name" type="text" class="form-control" v-model="data.name">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">사업자번호</label>
      <div class="col-sm-8">
        <input id="reg_no" type="text" class="form-control" v-model="data.reg_no">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">법인등록번호</label>
      <div class="col-sm-8">
        <input type="text" class="form-control" v-model="data.corp_no">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">대표번호</label>
      <div class="col-sm-8">
        <input id="tel" type="text" class="form-control" v-model="data.tel">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">주소</label>
      <div class="col-sm-8">
        <input id="address" type="text" class="form-control" v-model="data.address">
      </div>
    </div>
  </div>

  <div class="margin-top-1 text-center">
    <button class="btn btn-primary" @click="save()">저장</button>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#admin-company',
  data: {
    data: {}
  },
  methods: {
    init: function () {
      vm.reload();
    },
    reload: function () {
      vm.getList();
    },
    getList: function () {
      axios.get('/api/admin/company').then(function (response) {
        if (response.status == 200) {
          vm.data = response.data.item ? response.data.item : {};
        }
      });
    },
    validate: function () {
      if (!vm.data.corp_name) {
        alert('회사이름을 입력하세요.');
        $('#corp_name').focus();
        return false;
      } else if (!vm.data.name) {
        alert('대표자 이름을 입력하세요.');
        $('#name').focus();
        return false;
      } else if (!vm.data.reg_no) {
        alert('사업자번호를 입력하세요.');
        $('#reg_no').focus();
        return false;
      } else if (!vm.data.tel) {
        alert('대표번호를 입력하세요.');
        $('#tel').focus();
        return false;
      } else if (!vm.data.address) {
        alert('주소를 입력하세요.');
        $('#address').focus();
        return false;
      }

      return true;
    },
    save: function () {
      if (!vm.validate()) return;

      axios.put('/api/admin/company', vm.data).then(function (response) {
        if (response.status == 200) {
          alert('저장되었습니다.');
        }
      });
    }
  }
});
vm.init();
</script>
