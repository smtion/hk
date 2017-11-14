<div id="purchase-option-list">
  <div class="title">옵션 목록</div>

  <div class="text-right margin-bottom-1">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">등록</button>
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>옵션명</th>
        <th>옵션상세</th>
        <th>옵션</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in list">
        <td>{{ item.id }}</td>
        <td>{{ item.code }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.type }}</td>
        <td>{{ item.desc }}</td>
        <td>편집</td>
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

  <div class="row">
    <div class="col-sm-offset-2 col-sm-2">
      <select class="form-control" v-model="search">
        <option value="code">옵션 구성 코드</option>
        <option value="name">옵션 구성 명칭</option>
        <option value="type">옵션 구성 구분</option>
        <option value="desc">옵션 구성 항목</option>
      </select>
    </div>
    <div class="col-sm-4">
      <input type="text" class="form-control" v-model="keyword">
    </div>
    <div class="col-sm-2">
      <button class="btn btn-primary btn-block" @click="goPage(1)">검색</button>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="modalCreateLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">옵션 상세 등록</h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">옵션 구성 코드</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="시스템 자동 생성" v-model="data.code" disabled>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">옵션 구성 명칭</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="" v-model="data.name">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">옵션 구성 구분</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="" v-model="data.type">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">옵션 구성 항목</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="" v-model="data.desc">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
          <button type="button" class="btn btn-primary" @click="create()">등록</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#purchase-option-list',
  data: {
    list: [],
    data: {},
    search: 'code',
    keyword: '',
    sort: '',
    direction: '',
    paginate: {},
  },
  methods: {
    init: function() {
      vm.getList(1);
    },
    goPage: function (page) {
      vm.getList(page);
    },
    getList: function (page) {
      if (!page) page = 1;

      var params = makeParams({
        page: page,
        search: vm.search,
        keyword: vm.keyword,
        sort: vm.sort,
        direction: vm.direction,
      });
      axios.get('/api/purchase/option_list?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    },
    create: function () {
      axios.post('/api/purchase/option_list', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          $('#modalCreate').modal('hide');
          vm.getList(vm.paginate.page);
        }
      });
    }
  }
});
vm.init();
</script>
