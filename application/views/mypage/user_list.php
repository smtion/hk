<div id="mypage-user-list" v-cloak>
  <div class="title">사용자 관리</div>

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
      <button class="btn btn-primary btn-block" @click="goPage()">검색</button>
    </div>
  </form>
</div>

<script>
var vm = new Vue({
  el: '#mypage-user-list',
  data: {
    list: [],
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
      axios.get('/api/mypage/user_list?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    }
  }
});
</script>
