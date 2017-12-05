<div id="sales-product" v-cloak>
  <div class="title">제품</div>

  <div class="text-right margin-bottom-1">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">등록</button>
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>모델명</th>
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
        <td>{{ item.model }}</td>
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
        <option value="model">모델명</option>
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
              <label class="col-sm-4 control-label">모델명</label>
              <div class="col-sm-8">
                <select id="option" class="form-control" v-model="indexProduct" @change="selectProduct()">
                  <option value="">선택하세요.</option>
                  <option v-for="(item, index) in creatableList" :value="index">{{ item.model }}</option>
                </select>
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
          <h4 class="modal-title" id="myModalLabel">원가 편집</h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">모델명</label>
              <div class="col-sm-8">
                <span class="form-control">{{ selectedProduct.model }}</span>
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
                <tr v-for="(item, index) in selectedProduct.prices">
                  <td>{{ item.start_date }}</td>
                  <td>{{ item.end_date }}</td>
                  <td>{{ item.price | number }}</td>
                  <td><input type="number" class="form-control" v-model="data2[index].sales_price"></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
            <button type="button" class="btn btn-primary" @click="create2()">저장</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#sales-product',
  data: {
    list: [],
    customers: [],
    creatableList: [],
    indexCustomer: '',
    selectedCustomer: {},
    indexProduct: '',
    selectedProduct: {},
    data: {},
    data2: [],
    search: 'model',
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
      vm.indexProduct = '';
      vm.selectedProduct = {};
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
    selectProduct: function () {
      if (vm.indexProduct !== '') {
        vm.selectedProduct = vm.creatableList[vm.indexProduct];
        vm.data.product_id = vm.selectedProduct.id;
      } else {
        vm.indexProduct = '';
        vm.selectedProduct = {};
        vm.data.product_id = '';
      }
    },
    edit: function (index) {
      vm.selectedProduct = JSON.parse(JSON.stringify(vm.list[index]));
      if (vm.selectedProduct.prices) {
        vm.selectedProduct.prices.forEach(function (item) {
          vm.data2.push({
            product_customer_id: vm.selectedProduct.id,
            product_id: vm.selectedProduct.product_id,
            product_price_id: item.id,
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
      axios.get('/api/sales/product_creatable/' + customer_id).then(function (response) {
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
      axios.get('/api/sales/product?' + params).then(function (response) {
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
      } else if (!vm.data.product_id) {
        alert('제품을 선택하세요.');
        $('#option').focus();
        return false;
      }

      return true;
    },
    create: function () {
      if (!vm.validate()) return;

      axios.post('/api/sales/product', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          $('#modalCreate').modal('hide');
          vm.reload();
        }
      });
    },
    create2: function () {
      axios.put('/api/sales/product_customer_price', vm.data2).then(function (response) {
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
