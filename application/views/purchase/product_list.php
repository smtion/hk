 <div id="purchase-product-list">
  <div class="title">제품 목록</div>

  <div class="text-right margin-bottom-1">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">등록</button>
  </div>

  <div style="overflow: scroll">
    <table class="table table-striped table-bordered" style="width: 2000px">
      <thead>
        <tr>
          <th>No</th>
          <th>Model</th>
          <th>Size</th>
          <th>Grade</th>
          <th>Type</th>
          <th width="240" class="outer-td">
            <div class="inner-td">
              <div>
                1/2Edge (kgf)
              </div>
              <div class="clearfix">
                <div class="pull-left" style="width: 50%">
                  Ultimate Load
                </div>
                <div class="pull-left" style="width: 50%">
                  Concentrate Load @ 2mm
                </div>
              </div>
            </div>
          </th>
          <th width="240" class="outer-td">
            <div class="inner-td">
              <div>
                Center (kgf)
              </div>
              <div class="clearfix">
                <div class="pull-left" style="width: 50%">
                  Ultimate Load
                </div>
                <div class="pull-left" style="width: 50%">
                  Concentrate Load @ 2mm
                </div>
              </div>
            </div>
          </th>
          <th width="120">Uniform Loada @max 1mm (kgf/m2)</th>
          <th width="320" class="outer-td">
            <div class="inner-td">
              <div>
                Per'f Hole
              </div>
              <div class="clearfix">
                <div class="pull-left" style="width: 25%">
                  øPress
                </div>
                <div class="pull-left" style="width: 25%">
                  øDrill
                </div>
                <div class="pull-left" style="width: 25%">
                  #
                </div>
                <div class="pull-left" style="width: 25%">
                  Ratio (%)
                </div>
              </div>
            </div>
          </th>
          <th>무게 (kg)</th>
          <th width="100">Bare 시공부 높이</th>
          <th width="120">완제품 최대 높이 (2T Tie 기준)</th>
          <th>Main Rib</th>
          <th>비고</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in list">
          <td>{{ getNo(index) }}</td>
          <td>{{ item.model }}</td>
          <td>{{ item.size }}</td>
          <td>{{ item.grade }}</td>
          <td>{{ item.type }}</td>
          <td>
            <div class="clearfix">
              <div class="pull-left" style="width: 50%">
                {{ item.edge_ul }}
              </div>
              <div class="pull-left" style="width: 50%">
                {{ item.edge_cl }}
              </div>
            </div>
          </td>
          <td>
            <div class="clearfix">
              <div class="pull-left" style="width: 50%">
                {{ item.center_ul }}
              </div>
              <div class="pull-left" style="width: 50%">
                {{ item.center_cl }}
              </div>
            </div>
          </td>
          <td>{{ item.ul_max }}</td>
          <td>
            <div class="clearfix">
              <div class="pull-left" style="width: 25%">
                {{ item.ph_press }}
              </div>
              <div class="pull-left" style="width: 25%">
                {{ item.ph_drill }}
              </div>
              <div class="pull-left" style="width: 25%">
                {{ item.ph_num }}
              </div>
              <div class="pull-left" style="width: 25%">
                {{ item.ph_ratio }}
              </div>
            </div>
          </td>
          <td>{{ item.weight }}</td>
          <td>{{ item.bare_height }}</td>
          <td>{{ item.max_height }}</td>
          <td>{{ item.main_rib }}</td>
          <td>{{ item.memo }}</td>
          <td><span class="pointer" @click="edit(index)">편집</span></td>
        </tr>
      </tbody>
    </table>
  </div>

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

  <div class="row">
    <div class="col-sm-offset-2 col-sm-2">
      <select class="form-control" v-model="search">
        <option value="model">모델명</option>
      </select>
    </div>
    <div class="col-sm-4">
      <input type="text" class="form-control" v-model="keyword">
    </div>
    <div class="col-sm-2">
      <button class="btn btn-primary btn-block" @click="goPage()">검색</button>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="modalCreateLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">제품 등록</h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">Model</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.model">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Size</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.size">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Grade</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.grade">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Type</label>
              <div class="col-sm-8">
                <label class="radio-inline">
                  <input type="radio" value="1" v-model="data.type"> Per'f
                </label>
                <label class="radio-inline">
                  <input type="radio" value="2" v-model="data.type"> Grating
                </label>
                <label class="radio-inline">
                  <input type="radio" value="3" v-model="data.type"> Blind
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">1/2 Edge Ultimate Load</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.edge_ul">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">1/2 Edge Concentrated Load @ 2mm</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.edge_cl">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Center Ultimate Load</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.center_ul">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Center Concentrated Load @ 2mm</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.center_cl">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Uniform Load @ max 1.0mm</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.ul_max">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Per'f Hole Press</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.ph_press">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Per'f Hole Drill</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.ph_drill">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Per'f Hole #</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.ph_num">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Per'f Hole Ratio (%)</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.ph_ratio">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">무게</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.weight">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Bare 시공부 높이</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.bare_height">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">완제품 최대높이</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.max_height">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">Main Rib</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.main_rib">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">비고</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" v-model="data.memo">
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
  el: '#purchase-product-list',
  data: {
    list: [],
    data: {},
    options: [],
    option_detail2: [],
    option_index: '',
    search: 'model',
    keyword: '',
    paginate: {},
  },
  methods: {
    get: function () {
      return '50px';
    },
    init: function () {
      if (!vm.paginate.page) vm.paginate.page = 1;
      $('#modalCreate').on('hidden.bs.modal', function () {
        vm.reset();
      })
      vm.reload();
    },
    reset: function () {
      vm.data = {};
      vm.option_index = '';
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
      vm.data =  JSON.parse(JSON.stringify(vm.list[index]));
      $('#modalCreate').modal('show');
    },
    getList: function (page) {
      if (!page) page = 1;

      var params = makeParams({
        page: page,
        search: vm.search,
        keyword: vm.keyword,
      });
      axios.get('/api/purchase/product_list?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    },
    create: function () {
      axios.post('/api/purchase/product_list', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          $('#modalCreate').modal('hide');
          vm.reload();
        }
      });
    },
    update: function () {
      axios.patch('/api/purchase/product_list', vm.data).then(function (response) {
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
