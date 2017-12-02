<div id="sales-project-view" v-cloak>
  <input id="id" type="hidden" value="<?=$id?>">
  <div class="title">프로젝트 보기</div>

  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-4 control-label">프로젝트 이름</label>
      <div class="col-sm-8">
        <span class="form-control">{{ item.project_name }}</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">국내 or 해외</label>
      <div class="col-sm-8">
        <span class="form-control">{{ item.crossborder == '1' ? '해외' : '국내' }}</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">회사이름 한글</label>
      <div class="col-sm-8">
        <span class="form-control">{{ item.corp_name }}</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">회사이름 영문</label>
      <div class="col-sm-8">
        <span class="form-control">{{ item.corp_name_en }}</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">전화번호1</label>
      <div class="col-sm-8">
        <span class="form-control">{{ item.tel1 }}</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">전화번호2</label>
      <div class="col-sm-8">
        <span class="form-control">{{ item.tel2 }}</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">FAX 번호</label>
      <div class="col-sm-8">
        <span class="form-control">{{ item.fax }}</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Email 주소</label>
      <div class="col-sm-8">
        <span class="form-control">{{ item.email }}</span>
      </div>
    </div>
  </div>

  <div class="text-center margin-top-1">
    <a class="btn btn-primary" href="/sales/project">목록</a>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#sales-project-view',
  data: {
    id: $('#id').val(),
    item: {},
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
      axios.get('/api/sales/project/' + vm.id).then(function (response) {
        if (response.status == 200) {
          vm.item = response.data.item;
        }
      });
    }
  }
});
</script>
