<div id="sales-quotation">
  <input id="id" type="hidden" value="<?=$id?>">
  <div class="title">견적서</div>

  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-2 form-control-static">Product</label>
      <div class="col-sm-4">
        <select class="form-control" v-model="indexProduct" @change="selectProduct()">
          <option value="">선택하세요.</option>
          <option v-for="(item, index) in products" :value="index">{{ item.model }}</option>
        </select>
      </div>
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
        <th width="80">Q'ty</th>
        <th width="80">Unit</th>
        <th width="120">Unit Price</th>
        <th width="120">Extensions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-if="selectedProduct">
        <td>{{ selectedProduct.model }}</td>
        <td>{{ selectedProduct.type | product_type }}</td>
        <td>{{ selectedProduct.size }}</td>
        <td>{{ selectedProduct.edge_cl }}</td>
        <td>{{ selectedProduct.edge_ul }}</td>
        <td>{{ selectedProduct.ph_ratio }}</td>
        <td></td>
        <td><input type="number" class="form-control" v-model="data.product.qty"></td>
        <td><input type="text" class="form-control" v-model="data.product.unit"></td>
        <td><input type="text" class="form-control" v-model="data.product.unit_price"></td>
        <td><input type="text" class="form-control" v-model="data.product.ext"></td>
      </tr>
    </tbody>
  </table>

  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-2 form-control-static">Option</label>
      <div class="col-sm-4">
        <select class="form-control" v-model="indexOption" @change="selectOption()">
          <option value="">선택하세요.</option>
          <option v-for="(item, index) in options" :value="index">{{ item.name }}</option>
        </select>
      </div>
    </div>
  </div>
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>옵션명</th>
        <th>옵션상세</th>
        <th width="80">Q'ty</th>
        <th width="80">Unit</th>
        <th width="120">Unit Price</th>
        <th width="120">Extensions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-if="selectedOption">
        <td>{{ selectedOption.name }}</td>
        <td class="outer-td">
          <div class="clearfix">
            <div class="pull-left inner-td" v-for="(v, k) in selectedOption.details" v-bind:style="{width: 100 / Object.keys(selectedOption.details).length + '%'}">
              <div>{{ k }}</div>
              <div>{{ v }}</div>
            </div>
          </div>
        </td>
        <td><input type="number" class="form-control" v-model="data.option.qty"></td>
        <td><input type="text" class="form-control" v-model="data.option.unit"></td>
        <td><input type="text" class="form-control" v-model="data.option.unit_price"></td>
        <td><input type="text" class="form-control" v-model="data.option.ext"></td>
      </tr>
    </tbody>
  </table>

  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-2 form-control-static">Sub-material</label>
      <div class="col-sm-4">
        <select class="form-control" v-model="indexMaterial" @change="selectMaterial()">
          <option value="">선택하세요.</option>
          <option v-for="(item, index) in materials" :value="index">{{ item.name }}</option>
        </select>
      </div>
    </div>
  </div>
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>부자재명</th>
        <th>부자재상세</th>
        <th width="80">Q'ty</th>
        <th width="80">Unit</th>
        <th width="120">Unit Price</th>
        <th width="120">Extensions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-if="selectedMaterial">
        <td>{{ selectedMaterial.name }}</td>
        <td class="outer-td">
          <div class="clearfix">
            <div class="pull-left inner-td" v-for="(v, k) in selectedMaterial.details" v-bind:style="{width: 100 / Object.keys(selectedMaterial.details).length + '%'}">
              <div>{{ k }}</div>
              <div>{{ v }}</div>
            </div>
          </div>
        </td>
        <td><input type="number" class="form-control" v-model="data.material.qty"></td>
        <td><input type="text" class="form-control" v-model="data.material.unit"></td>
        <td><input type="text" class="form-control" v-model="data.material.unit_price"></td>
        <td><input type="text" class="form-control" v-model="data.material.ext"></td>
      </tr>
    </tbody>
  </table>

  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-2 form-control-static">Indirect Cost</label>
      <div class="col-sm-4">
        <select class="form-control" v-model="indexCost" @change="selectCost()">
          <option value="">선택하세요.</option>
          <option v-for="(item, index) in costs" :value="index">{{ item.name }}</option>
        </select>
      </div>
    </div>
  </div>
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>간접비명</th>
        <th>간접비상세</th>
        <th width="80">Q'ty</th>
        <th width="80">Unit</th>
        <th width="120">Unit Price</th>
        <th width="120">Extensions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-if="selectedCost">
        <td>{{ selectedCost.name }}</td>
        <td class="outer-td">
          <div class="clearfix">
            <div class="pull-left inner-td" v-for="(v, k) in selectedCost.details" v-bind:style="{width: 100 / Object.keys(selectedCost.details).length + '%'}">
              <div>{{ k }}</div>
              <div>{{ v }}</div>
            </div>
          </div>
        </td>
        <td><input type="number" class="form-control" v-model="data.cost.qty"></td>
        <td><input type="text" class="form-control" v-model="data.cost.unit"></td>
        <td><input type="text" class="form-control" v-model="data.cost.unit_price"></td>
        <td><input type="text" class="form-control" v-model="data.cost.ext"></td>
      </tr>
    </tbody>
  </table>

  <div clss="margin-top-1">
    <button class="btn btn-primary" @click="save()">저장</button>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#sales-quotation',
  data: {
    id: $('#id').val(),
    data: {
      product: {},
      option: {},
      material: {},
      cost: {},
    },
    products: [],
    options: [],
    materials: [],
    costs: [],
    indexProduct: '',
    indexOption: '',
    indexMaterial: '',
    indexCost: '',
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
    },
    reload: function () {
      vm.getItem();
      vm.getProducts();
      vm.getOptions();
      vm.getMaterials();
      vm.getCosts();
    },
    selectProduct: function () {
      vm.selectedProduct = vm.products[vm.indexProduct];
      vm.data.product.type_id = vm.selectedProduct.id;
    },
    selectOption: function () {
      vm.selectedOption = vm.options[vm.indexOption];
      vm.data.option.type_id = vm.selectedOption.id;
    },
    selectMaterial: function () {
      vm.selectedMaterial = vm.materials[vm.indexMaterial];
      vm.data.material.type_id = vm.selectedMaterial.id;
    },
    selectCost: function () {
      vm.selectedCost = vm.costs[vm.indexCost];
      vm.data.cost.type_id = vm.selectedCost.id;
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
