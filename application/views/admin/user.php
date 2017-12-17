<div id="admin-user" v-cloak>
  <div class="title">사용자 관리</div>

  <div class="text-right margin-bottom-1">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">사용자 등록</button>
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>이름</th>
        <th>부서</th>
        <th>팀명</th>
        <th>직급</th>
        <th>이메일</th>
        <th>전화번호</th>
        <th>휴대폰번호</th>
        <th>등록일</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in list">
        <td>{{ getNo(index) }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.dept_name }}</td>
        <td>{{ item.team }}</td>
        <td>{{ item.position_name }}</td>
        <td>{{ item.email }}</td>
        <td>{{ item.tel }}</td>
        <td>{{ item.phone }}</td>
        <td>{{ item.created_at | date }}</td>
        <td><button class="btn btn-default btn-sm" @click="edit(index)">편집</button></td>
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
        <option value="name">이름</option>
        <option value="dept">부서명</option>
        <option value="team">팀명</option>
        <option value="position">직급</option>
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
          <h4 class="modal-title" id="myModalLabel">사용자 <span v-show="!data.id">등록</span><span v-show="data.id">정보 변경</span></h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">이름</label>
              <div class="col-sm-8">
                <input id="name" type="text" class="form-control" v-model="data.name">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">임시 비밀번호</label>
              <div class="col-sm-8">
                <input id="password" type="password" class="form-control" placeholder="" v-model="data.password">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">관리자 권한</label>
              <div class="col-sm-8">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" v-model="data.admin" :true-value="1" :false-value="0"> 관리자
                  </label>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">부서</label>
              <div class="col-sm-8">
                <select id="dept_id" class="form-control" v-model="deptId">
                  <option value="">선택하세요.</option>
                  <option v-for="(item, index) in depts" :value="item.id" :selected="item.id == deptId">{{ item.name }}</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">팀명</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="" v-model="data.team">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">직급</label>
              <div class="col-sm-8">
                <select id="position_id" class="form-control" v-model="positionId">
                  <option value="">선택하세요.</option>
                  <option v-for="(item, index) in positions" :value="item.id" :selected="item.id == positionId">{{ item.name }}</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">이메일</label>
              <div class="col-sm-8">
                <input id="email" type="text" class="form-control" placeholder="" v-model="data.email">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">전화번호</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="" v-model="data.tel">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">휴대폰번호</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="" v-model="data.phone">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">접속권한</label>
              <div class="col-sm-8">
                <label class="checkbox-inline">
                  <input type="checkbox" v-model="data.permission.sales"> 영업
                </label>
                <label class="checkbox-inline">
                  <input type="checkbox" v-model="data.permission.purchase"> 구매
                </label>
                <label class="checkbox-inline">
                  <input type="checkbox" v-model="data.permission.production"> 생산
                </label>
                <label class="checkbox-inline">
                  <input type="checkbox" v-model="data.permission.finance"> 재무
                </label>
                <label class="checkbox-inline">
                  <input type="checkbox" v-model="data.permission.admin"> 인사
                </label>
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
  el: '#admin-user',
  data: {
    list: [],
    data: {
      permission: {},
    },
    permission: {},
    password: '',
    depts: [],
    positions: [],
    deptId: '',
    positionId: '',
    selectedDept: {},
    selectedPosition: {},
    search: 'name',
    keyword: '',
    paginate: {},
  },
  mounted: function () {
    this.$nextTick(function () {
      vm.init();
    });
  },
  methods: {
    init: function () {
      if (!vm.paginate.page) vm.paginate.page = 1;
      $('#modalCreate').on('hidden.bs.modal', function () {
        vm.reset();
      })
      $('#modalCreate').on('shown.bs.modal', function () {
        $('#name').focus();
      })

      vm.reload();
      vm.getDepts();
      vm.getPositions();
    },
    reset: function () {
      vm.data = {
        permission: {},
      };
      vm.permission = {};
      vm.deptId = '';
      vm.positionId = '';
      vm.selectedDept = {};
      vm.selectedPosition = {};
    },
    reload: function () {
      vm.reset();
      vm.getList(vm.paginate.page);
    },
    getNo: function (i) {
      return vm.paginate.total - ((vm.paginate.page - 1) * vm.paginate.limit) - i;
    },
    goPage: function (page) {
      vm.getList(page);
    },
    edit: function (index) {
      var tmp =  JSON.parse(JSON.stringify(vm.list[index]));
      vm.data.id = tmp.id;
      vm.data.name = tmp.name;
      vm.data.admin = tmp.admin;
      vm.data.dept_id = vm.deptId = tmp.dept_id;
      vm.data.position_id = vm.positionId = tmp.position_id;
      vm.data.team = tmp.team;
      vm.data.email = tmp.email;
      vm.data.tel = tmp.tel;
      vm.data.phone = tmp.phone;
      vm.data.permission = tmp.permission;

      $('#modalCreate').modal('show');
    },
    getDepts: function () {
      axios.get('/api/admin/user_dept').then(function (response) {
        if (response.status == 200) {
          vm.depts = response.data.list;
        }
      });
    },
    getPositions: function () {
      axios.get('/api/admin/user_position').then(function (response) {
        if (response.status == 200) {
          vm.positions = response.data.list;
        }
      });
    },
    getPermission: function () {
      // vm.permission
    },
    selectDept: function () {

    },
    getList: function (page) {
      if (!page) page = 1;

      var params = makeParams({
        page: page,
        search: vm.search,
        keyword: vm.keyword,
        // sort: vm.sort,
        // direction: vm.direction,
      });
      axios.get('/api/admin/user?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    },
    validate: function () {
      if (!vm.data.name) {
        alert('이름을 입력하세요');
        $('#name').focus();
        return false;
      } else if (!vm.data.password) {
        alert('임시 비밀번호를 입력하세요');
        $('#password').focus();
        return false;
      } else if (!vm.deptId) {
        alert('부서를 선택하세요');
        $('#dept_id').focus();
        return false;
      } else if (!vm.positionId) {
        alert('직급을 선택하세요');
        $('#position_id').focus();
        return false;
      } else if (!vm.data.email) {
        alert('이메일을 입력하세요');
        $('#email').focus();
        return false;
      }

      return true;
    },
    create: function () {
      if (!vm.validate()) return;

      vm.data.dept_id = vm.deptId;
      vm.data.position_id = vm.positionId;

      axios.post('/api/admin/user', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          $('#modalCreate').modal('hide');
          vm.reload();
        }
      });
    },
    update: function () {
      if (!vm.validate()) return;

      vm.data.dept_id = vm.deptId;
      vm.data.position_id = vm.positionId;

      if (vm.data.password) {
        if (!confirm('임시 비밀번호를 발급합니다.')) {
          return false;
        }
      }

      axios.patch('/api/admin/user', vm.data).then(function (response) {
        if (response.status == 200) {
          alert('변경되었습니다.');
          $('#modalCreate').modal('hide');
          vm.reload();
        }
      });
    }
  }
});
</script>
