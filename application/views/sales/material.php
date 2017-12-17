<div id="sales-material" v-cloak>
  <div class="title">부자재</div>

  <div class="text-right margin-bottom-1">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">등록</button>
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>부자재명</th>
        <th>부자재 상세</th>
        <th>고객사</th>
        <th>적용 시작일</th>
        <th>적용 종료일</th>
        <th>원가</th>
        <th>영업가격</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in list">
        <td>{{ getNo(index) }}</td>
        <td>{{ item.name }}</td>
        <td class="outer-td">
          <div class="clearfix">
            <div class="pull-left inner-td" v-for="(v, k) in item.details" v-bind:style="{width: 100 / Object.keys(item.details).length + '%'}">
              <div>{{ k }}</div>
              <div>{{ v ? v : '&nbsp;' }}</div>
            </div>
          </div>
        </td>
        <td>{{ item.corp_name }}</td>
        <td class="outer-td">
          <div class="inner-td">
            <div v-for="price in item.prices.slice(0, 2)">
              {{ price.start_date }}
            </div>
          </div>
        </td>
        <td class="outer-td">
          <div class="inner-td">
            <div v-for="price in item.prices.slice(0, 2)">
              {{ price.end_date }}
            </div>
          </div>
        </td>
        <td class="outer-td">
          <div class="inner-td">
            <div v-for="price in item.prices.slice(0, 2)">
              {{ price.price | number }}
            </div>
          </div>
        </td>
        <td class="outer-td">
          <div class="inner-td">
            <div v-for="price in item.prices.slice(0, 2)">
             {{ price.sales_price | number }}
            </div>
          </div>
        </td>
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
        <option value="name">부자재명</option>
        <option value="details">부자재상세</option>
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
          <h4 class="modal-title" id="myModalLabel">신규 고객사 등록</h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">고객사</label>
              <div class="col-sm-8">
                <select id="customer" class="form-control" v-model="indexCustomer" @change="selectCustomer()">
                  <option value="">선택하세요.</option>
                  <option v-for="(item, index) in customers" :value="index">{{ item.corp_name }}</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">부자재명</label>
              <div class="col-sm-8">
                <select id="option" class="form-control" v-model="indexMaterial" @change="selectMaterial()">
                  <option value="">선택하세요.</option>
                  <option v-for="(item, index) in creatableList" :value="index">{{ item.name }}</option>
                </select>
              </div>
            </div>
            <div class="form-group" v-for="(v, k) in selectedMaterial.details" v-show="selectedMaterial">
              <label class="col-sm-4 control-label">{{ k }}</label>
              <div class="col-sm-8">
                <span class="form-control">{{ v }}</span>
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

  <!-- Modal -->
  <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">영업가격 편집</h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">고객사명</label>
              <div class="col-sm-8">
                <span class="form-control">{{ selectedMaterial.corp_name }}</span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">부자재명</label>
              <div class="col-sm-8">
                <span class="form-control">{{ selectedMaterial.name }}</span>
              </div>
            </div>
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>적용 시작일</th>
                  <th>적용 종료일</th>
                  <th>원가 (원)</th>
                  <th>영업가격 (원)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in selectedMaterial.prices">
                  <td>{{ item.start_date }}</td>
                  <td>{{ item.end_date }}</td>
                  <td>{{ item.price | number }}</td>
                  <td><input type="number" class="form-control" v-model="data2[index].sales_price"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
          <button type="button" class="btn btn-primary" @click="create2()">저장</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#sales-material',
  data: {
    list: [],
    customers: [],
    creatableList: [],
    indexCustomer: '',
    selectedCustomer: {},
    indexMaterial: '',
    selectedMaterial: {},
    data: {},
    data2: [],
    search: 'name',
    keyword: '',
    paginate: {},
  },
  methods: {
    init: function () {
      if (!vm.paginate.page) vm.paginate.page = 1;
      $('#modalCreate').on('hidden.bs.modal', function () {
        vm.reset();
      });
      $('#modalEdit').on('hidden.bs.modal', function () {
        vm.reset();
      });
      vm.reload();
    },
    reset: function () {
      vm.data = {};
      vm.data2 = [];
      vm.indexCustomer = '';
      vm.selectedCustomer = {};
      vm.indexMaterial = '';
      vm.selectedMaterial = {};
    },
    reload: function () {
      vm.reset();
      vm.getList(vm.paginate.page);
      vm.getCustomer();
    },
    getNo: function (i) {
      return vm.paginate.total - ((vm.paginate.page - 1) * vm.paginate.limit) - i;
    },
    goPage: function (page) {
      vm.getList(page);
    },
    selectCustomer: function () {
      if (vm.indexCustomer !== '') {
        vm.selectedCustomer = vm.customers[vm.indexCustomer];
        vm.data.customer_id = vm.selectedCustomer.id;
        vm.getCreatableList(vm.data.customer_id);
      } else {
        vm.reset();
      }
    },
    selectMaterial: function () {
      if (vm.indexMaterial !== '') {
        vm.selectedMaterial = vm.creatableList[vm.indexMaterial];
        vm.data.material_id = vm.selectedMaterial.id;
      } else {
        vm.indexMaterial = '';
        vm.selectedMaterial = {};
        vm.data.material_id = '';
      }
    },
    edit: function (index) {
      vm.selectedMaterial = JSON.parse(JSON.stringify(vm.list[index]));
      if (vm.selectedMaterial.prices) {
        vm.selectedMaterial.prices.forEach(function (item) {
          vm.data2.push({
            material_customer_id: vm.selectedMaterial.id,
            material_id: vm.selectedMaterial.material_id,
            material_price_id: item.id,
            sales_price: item.sales_price,
          });
        });
      }
      $('#modalEdit').modal('show');
    },
    getCustomer: function () {
      axios.get('/api/sales/customer_creatable').then(function (response) {
        if (response.status == 200) {
          vm.customers = response.data.list;
        }
      });
    },
    getCreatableList: function (customer_id) {
      axios.get('/api/sales/material_creatable/' + customer_id).then(function (response) {
        if (response.status == 200) {
          vm.creatableList = response.data.list;
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
      axios.get('/api/sales/material?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    },
    validate: function () {
      if (!vm.data.customer_id) {
        alert('고객사를 선택하세요.');
        $('#customer').focus();
        return false;
      } else if (!vm.data.material_id) {
        alert('부자재를 선택하세요.');
        $('#option').focus();
        return false;
      }

      return true;
    },
    create: function () {
      if (!vm.validate()) return;

      axios.post('/api/sales/material', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          $('#modalCreate').modal('hide');
          vm.reload();
        }
      });
    },
    create2: function () {
      axios.put('/api/sales/material_customer_price', vm.data2).then(function (response) {
        if (response.status == 200) {
          alert('등록되었습니다.');
          $('#modalEdit').modal('hide');
          vm.reload();
        }
      });
    }
  }
});
vm.init();
</script>
