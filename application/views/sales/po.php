<div id="sales-po" v-cloak>
  <div class="title">PO 목록</div>

  <div class="text-right margin-bottom-1">
    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">등록</button> -->
    <!-- <a class="btn btn-primary" href="/sales/quotation_create">등록</a> -->
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>견적번호</th>
        <th>Version</th>
        <th>프로젝트 이름</th>
        <th>납기 기간</th>
        <th>총금액</th>
        <th>고객 전송 날짜</th>
        <th>가 PO 등록</th>
        <th>PO 등록</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in list">
        <td>{{ getNo(index) }}</td>
        <td>{{ item.code }}</td>
        <td>{{ item.version }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.delivery_term }}일 후</td>
        <td>{{ item.total | number }}</td>
        <td></td>
        <td>
          <button type="button" class="btn btn-default bt-sm" @click="createTempPo(index)">등록</button>
          <!-- <a class="btn btn-default btn-sm" :href="'/sales/po_create/' + item.id">등록</a> -->
        </td>
        <td>
          <button type="button" class="btn btn-default bt-sm" @click="createPo(index)">등록</button>
          <!-- <a class="btn btn-default btn-sm" :href="'/sales/po_create/' + item.id">등록</a> -->
        </td>
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
        <option value="code">견적번호</option>
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
          <h4 class="modal-title" id="myModalLabel"><span v-show="!mode">가</span> PO 등록</h4>
        </div>
        <h4 class="text-center">견적 정보</h4>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">견적서 번호</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" :value="selected.code">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">견적서 버전</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" :value="selected.version">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">프로젝트 이름</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" :value="selected.name">
              </div>
            </div>
          </div>
          <p class="text-center">
            위 견적서를 <span v-show="!mode">가</span> PO로 등록 등록 하시겠습니까?
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
          <a type="button" class="btn btn-primary" :href="'/sales/po_create/' + selected.quotation_detail_id">등록</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#sales-po',
  data: {
    list: [],
    selected: {},
    mode: false,
    search: 'code',
    keyword: '',
    paginate: {},
  },
  methods: {
    init: function () {
      if (!vm.paginate.page) vm.paginate.page = 1;
      $('#modalCreate').on('hidden.bs.modal', function () {
        vm.reset();
      });
      vm.reload();
    },
    reset: function () {
      vm.selected = {};
      vm.mode = false;
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
    createTempPo: function (index) {
      vm.selected = vm.list[index];
      vm.mode = false;

      $('#modalCreate').modal('show');
    },
    createPo: function (index) {
      vm.selected = vm.list[index];
      vm.mode = true;

      $('#modalCreate').modal('show');
    },
    getList: function (page) {
      if (!page) page = 1;

      var params = makeParams({
        page: page,
        search: vm.search,
        keyword: vm.keyword,
      });
      axios.get('/api/sales/po?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    }
  }
});
vm.init();
</script>
