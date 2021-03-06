<div id="sales-project" v-cloak>
  <div class="title">프로젝트 관리</div>

  <div class="text-right margin-bottom-1">
    <a class="btn btn-primary" href="/sales/project_create">등록</a>
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>회사이름</th>
        <th>프로젝트 이름</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in list">
        <td>{{ getNo(index) }}</td>
        <td>{{ item.corp_name }}</td>
        <td>{{ item.name }}</td>
        <td><a  :href="'/sales/project/' + item.id">보기</a></td>
      </tr>
    </tbody>
  </table>

  <div class="text-center">
    <paginate
      :page-count="Math.ceil(paginate.total / paginate.limit)"
      :click-handler="goPage"
      :prev-text="'<'"
      :next-text="'>'"
      :container-class="'pagination'"
      :page-class="'page-item'">
    </paginate>
  </div>

  <form class="row" @submit.prevent>
    <div class="col-sm-offset-2 col-sm-2">
      <select class="form-control" v-model="search">
        <option value="corp_name">회사이름</option>
        <option value="name">프로젝트 이름</option>
      </select>
    </div>
    <div class="col-sm-4">
      <input type="text" class="form-control" v-model="keyword">
    </div>
    <div class="col-sm-2">
      <button class="btn btn-primary btn-block" @click="goPage(1)">검색</button>
    </div>
  </form>

  <!-- Modal -->
  <div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="modalCreateLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">프로젝트 등록</h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">프로젝트 이름</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.name">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">국내 or 해외</label>
              <div class="col-sm-8">
                <label class="radio-inline">
                  <input type="radio" value="0" v-model="data.crossborder"> 국내
                </label>
                <label class="radio-inline">
                  <input type="radio" value="1" v-model="data.crossborder"> 해외
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">회사이름 한글</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.corp_name">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">회사이름 영문</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.corp_name_en">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">전화번호1</label>
              <div class="col-sm-8">
                <input type="phone" class="form-control" v-model="data.tel1">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">전화번호2</label>
              <div class="col-sm-8">
                <input type="phone" class="form-control" v-model="data.tel2">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">FAX 번호</label>
              <div class="col-sm-8">
                <input type="phone" class="form-control" v-model="data.fax">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Email 주소</label>
              <div class="col-sm-8">
                <input type="email" class="form-control" v-model="data.email">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
          <button type="button" class="btn btn-primary" v-show="!data.id" @click="create()">등록</button>
          <button type="button" class="btn btn-primary" v-show="data.id" @click="update()">변경</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#sales-project',
  data: {
    data: {},
    list: {},
    search: 'corp_name',
    keyword: '',
    paginate: {},
  },
  methods: {
    init: function () {
      if (!vm.paginate.page) vm.paginate.page = 1;
      $('#modalCreate').on('hidden.bs.modal', function () {
        vm.reset();
      });
      $('#modalAdd').on('hidden.bs.modal', function () {
        vm.reset();
      });
      vm.reload();
    },
    reset: function () {
      vm.data = {};
    },
    reload: function () {
      vm.reset();
      vm.getList();
    },
    getNo: function (i) {
      return vm.paginate.total - ((vm.paginate.page - 1) * vm.paginate.limit) - i;
    },
    goPage: function (page) {
      vm.getList(page);
    },
    edit: function (index) {
      vm.data = JSON.parse(JSON.stringify(vm.list[index]));
      $('#modalCreate').modal('show');
    },
    getList: function (page) {
      if (!page) page = 1;

      var params = makeParams({
        page: page,
        search: vm.search,
        keyword: vm.keyword,
      });
      axios.get('/api/sales/project?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    },
    create: function () {
      axios.post('/api/sales/project', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          $('#modalCreate').modal('hide');
          vm.reload();
        }
      });
    },
    update: function () {
      axios.patch('/api/sales/project', vm.data).then(function (response) {
        if (response.status == 200) {
          alert('변경되었습니다.');
          $('#modalCreate').modal('hide');
          vm.reload();
        }
      });
    }
  }
});
vm.init();
</script>
