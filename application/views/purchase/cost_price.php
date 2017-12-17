<div id="purchase-cost-price" v-cloak>
  <div class="title">간접비 가격</div>

  <div class="text-right margin-bottom-1">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">등록</button>
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>간접비명</th>
        <th>간접비 상세</th>
        <th>적용 시작일</th>
        <th>적용 종료일</th>
        <th>원가</th>
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
        <td class="outer-td">
          <div class="inner-td" >
            <div v-for="price in item.prices.slice(0, 2)">
              {{ price.start_date }}
            </div>
          </div>
        </td>
        <td class="outer-td">
          <div class="inner-td" >
            <div v-for="price in item.prices.slice(0, 2)">
              {{ price.end_date }}
            </div>
          </div>
        </td>
        <td class="outer-td">
          <div class="inner-td" >
            <div v-for="price in item.prices.slice(0, 2)">
              {{ price.price | number }}
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
        <option value="name">간접비명</option>
        <option value="details">간접비 상세</option>
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
          <h4 class="modal-title" id="myModalLabel">신규 원가 등록</h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">간접비명</label>
              <div class="col-sm-8">
                <select class="form-control" v-model="indexCost" @change="selectCost()">
                  <option value="">선택하세요.</option>
                  <option v-for="(item, index) in creatableList" :value="index">{{ item.name }}</option>
                </select>
                <!-- <span class="form-control">{{ selectedCost.name }}</span> -->
              </div>
            </div>
            <div class="form-group" v-for="(v, k) in selectedCost.details" v-show="selectedCost">
              <label class="col-sm-4 control-label">{{ k }}</label>
              <div class="col-sm-8">
                <span class="form-control">{{ v }}</span>
              </div>
            </div>
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>적용 시작일</th>
                  <th>적용 종료일</th>
                  <th>원가 (원)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><input type="date" class="form-control" v-model="data.start_date"></td>
                  <td><input type="date" class="form-control" v-model="data.end_date"></td>
                  <td><input type="number" class="form-control" v-model="data.price"></td>
                </tr>
                <tr v-for="item in selectedCost.prices">
                  <td>{{ item.start_date }}</td>
                  <td>{{ item.end_date }}</td>
                  <td>{{ item.price | number }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
          <button type="button" class="btn btn-primary" @click="create()">등록</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#purchase-cost-price',
  data: {
    list: [],
    creatableList: [],
    indexCost: '',
    selectedCost: {},
    data: {},
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
      vm.indexCost = '';
      vm.selectedCost = {};
    },
    reload: function () {
      vm.reset();
      vm.getList(vm.paginate.page);
      vm.getCreatableList();
    },
    getNo: function (i) {
      return vm.paginate.total - ((vm.paginate.page - 1) * vm.paginate.limit) - i;
    },
    getPrices: function (p) {
      if (typeof p == 'object') return p.slice(0, 1);
    },
    goPage: function (page) {
      vm.getList(page);
    },
    selectCost: function () {
      vm.selectedCost = vm.creatableList[vm.indexCost];
      vm.data.cost_id = vm.selectedCost.id;
    },
    edit: function (index) {
      vm.selectedCost = vm.list[index];
      vm.data.cost_id = vm.selectedCost.id;
      $('#modalCreate').modal('show');
    },
    getCreatableList: function () {
      axios.get('/api/purchase/cost_price_creatable').then(function (response) {
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
      axios.get('/api/purchase/cost_price?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    },
    create: function () {
      if (!vm.selectedCost.id) {
        alert('등록할 간접비를 선택하세요.');
        return;
      }
      if (!vm.data.start_date || !vm.data.end_date || !vm.data.price) {
        alert('입력값을 확인하세요.');
        return;
      }
      if (vm.data.start_date == today()) {
        alert('적용시작일은 당일로 설정할 수 없습니다.')
        return;
      }
      if (vm.selectedCost.prices) {
        var validate = true;
        vm.selectedCost.prices.forEach(function (price) {
          if (price.end_date >= vm.data.start_date) {
            alert('적용시작일은 ' + price.end_date + ' 이후로만 가능합니다.');
            validate = false;
            return;
          }
        });
        if (!validate) return;
      }

      axios.post('/api/purchase/cost_price', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          $('#modalCreate').modal('hide');
          $('#modalEdit').modal('hide');
          vm.reload();
        }
      });
    }
  }
});
vm.init();
</script>
