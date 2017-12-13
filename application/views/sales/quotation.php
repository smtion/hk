<div id="sales-quotation" v-cloak>
  <div class="title">견적서 관리</div>

  <div class="text-right margin-bottom-1">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">등록</button>
    <!-- <a class="btn btn-primary" href="/sales/quotation_create">등록</a> -->
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>견적번호</th>
        <th>Version</th>
        <th>국가</th>
        <th>회사이름</th>
        <th>프로젝트 이름</th>
        <th>계약서 발행일</th>
        <th>총금액</th>
        <th>승인</th>
        <th>보기</th>
        <!-- <th>다운로드</th> -->
        <th>견적상세등록</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in list">
        <td>{{ getNo(index) }}</td>
        <td>{{ item.code }}</td>
        <td>{{ item.version }}</td>
        <td>{{ item.country }}</td>
        <td>{{ item.corp_name }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.publish_date }}</td>
        <td>{{ item.total | number }}</td>
        <td></td>
        <td><a class="btn btn-default btn-sm" :href="'/sales/quotation/' + item.quotation_detail_id" v-if="item.quotation_detail_id">보기</a></td>
        <!-- <td></td> -->
        <td><a class="btn btn-default btn-sm" :href="'/sales/quotation_create/' + item.id">추가</a></td>
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
          <h4 class="modal-title" id="myModalLabel">프로젝트 등록</h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">프로젝트 이름</label>
              <div class="col-sm-8">
                <select id="project" class="form-control" v-model="data.project_id" v-show="!data.id">
                  <option value="">선택하세요.</option>
                  <!-- <option v-for="(item, index) in projects" :value="index">{{ item.name }}</option> -->
                  <option v-for="item in projects" :value="item.id">{{ item.name }}</option>
                </select>
                <span class="form-control" v-show="data.id">{{ data.name }}</span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">견적 언어</label>
              <div class="col-sm-8">
                <label class="radio-inline">
                  <input type="radio" value="0" v-model="data.language"> 국문
                </label>
                <label class="radio-inline">
                  <input type="radio" value="1" v-model="data.language"> 영문
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Payment Term</label>
              <div class="col-sm-3">
                <select class="form-control" v-model="pt.m" :disabled="pt.ns">
                  <option value="0">개월</option>
                  <option v-for="i in 12" :value="i">{{ i }}</option>
                </select>
              </div>
              <div class="col-sm-3">
                <select class="form-control" v-model="pt.d" :disabled="pt.ns">
                  <option value="0">일</option>
                  <option v-for="i in 30" :value="i">{{ i }}</option>
                </select>
              </div>
              <div class="col-sm-2">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" v-model="pt.ns">
                    미정
                  </label>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">배송 납품기일</label>
              <div class="col-sm-3">
                <select class="form-control" v-model="dt.m" :disabled="dt.ns">
                  <option value="0">개월</option>
                  <option v-for="i in 12" :value="i">{{ i }}</option>
                </select>
              </div>
              <div class="col-sm-3">
                <select class="form-control" v-model="dt.d" :disabled="dt.ns">
                  <option value="0">일</option>
                  <option v-for="i in 30" :value="i">{{ i }}</option>
                </select>
              </div>
              <div class="col-sm-2">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" v-model="dt.ns">
                    미정
                  </label>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">공사 이름</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.construct_name">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">견적서 발행일</label>
              <div class="col-sm-8">
                <input id="publish_date" type="date" class="form-control" v-model="data.publish_date">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">견적서 유효기간</label>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">발행 후</span>
                  <input type="phone" class="form-control" v-model="data.expiry_day">
                  <span class="input-group-addon">일</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">승인 필요</label>
              <div class="col-sm-8">
                <label class="radio-inline">
                  <input type="radio" value="1" v-model="data.approval"> 필요
                </label>
                <label class="radio-inline">
                  <input type="radio" value="0" v-model="data.approval"> 불필요
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">비고</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.memo">
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
  el: '#sales-quotation',
  data: {
    data: {
      expiry_day: 30,
    },
    list: [],
    projects: [],
    selectedIndex: '',
    pt: {
      m: 0,
      d: 0,
      ns: false,
    },
    dt: {
      m: 0,
      d: 0,
      ns: false,
    },
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
      $('#modalAdd').on('hidden.bs.modal', function () {
        vm.reset();
      });
      vm.reload();
    },
    reset: function () {
      vm.data = {
        expiry_day: 30,
        project_id: '',
      };
      vm.selectedIndex = '';
      vm.pt = {
        m: 0,
        d: 0,
        ns: false,
      };
      vm.dt = {
        m: 0,
        d: 0,
        ns: false,
      };
    },
    reload: function () {
      vm.reset();
      vm.getList();
      vm.getProjects();
    },
    getNo: function (i) {
      return vm.paginate.total - ((vm.paginate.page - 1) * vm.paginate.limit) - i;
    },
    goPage: function (page) {
      vm.getList(page);
    },
    edit: function (index) {
      vm.selectedIndex = index;
      vm.data = JSON.parse(JSON.stringify(vm.list[index]));
      if (vm.data.payment_term == 0) {
        vm.pt.m = vm.pt.d = 0;
        vm.pt.ns = true;
      } else {
        vm.pt.m = Math.floor(vm.data.payment_term / 30);
        vm.pt.d = Math.floor(vm.data.payment_term % 30);
        vm.pt.ns = false;
      }
      if (vm.data.delivery_term == 0) {
        vm.dt.m = vm.dt.d = 0;
        vm.dt.ns = true;
      } else {
        vm.dt.m = Math.floor(vm.data.delivery_term / 30);
        vm.dt.d = Math.floor(vm.data.delivery_term % 30);
        vm.dt.ns = false;
      }

      $('#modalCreate').modal('show');
    },
    getProjects: function () {
      axios.get('/api/sales/quotation_projects').then(function (response) {
        if (response.status == 200) {
          vm.projects = response.data.list;
        }
      });
    },
    getList: function (page) {
      if (!page) page = 1;

      var params = makeParams({
        page: page,
        search: vm.search,
        keyword: vm.keyword,
      });
      axios.get('/api/sales/quotation?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    },
    validate: function () {
      if (!vm.data.id && !vm.data.project_id) {
        alert('프로젝트를 선택하세요.');
        $('#project').focus();
        return false;
      } else if (!vm.data.publish_date) {
        alert('견적서 발행일을 입력하세요.');
        $('#publish_date').focus();
        return false;
      }

      return true;
    },
    create: function () {
      if (!vm.validate()) return;

      vm.data.payment_term = vm.pt.ns ? 0 : vm.pt.m * 30 + vm.pt.d;
      vm.data.delivery_term = vm.dt.ns ? 0 : vm.dt.m * 30 + vm.dt.d;

      axios.post('/api/sales/quotation', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          $('#modalCreate').modal('hide');
          vm.reload();
        }
      });
    },
    update: function () {
      if (!vm.validate()) return;

      var data = {
        id: vm.data.id,
        payment_term: vm.pt.ns ? 0 : vm.pt.m * 30 + vm.pt.d,
        delivery_term: vm.dt.ns ? 0 : vm.dt.m * 30 + vm.dt.d,
        construct_name: vm.data.construct_name,
        publish_date: vm.data.publish_date,
        expiry_day: vm.data.expiry_day,
        approval: vm.data.approval,
        memo: vm.data.memo
      };

      axios.patch('/api/sales/quotation', data).then(function (response) {
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
