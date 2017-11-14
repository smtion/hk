 <div id="purchase-option-list">
  <div class="title">옵션 목록</div>

  <div class="text-right margin-bottom-1">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalOption">등록</button>
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>옵션명</th>
        <th>옵션 상세</th>
        <th>옵션</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in list">
        <td>{{ getNo(index) }}</td>
        <td>{{ item.name }}</td>
        <td class="outer-td">
          <div class="clearfix">
            <div class="pull-left inner-td" v-for="(v, k) in parseJson(item.details)" v-bind:style="{width: 100 / Object.keys(parseJson(item.details)).length + '%'}">
              <div>{{ k }}</div>
              <div>{{ v }}</div>
            </div>
          </div>
        </td>
        <td class="outer-td">
          <div class="clearfix">
            <div class="pull-left inner-td" style="width: 33.333333%">
              <div>Per'f</div>
              <div><input type="checkbox" v-model="item.perf" v-bind:true-value="1" v-bind:false-value="0" disabled></div>
            </div>
            <div class="pull-left inner-td" style="width: 33.333333%">
              <div>Grating</div>
              <div><input type="checkbox" v-model="item.grating" v-bind:true-value="1" v-bind:false-value="0" disabled></div>
            </div>
            <div class="pull-left inner-td" style="width: 33.333333%">
              <div>Blind</div>
              <div><input type="checkbox" v-model="item.blind" v-bind:true-value="1" v-bind:false-value="0" disabled></div>
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

  <div class="row">
    <div class="col-sm-offset-2 col-sm-2">
      <select class="form-control" v-model="search">
        <option value="name">옵션명</option>
        <option value="details">옵션상세</option>
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
  <div class="modal fade" id="modalOption" tabindex="-1" role="dialog" aria-labelledby="modalOptionLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">옵션 등록</h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">옵션명</label>
              <div class="col-sm-8">
                <select class="form-control" v-model="option_index" @change="getOptionDetail2()" v-show="!data.id">
                  <option value="">선택하세요.</option>
                  <option v-for="(item, index) in options" :value="index">{{ item.name }}</option>
                </select>
                <span class="form-control" v-show="data.id">{{ data.name }}</span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">옵션</label>
              <div class="col-sm-8">
                <label class="checkbox-inline">
                  <input type="checkbox" v-model="data.perf" v-bind:true-value="1" v-bind:false-value="0"> Per'f
                </label>
                <label class="checkbox-inline">
                  <input type="checkbox" v-model="data.grating" v-bind:true-value="1" v-bind:false-value="0"> Grating
                </label>
                <label class="checkbox-inline">
                  <input type="checkbox" v-model="data.blind" v-bind:true-value="1" v-bind:false-value="0"> Blind
                </label>
              </div>
            </div>
            <div class="form-group" v-for="item in option_detail2">
              <label class="col-sm-4 control-label">{{ item.type }}</label>
              <div class="col-sm-8">
                <select class="form-control" v-model="data.details[item.type]">
                  <option value="">선택하세요.</option>
                  <option v-for="(value, index) in parseJson(item.values)" :value="value">{{ value }}</option>
                </select>
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
  el: '#purchase-option-list',
  data: {
    list: [],
    data: {},
    options: [],
    option_detail2: [],
    option_index: '',
    search: 'name',
    keyword: '',
    paginate: {},
  },
  methods: {
    get: function () {
      return '50px';
    },
    init: function () {
      if (!vm.paginate.page) vm.paginate.page = 1;
      $('#modalOption').on('hidden.bs.modal', function () {
        vm.reset();
      })
      vm.getOptionName();
      vm.reload();
    },
    reset: function () {
      vm.data = {
        details: {},
      };
    },
    reload: function () {
      vm.reset();
      vm.getList(vm.paginate.page);
    },
    getNo: function (i) {
      return (vm.paginate.page - 1) * vm.paginate.limit + i + 1;
    },
    parseJson: function (s) {
      return JSON.parse(s);
    },
    parsedValues: function (e) {
      var d = JSON.parse(e);
      return d.join(', ');
    },
    goPage: function (page) {
      vm.getList(page);
    },
    edit: function (index) {
      var tmp =  JSON.parse(JSON.stringify(vm.list[index]));
      vm.data = tmp;
      vm.data.details = JSON.parse(tmp.details);

      var params = makeParams({
        'name': vm.data.name
      });
      axios.get('/api/purchase/option_detail2?' + params).then(function (response) {
        if (response.status == 200) {
          vm.option_detail2 = response.data.list;
        }
      });

      $('#modalOption').modal('show');
    },
    getOptionName: function () {
      axios.get('/api/purchase/option_name').then(function (response) {
        if (response.status == 200) {
          vm.options = response.data.list;
        }
      });
    },
    getOptionDetail2: function () {
      var params = makeParams({
        'name': vm.options[vm.option_index]['name']
      });
      axios.get('/api/purchase/option_detail2?' + params).then(function (response) {
        if (response.status == 200) {
          vm.option_detail2 = response.data.list;

          vm.data.name = vm.options[vm.option_index]['name'];
          vm.data.details = {};
          vm.option_detail2.map(function (item, index) {
            // vm.data.details[index] = {};
            vm.data.details[item.type] = '';
          });
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
      axios.get('/api/purchase/option_list?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    },
    create: function () {
      axios.post('/api/purchase/option_list', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          $('#modalOption').modal('hide');
          vm.reload();
        }
      });
    },
    update: function () {
      axios.patch('/api/purchase/option_list', vm.data).then(function (response) {
        if (response.status == 200) {
          alert('변경되었습니다.');
          $('#modalOption').modal('hide');
          vm.reload();
        }
      });
    }
  }
});
vm.init();
</script>
