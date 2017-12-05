<div id="admin-login">
  <div class="title">사용자 접속 정보</div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>이름</th>
        <th>부서명</th>
        <th>직급</th>
        <th>IP</th>
        <th>로그인 일시</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in list">
        <td>{{ getNo(index) }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.dept_name }}</td>
        <td>{{ item.position_name }}</td>
        <td>{{ item.ip }}</td>
        <td>{{ item.created_at }}</td>
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

  <form class="row"  @submit.prevent>
    <div class="col-sm-offset-2 col-sm-2">
      <select class="form-control" v-model="search">
        <option value="name">이름</option>
        <option value="dept">부서명</option>
      </select>
    </div>
    <div class="col-sm-4">
      <input type="text" class="form-control" v-model="keyword">
    </div>
    <div class="col-sm-2">
      <button class="btn btn-primary btn-block" @click="goPage(1)">검색</button>
    </div>
  </form>
</div>

<script>
var vm = new Vue({
  el: '#admin-login',
  data: {
    list: [],
    search: 'name',
    keyword: '',
    paginate: {},
  },
  methods: {
    init: function () {
      vm.reload();
    },
    reload: function () {
      vm.getList(vm.paginate.page);
    },
    getNo: function (i) {
      return vm.paginate.total - ((vm.paginate.page - 1) * vm.paginate.limit) - i;
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
        // sort: vm.sort,
        // direction: vm.direction,
      });
      axios.get('/api/admin/login?' + params).then(function (response) {
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
