<div id="sales-quotation-create">
  <div class="title">견적서 등록</div>

  <div>프로젝트 선택</div>

  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-2 form-control-static">적용 통화</label>
      <div class="col-sm-10">
        <label class="radio-inline">
          <input type="radio" value="kwn" v-model="data.currency"> 대한민국(원)
        </label>
        <label class="radio-inline">
          <input type="radio" value="cny" v-model="data.currency"> 중국 CNY
        </label>
        <label class="radio-inline">
          <input type="radio" value="jpy" v-model="data.currency"> 일본 JPY
        </label>
        <label class="radio-inline">
          <input type="radio" value="usd" v-model="data.currency"> 미국 USD
        </label>
        <label class="radio-inline">
          <input type="radio" value="eur" v-model="data.currency"> 유로 EUR
        </label>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 form-control-static">해광 통화</label>
      <div class="col-sm-4">
        <input type="number" class="form-control">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 form-control-static">견적 적용 환율</label>
      <div class="col-sm-4">
        <input type="number" class="form-control">
      </div>
    </div>
  </div>

  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-2 form-control-static">설명 </label>
      <div class="col-sm-10">
        <input type="text" class="form-control" v-model="data.desc">
      </div>
    </div>
  </div>

  <div class="margin-top-1 margin-bottom-1 pull-left" v-for="(set, index) in setCount">
    <hr>

    <br>
    <h4>제품</h4>
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-sm-2 form-control-static">Product</label>
        <div class="col-sm-4">
          <select class="form-control" v-model="indexProduct[index]">
            <option value="">선택하세요.</option>
            <option v-for="(item, index) in products" :value="index">{{ item.model }}</option>
          </select>
        </div>
        <div class="col-sm-2">
          <button class="btn btn-success" @click="selectProduct(index)">추가</button>
        </diV>
      </div>
    </div>
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>Model</th>
          <th>Type</th>
          <th>Dimension</th>
          <th>Concentrated Load 2mm delfection @ 1/2 edge</th>
          <th>Ultimate Load @ 1/2 edge</th>
          <th>Open Ratio(%)</th>
          <th>Conductivity</th>
          <th width="100">면적</th>
          <th width="100">수량</th>
          <th width="150">단가</th>
          <th width="150">금액</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, j) in selected[index]['products']">
          <td>{{ item.model }}</td>
          <td>{{ item.type | product_type }}</td>
          <td>{{ item.size }}</td>
          <td>{{ item.edge_cl }}</td>
          <td>{{ item.edge_ul }}</td>
          <td>{{ item.ph_ratio }}</td>
          <td></td>
          <td><input type="text" class="form-control" v-model="data.set[index]['products']['list'][j]['area']"></td>
          <td>
            <input type="text" class="form-control" style="margin-bottom: 5px;"
              @keyup="calulate(data.set[index]['products']['list'][j])"
              v-model="data.set[index]['products']['list'][j]['qty']">
            <div class="input-group">
              <input type="text" class="form-control"
                @keyup="calulate(data.set[index]['products']['list'][j])"
                v-model="data.set[index]['products']['list'][j]['dc_rate']">
              <span class="input-group-addon">%</span>
            </div>
          </td>
          <td>
            <input type="text" class="form-control" v-model="data.set[index]['products']['list'][j]['sales_price']" style="margin-bottom: 5px;" readonly>
            <input type="text" class="form-control" v-model="data.set[index]['products']['list'][j]['sales_price_dc']" readonly>
          </td>
          <td>
            <input type="text" class="form-control" v-model="data.set[index]['products']['list'][j]['total']" style="margin-bottom: 5px;">
            <input type="text" class="form-control" v-model="data.set[index]['products']['list'][j]['total_dc']">
          </td>
        </tr>
      </tbody>
    </table>

    <br>
    <h4>옵션</h4>
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-sm-2 form-control-static">Option</label>
        <div class="col-sm-4">
          <select class="form-control" v-model="indexOption[index]">
            <option value="">선택하세요.</option>
            <option v-for="(item, index) in options" :value="index">{{ item.name }}</option>
          </select>
        </div>
        <div class="col-sm-2">
          <button class="btn btn-success" @click="selectOption(index)">추가</button>
        </diV>
      </div>
    </div>
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>옵션명</th>
          <th>옵션상세</th>
          <th width="100">수량</th>
          <th width="150">단가</th>
          <th width="150">금액</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, j) in selected[index]['options']">
          <td>{{ item.name }}</td>
          <td class="outer-td">
            <div class="clearfix">
              <div class="pull-left inner-td" v-for="(v, k) in item.details" v-bind:style="{width: 100 / Object.keys(item.details).length + '%'}">
                <div>{{ k }}</div>
                <div>{{ v ? v : &nbsp; }}</div>
              </div>
            </div>
          </td>
          <td>
            <input type="text" class="form-control" style="margin-bottom: 5px;"
              @keyup="calulate(data.set[index]['options']['list'][j])"
              v-model="data.set[index]['options']['list'][j]['qty']">
            <div class="input-group">
              <input type="text" class="form-control"
                @keyup="calulate(data.set[index]['options']['list'][j])"
                v-model="data.set[index]['options']['list'][j]['dc_rate']">
              <span class="input-group-addon">%</span>
            </div>
          </td>
          <td>
            <input type="text" class="form-control" v-model="data.set[index]['options']['list'][j]['sales_price']" style="margin-bottom: 5px;" readonly>
            <input type="text" class="form-control" v-model="data.set[index]['options']['list'][j]['sales_price_dc']" readonly>
          </td>
          <td>
            <input type="text" class="form-control" v-model="data.set[index]['options']['list'][j]['total']" style="margin-bottom: 5px;">
            <input type="text" class="form-control" v-model="data.set[index]['options']['list'][j]['total_dc']">
          </td>
        </tr>
      </tbody>
    </table>

    <br>
    <h4>부자재</h4>
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-sm-2 form-control-static">Sub-material</label>
        <div class="col-sm-4">
          <select class="form-control" v-model="indexMaterial[index]">
            <option value="">선택하세요.</option>
            <option v-for="(item, index) in materials" :value="index">{{ item.name }}</option>
          </select>
        </div>
        <div class="col-sm-2">
          <button class="btn btn-success" @click="selectMaterial(index)">추가</button>
        </diV>
      </div>
    </div>
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>부자재명</th>
          <th>부자재상세</th>
          <th width="100">수량</th>
          <th width="150">단가</th>
          <th width="150">금액</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, j) in selected[index]['materials']">
          <td>{{ item.name }}</td>
          <td class="outer-td">
            <div class="clearfix">
              <div class="pull-left inner-td" v-for="(v, k) in item.details" v-bind:style="{width: 100 / Object.keys(item.details).length + '%'}">
                <div>{{ k }}</div>
                <div>{{ v ? v : &nbsp; }}</div>
              </div>
            </div>
          </td>
          <td>
            <input type="text" class="form-control" style="margin-bottom: 5px;"
              @keyup="calulate(data.set[index]['materials']['list'][j])"
              v-model="data.set[index]['materials']['list'][j]['qty']">
            <div class="input-group">
              <input type="text" class="form-control"
                @keyup="calulate(data.set[index]['materials']['list'][j])"
                v-model="data.set[index]['materials']['list'][j]['dc_rate']">
              <span class="input-group-addon">%</span>
            </div>
          </td>
          <td>
            <input type="text" class="form-control" v-model="data.set[index]['materials']['list'][j]['sales_price']" style="margin-bottom: 5px;" readonly>
            <input type="text" class="form-control" v-model="data.set[index]['materials']['list'][j]['sales_price_dc']" readonly>
          </td>
          <td>
            <input type="text" class="form-control" v-model="data.set[index]['materials']['list'][j]['total']" style="margin-bottom: 5px;">
            <input type="text" class="form-control" v-model="data.set[index]['materials']['list'][j]['total_dc']">
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div clss="margin-top-1">
    <button class="btn btn-primary" @click="addSet()">추가</button>
  </div>

  <div clss="margin-top-1">
    <button class="btn btn-primary" @click="save()">저장</button>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#sales-quotation-create',
  data: {
    a: 1,
    b: 2,
    c: '',
    id: $('#id').val(),
    data: {
      set: [],
    },
    selected: [
      {
        products: [],
        options: [],
        materials: [],
        costs: [],
      }
    ],
    products: [],
    options: [],
    materials: [],
    costs: [],

    setCount: 0,


    indexProduct: [''],
    indexOption: [''],
    indexMaterial: [''],
    indexCost: [''],
    selectedProduct: undefined,
    selectedOption: undefined,
    selectedMaterial: undefined,
    selectedCost: undefined,
    selectedOptions: [],
    selectedMaterials: [],
    selectedCosts: [],
  },
  methods: {
    init: function () {
      vm.data.id = vm.id;
      vm.reload();
      vm.addSet();
    },
    reload: function () {
      vm.getItem();
      vm.getProducts();
      vm.getOptions();
      vm.getMaterials();
      vm.getCosts();
    },
    addSet: function () {
      vm.setCount++;
      vm.indexProduct.push('');
      vm.indexOption.push('');
      vm.indexMaterial.push('');
      vm.indexCost.push('');
      vm.selected.push({products: [], options: [], materials: [], costs: []});
      vm.data.set.push({products: {total: 0, list: []}, options: {total: 0, list: []}, materials: {total: 0, list: []}, costs: {total: 0, list: []}});
    },
    selectProduct: function (index) {
      vm.selected[index]['products'].push(vm.products[vm.indexProduct[index]]);
      vm.data.set[index]['products']['list'].push({
        product_id: vm.products[vm.indexProduct[index]]['id'],
        sales_price: 3000,  // vm.products[vm.indexProduct[index]]['sales_price']
        qty: '',
        total: 0,
        total_dc: 0,
        sales_price_dc: 3000,  // vm.products[vm.indexProduct[index]]['sales_price']
        dc_rate: '',
      });
    },
    selectOption: function (index) {
      vm.selected[index]['options'].push(vm.options[vm.indexOption[index]]);
      vm.data.set[index]['options']['list'].push({
        option_id: vm.options[vm.indexOption[index]]['id'],
        sales_price: 3000,  // vm.products[vm.indexProduct[index]]['sales_price']
        qty: '',
        total: 0,
        total_dc: 0,
        sales_price_dc: 3000,  // vm.products[vm.indexProduct[index]]['sales_price']
        dc_rate: '',
      });
    },
    selectMaterial: function (index) {
      vm.selected[index]['materials'].push(vm.materials[vm.indexMaterial[index]]);
      vm.data.set[index]['materials']['list'].push({
        material_id: vm.materials[vm.indexMaterial[index]]['id'],
        sales_price: 3000,  // vm.products[vm.indexProduct[index]]['sales_price']
        qty: '',
        total: 0,
        total_dc: 0,
        sales_price_dc: 3000,  // vm.products[vm.indexProduct[index]]['sales_price']
        dc_rate: '',
      });
    },
    selectCost: function (index) {
      vm.selected[index]['costs'].push(vm.costs[vm.indexCost[index]]);
      vm.data.set[index]['costs'].push({
        cost_id: vm.costs[vm.indexCost[index]]['id'],
        sales_price: 3000,  // vm.products[vm.indexProduct[index]]['sales_price']
        qty: '',
        total: 0,
        total_dc: 0,
        sales_price_dc: 3000,  // vm.products[vm.indexProduct[index]]['sales_price']
        dc_rate: '',
      });
    },
    calulate: function (item) {
      item.total = item.qty * item.sales_price;
      item.sales_price_dc = item.sales_price * (100 - item.dc_rate) / 100;
      item.total_dc = item.qty * item.sales_price_dc;
    },
    getItem: function () {
      axios.get('/api/sales/quotation/' + vm.id).then(function (response) {
        if (response.status == 200) {
          vm.item = response.data.item;
          vm.selectedProduct = response.data.selected.product;
          vm.selectedOption = response.data.selected.option;
          vm.selectedMaterial = response.data.selected.material;
          vm.selectedCost = response.data.selected.cost;
          vm.data.product = response.data.data.product ? response.data.data.product : {};
          vm.data.option = response.data.data.option ? response.data.data.option : {};
          vm.data.material = response.data.data.material ? response.data.data.material : {};
          vm.data.cost = response.data.data.cost ? response.data.data.cost : {};
        }
      });
    },
    getProducts: function () {
      axios.get('/api/sales/quotation_products').then(function (response) {
        if (response.status == 200) {
          vm.products = response.data.list;
        }
      });
    },
    getOptions: function () {
      axios.get('/api/sales/quotation_options').then(function (response) {
        if (response.status == 200) {
          vm.options = response.data.list;
        }
      });
    },
    getMaterials: function () {
      axios.get('/api/sales/quotation_materials').then(function (response) {
        if (response.status == 200) {
          vm.materials = response.data.list;
        }
      });
    },
    getCosts: function () {
      axios.get('/api/sales/quotation_costs').then(function (response) {
        if (response.status == 200) {
          vm.costs = response.data.list;
        }
      });
    },
    save: function () {
      axios.put('/api/sales/quotation_detail', vm.data).then(function (response) {
        if (response.status == 200) {
          alert('저장되었습니다.');
        }
      });
    },
  }
});
vm.init();
</script>
