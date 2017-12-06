<div id="admin-activity">
  <div class="title">사용자 활동 정보</div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>이름</th>
        <th>부서명</th>
        <th>직급</th>
        <th>메뉴</th>
        <th>활동</th>
        <th>활동 일시</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in list">
        <td>{{ getNo(index) }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.dept_name }}</td>
        <td>{{ item.position_name }}</td>
        <td>{{ item.class | class }}</td>
        <td>{{ item.function | function }}</td>
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
Vue.filter('class', function (value) {
  if (value == 'Purchase') return '구매팀';
  else if (value == 'Sales') return '영업팀';
  else if (value == 'Finance') return '재무팀';
  else if (value == 'Production') return '제품팀';
  else if (value == 'Admin') return '관리자';
  else if (value == 'Mypage') return '마이페이지';
  else return '???';
});
Vue.filter('function', function (value) {
  var list = {
    aluminum_get: '알류미늄 시세 조회',
    aluminum_put: '알류미늄 가격 등록',
    option_parts_get: '옵션 구성 상세 조회',
    option_parts_post: '옵션 구성 상세 등록',
    option_parts_patch: '옵션 구성 상세 변경',
    option_list_get: '옵션 목록 조회',
    option_list_post: '옵션 목록 등록',
    option_list_patch: '옵션 목록 변경',
    option_price_get: '옵션 가격 조회',
    option_price_post: '옵션 가격 등록',
    material_parts_get: '부자재 구성 상세 조회',
    material_parts_post: '부자재 구성 상세 등록',
    material_parts_patch: '부자재 구성 상세 변경',
    material_list_get: '부자재 목록 조회',
    material_list_post: '부자재 목록 등록',
    material_list_patch: '부자재 목록 변경',
    material_price_get: '부자재 가격 조회',
    material_price_post: '부자재 가격 등록',
    cost_parts_get: '간접비 구성 상세 조회',
    cost_parts_post: '간접비 구성 상세 등록',
    cost_parts_patch: '간접비 구성 상세 변경',
    cost_list_get: '간접비 목록 조회',
    cost_list_post: '간접비 목록 등록',
    cost_list_patch: '간접비 목록 변경',
    cost_price_get: '간접비 가격 조회',
    cost_price_post: '간접비 가격 등록',
    product_list_get: '제품 목록 조회',
    product_list_post: '제품 목록 등록',
    product_list_patch: '제품 목록 변경',
    product_price_get: '제품 가격 조회',
    product_price_post: '제품 가격 등록',

    currency_get: '환율 조회',
    currency_put: '환율 등록',
    option_get: '고객사 옵션 조회',
    option_post: '고객사 옵션 등록',
    option_customer_price_put: '고객사 옵션 가격 등록',
    material_get: '고객사 부자재 조회',
    material_post: '고객사 부자재 등록',
    material_customer_price_put: '고객사 부자재 가격 등록',
    cost_get: '고객사 간접비 조회',
    cost_post: '고객사 간접비 등록',
    cost_customer_price_put: '고객사 간접비 가격 등록',
    product_get: '고객사 제품 조회',
    product_post: '고객사 제품 등록',
    product_customer_price_put: '고객사 제품 가격 등록',
    customer_get: '고객사 정보 조회',
    customer_post: '고객사 정보 등록',
    customer_patch: '고객사 정보 변경',
    project_get: '프로젝트 조회',
    project_post: '프로젝트 등록',
    project_patch: '프로젝트 변경',
    quotation_get: '견적서 조회',
    quotation_post: '견적서 등록',
    quotation_patch: '견적서 변경',
    quotation_detail_post: '견적서 상세 견적 등록',

    user_get: '사용자 정보 조회',
    user_post: '사용자 정보 등록',
    user_patch: '사용자 정보 변경',
    dept_get: '부서 정보 조회',
    dept_post: '부서 정보 등록',
    dept_patch: '부서 정보 변경',
    position_get: '직급 정보 조회',
    position_post: '직급 정보 등록',
    position_patch: '직급 정보 변경',
    login_get: '사용자 로그인 정보 조회',
    activity_get: '사용자 활동 정보 조회',
    company_get: '회사 정보 조회',
    company_put: '회사 정보 변경',

    info_get: '내정보 조회',
    info_patch: '내정보 변경',
    user_list_get: '사용자 목록 조회',
  };

  return list[value] ? list[value] : value;
});
var vm = new Vue({
  el: '#admin-activity',
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
      axios.get('/api/admin/activity?' + params).then(function (response) {
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
