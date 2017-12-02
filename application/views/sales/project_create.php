<div id="sales-project-create" v-cloak>
  <div class="title">프로젝트 등록</div>

  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-4 control-label">프로젝트 이름</label>
      <div class="col-sm-6">
        <input type="text" class="form-control" v-model="data.name">
      </div>
      <div class="col-sm-2">
        <button class="btn btn-primary" @click="check()">중복확인</button>
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
      <div class="col-sm-6">
        <span class="form-control">{{ selectedCustomer.corp_name }}</span>
      </div>
      <div class="col-sm-2">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalSearch">찾기</button>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">회사이름 영문</label>
      <div class="col-sm-6">
        <span class="form-control">{{ selectedCustomer.corp_name_en }}</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">전화번호1</label>
      <div class="col-sm-8">
        <span class="form-control">{{ selectedCustomer.tel1 }}</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">전화번호2</label>
      <div class="col-sm-8">
        <span class="form-control">{{ selectedCustomer.tel2 }}</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">FAX 번호</label>
      <div class="col-sm-8">
        <span class="form-control">{{ selectedCustomer.fax }}</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Email 주소</label>
      <div class="col-sm-8">
        <span class="form-control">{{ selectedCustomer.email }}</span>
      </div>
    </div>
  </div>

  <div class="text-center margin-top-1">
    <button class="btn btn-primary" @click="create()">등록</button>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modalSearch" tabindex="-1" role="dialog" aria-labelledby="modalSearchLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">회사 검색</h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" @submit.prevent="search()">
            <div class="form-group">
              <label class="col-sm-4 control-label">회사 이름</label>
              <div class="col-sm-6">
                <input id="keyword" type="text" class="form-control" v-model="keyword">
              </div>
              <div class="col-sm-2">
                <button class="btn btn-primary">찾기</button>
              </div>
            </div>
          </form>
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>선택</th>
                <th>No</th>
                <th>회사이름</th>
                <th>국가</th>
                <th>담당자</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in list">
                <td><input type="radio" :value="index" v-model="indexCustomer"></td>
                <td>{{ getNo(index) }}</td>
                <td>{{ item.corp_name }}</td>
                <td>{{ item.country }}</td>
                <td>{{ item.name }}</td>
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
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
          <button type="button" class="btn btn-primary" @click="select()">선택</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#sales-project-create',
  data: {
    data: {},
    list: [],
    indexCustomer: '',
    selectedCustomer: {},
    keyword: '',
    paginate: {},
  },
  methods: {
    init: function () {
      if (!vm.paginate.page) vm.paginate.page = 1;
      $('#modalSearch').on('shown.bs.modal', function () {
        $('#keyword').focus();
      });
    },
    reset: function () {
      vm.data = {};
    },
    reload: function () {
      vm.reset();
    },
    getNo: function (i) {
      return vm.paginate.total - ((vm.paginate.page - 1) * vm.paginate.limit) - i;
    },
    goPage: function (page) {
      vm.search(page);
    },
    check: function () {
      var params = makeParams({name: vm.data.name});

      axios.get('/api/sales/project_check?' + params).then(function (response) {
        if (response.status == 200) {
          if (response.data.result) {
            alert('사용 가능합니다.');
          } else {
            alert('이미 존재하는 이름입니다.');
          }
        }
      });
    },
    search: function (page) {
      if (!vm.keyword) {
        alert("검색어를 입력하세요.");
        return;
      }

      if (!page) page = 1;

      var params = makeParams({
        page: page,
        keyword: vm.keyword
      });

      axios.get('/api/sales/project_search?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    },
    select: function () {
      vm.selectedCustomer = vm.list[vm.indexCustomer];
      vm.data.customer_id = vm.selectedCustomer.id;
      $('#modalSearch').modal('hide');
    },
    edit: function (index) {
      vm.data = JSON.parse(JSON.stringify(vm.list[index]));
      $('#modalSearch').modal('show');
    },
    create: function () {
      axios.post('/api/sales/project', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          location.href = "/sales/project";
        }
      });
    },
    update: function () {
      axios.patch('/api/sales/project', vm.data).then(function (response) {
        if (response.status == 200) {
          alert('변경되었습니다.');
          $('#modalSearch').modal('hide');
          vm.reload();
        }
      });
    }
  }
});
vm.init();
</script>
