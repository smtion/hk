<div id="sales-customer" v-cloak>
  <div class="title">고객 관리</div>

  <div class="text-right margin-bottom-1">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">등록</button>
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>회사이름</th>
        <th>국가</th>
        <th>담당자 이름</th>
        <th>회사번호</th>
        <th>Email 주소</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in list">
        <td>{{ getNo(index) }}</td>
        <td>{{ item.corp_name }}</td>
        <td>{{ item.country }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.tel }}</td>
        <td>{{ item.email }}</td>
        <td><span class="pointer" @click="edit(index)">편집</span></td>
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
        <option value="name">담당자 이름</option>
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
          <h4 class="modal-title" id="myModalLabel">고객 등록</h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">회사이름 한글</label>
              <div class="col-sm-8">
                <input id="corp_name" type="text" class="form-control" v-model="data.corp_name">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">회사이름 영문</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.corp_name_en">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">국가</label>
              <div class="col-sm-8">
                <input id="country" type="phone" class="form-control" v-model="data.country">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">사업자등록번호</label>
              <div class="col-sm-8">
                <input id="regno" type="text" class="form-control" v-model="data.regno">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">업종</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.type">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">업태</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.subtype">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">담당자 이름 국문</label>
              <div class="col-sm-3">
                <input id="name" type="text" class="form-control" v-model="data.name">
              </div>
              <div class="col-sm-3">
                <select class="form-control" v-model="pos" @change="selectPosition()">
                  <option value="">선택하세요.</option>
                  <option value="사원">사원</option>
                  <option value="대리">대리</option>
                  <option value="과장">과장</option>
                  <option value="차장">차장</option>
                  <option value="부장">부장</option>
                  <option value="이사">이사</option>
                  <option value="대표">대표</option>
                  <option value="0">직접입력</option>
                </select>
              </div>
              <div class="col-sm-2">
                <input type="text" class="form-control" v-show="(pos === '0')" v-model="data.position">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">담당자 이름 영문</label>
              <div class="col-sm-3">
                <input type="text" class="form-control" v-model="data.name_en">
              </div>
              <div class="col-sm-3">
                <select class="form-control" v-model="pos_en" @change="selectPosition()">
                  <option value="">선택하세요.</option>
                  <option value="Assistant">Assistant</option>
                  <option value="Manager">Manager</option>
                  <option value="Director">Director</option>
                  <option value="President">President</option>
                  <option value="0">직접입력</option>
                </select>
              </div>
              <div class="col-sm-2">
                <input type="text" class="form-control" v-show="(pos_en === '0')" v-model="data.position_en">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">회사번호</label>
              <div class="col-sm-8">
                <input id="tel" type="phone" class="form-control" v-model="data.tel">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">핸드폰번호</label>
              <div class="col-sm-8">
                <input id="phone" type="phone" class="form-control" v-model="data.phone">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">이메일주소</label>
              <div class="col-sm-8">
                <input id="email" type="email" class="form-control" v-model="data.email">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">주소</label>
              <div class="col-sm-8">
                <label class="radio-inline">
                  <input type="radio" value="ab" v-model="addr"> 빌링
                </label>
                <label class="radio-inline">
                  <input type="radio" value="ad" v-model="addr"> 배송지
                </label>
                <label class="radio-inline">
                  <input type="radio" value="ae" v-model="addr"> 기타
                </label>
              </div>
            </div>
            <div v-show="addr == 'ab'">
              <div class="form-group">
                <label class="col-sm-4 control-label">한글 주소</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ab_addr">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">한글 상세 주소</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ab_addr_detail">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">우편번호</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ab_zipcode">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">Address 1</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ab_addr1_en">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">Address 2</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ab_addr2_en">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">City</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ab_city_en">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">Country</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ab_country_en">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">Zip code</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ab_zipcode">
                </div>
              </div>
            </div>
            <div v-show="addr == 'ad'">
              <div class="form-group">
                <label class="col-sm-4 control-label">한글 주소</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ad_addr">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">한글 상세 주소</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ad_addr_detail">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">우편번호</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ad_zipcode">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">Address 1</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ad_addr1_en">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">Address 2</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ad_addr2_en">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">City</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ad_city_en">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">Country</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ad_country_en">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">Zip code</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ad_zipcode">
                </div>
              </div>
            </div>
            <div v-show="addr == 'ae'">
              <div class="form-group">
                <label class="col-sm-4 control-label">한글 주소</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ae_addr">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">한글 상세 주소</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ae_addr_detail">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">우편번호</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ae_zipcode">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">Address 1</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ae_addr1_en">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">Address 2</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ae_addr2_en">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">City</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ae_city_en">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">Country</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ae_country_en">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">Zip code</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" v-model="data.ae_zipcode">
                </div>
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
  el: '#sales-customer',
  data: {
    data: {},
    list: {},
    pos: '',
    pos_en: '',
    addr: 'ab',
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
    selectPosition: function () {
      if (vm.pos !== '0') {
        vm.data.position = vm.pos;
      }
      if (vm.pos_en !== '0') {
        vm.data.position_en = vm.pos_en;
      }
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
      axios.get('/api/sales/customer?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    },
    validate: function () {
      if (!vm.data.corp_name) {
        alert('회사이름을 입력하세요.');
        $('#corp_name').focus();
        return false;
      } else if (!vm.data.country) {
        alert('국가를 입력하세요.');
        $('#country').focus();
        return false;
      } else if (!vm.data.regno) {
        alert('사업자등록번호를 입력하세요.');
        $('#regno').focus();
        return false;
      } else if (!vm.data.name) {
        alert('담장자 이름을 입력하세요.');
        $('#name').focus();
        return false;
      } else if (!vm.data.tel) {
        alert('회사번호를 입력하세요.');
        $('#tel').focus();
        return false;
      } else if (!vm.data.phone) {
        alert('휴대폰번호를 입력하세요.');
        $('#phone').focus();
        return false;
      } else if (!vm.data.email) {
        alert('이메일주소를 입력하세요.');
        $('#email').focus();
        return false;
      }

      return true;
    },
    create: function () {
      if (!vm.validate()) return;

      axios.post('/api/sales/customer', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          $('#modalCreate').modal('hide');
          vm.reload();
        }
      });
    },
    update: function () {
      if (!vm.validate()) return;

      axios.patch('/api/sales/customer', vm.data).then(function (response) {
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
