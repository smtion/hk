<div id="purchase-material-list">
  <div class="title">부자재 목록</div>

  <div class="text-right margin-bottom-1">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">등록</button>
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>부자재명</th>
        <th>부자재 상세</th>
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
          <h4 class="modal-title" id="myModalLabel">부자재 등록</h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">부자재명</label>
              <div class="col-sm-8">
                <select id="parts" class="form-control" v-model="option_index" @change="getOptionDetail2()" v-show="!data.id">
                  <option value="">선택하세요.</option>
                  <option v-for="(item, index) in options" :value="index">{{ item.name }}</option>
                </select>
                <span class="form-control" v-show="data.id">{{ data.name }}</span>
              </div>
            </div>
            <div class="form-group" v-for="item in option_detail2">
              <label class="col-sm-4 control-label">{{ item.type }}</label>
              <div class="col-sm-8">
                <select class="form-control" v-model="data.details[item.type]">
                  <option value="">선택하세요.</option>
                  <option v-for="(value, index) in item.values" :value="value">{{ value }}</option>
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
  el: '#purchase-material-list',
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
      $('#modalCreate').on('hidden.bs.modal', function () {
        vm.reset();
      })
      vm.getOptionName();
      vm.reload();
    },
    reset: function () {
      vm.data = {
        details: {},
      };
      vm.option_index = '';
      vm.option_detail2 = [];
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
      var tmp =  JSON.parse(JSON.stringify(vm.list[index]));
      vm.data = tmp;
      // vm.data.details = JSON.parse(tmp.details);

      var params = makeParams({
        'name': vm.data.name
      });
      axios.get('/api/purchase/material_detail2?' + params).then(function (response) {
        if (response.status == 200) {
          vm.option_detail2 = response.data.list;
        }
      });

      $('#modalCreate').modal('show');
    },
    getOptionName: function () {
      axios.get('/api/purchase/material_parts_name').then(function (response) {
        if (response.status == 200) {
          vm.options = response.data.list;
        }
      });
    },
    getOptionDetail2: function () {
      var params = makeParams({
        'name': vm.options[vm.option_index]['name']
      });
      axios.get('/api/purchase/material_detail2?' + params).then(function (response) {
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
      axios.get('/api/purchase/material_list?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    },
    validate: function () {
      if (!vm.data.id && vm.option_index === '') {
        alert('부자재 구성을 선택하세요.');
        $('#parts').focus();
        return false;
      }
      var flag = false;
      Object.keys(vm.data.details).forEach(function (v) {
        if (vm.data.details[v]) {
          flag = true;
        }
      });
      if (Object.keys(vm.data.details).length && !flag) {
        alert('부자재 구성 항목을 하나 이상 선택하세요.');
        return false;
      }

      return true;
    },
    create: function () {
      if (!vm.validate()) return;

      axios.post('/api/purchase/material_list', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          $('#modalCreate').modal('hide');
          vm.reload();
        }
      });
    },
    update: function () {
      if (!vm.validate()) return;

      axios.patch('/api/purchase/material_list', vm.data).then(function (response) {
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
