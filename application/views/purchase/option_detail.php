<div id="purchase-option-detail">
  <div class="title">옵션 상세</div>

  <div class="text-right margin-bottom-1">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalOption">등록</button>
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>옵션 구성 코드</th>
        <th>옵션 구성 명칭</th>
        <th>옵션 구성 구분</th>
        <th>옵션 구성 항목</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in list">
        <td>{{ item.id }}</td>
        <td>{{ item.code }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.type }}</td>
        <td>{{ parsedValues(item.values) }}</td>
        <td><span @click="edit(index)">편집</span></td>
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
        <option value="code">옵션 구성 코드</option>
        <option value="name">옵션 구성 명칭</option>
        <option value="type">옵션 구성 구분</option>
        <option value="values">옵션 구성 항목</option>
      </select>
    </div>
    <div class="col-sm-4">
      <input type="text" class="form-control" v-model="keyword">
    </div>
    <div class="col-sm-2">
      <button class="btn btn-primary btn-block" @click="goPage(1)">검색</button>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modalOption" tabindex="-1" role="dialog" aria-labelledby="modalOptionLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">옵션 상세 등록</h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-4 control-label">옵션 구성 코드</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="시스템 자동 생성" v-model="data.code" disabled>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">옵션 구성 명칭</label>
              <div class="col-sm-8">
                <select name="" class="form-control" v-model="option_index">
                  <option value="">사용자 입력</option>
                  <option v-for="(item, index) in options" :value="index">{{ item.name }}</option>
                </select>
                <input type="text" class="form-control" placeholder="" v-model="data.name" v-show="isNaN(parseInt(option_index))">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">옵션 구성 구분</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="" v-model="data.type">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">옵션 구성 항목</label>
              <div class="col-sm-8">
                <div class="input-group margin-top-between-1" v-for="(item, index) in data.values">
                  <input type="text" class="form-control" placeholder="" v-model="data.values[index]">
                  <span class="input-group-btn"><button class="btn btn-default" @click="removeOptionValue(index)">X</button></span>
                </div>
                <div class="text-center margin-top-1">
                  <button class="btn btn-primary btn-sm" @click="addOptionValue()">추가</button>
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
  el: '#purchase-option-detail',
  data: {
    list: [],
    data: {
      values: ['']
    },
    options: [],
    option_index: '',
    search: 'code',
    keyword: '',
    sort: '',
    direction: '',
    paginate: {},
  },
  methods: {
    init: function () {
      if (!vm.paginate.page) vm.paginate.page = 1;
      $('#modalOption').on('hidden.bs.modal', function () {
        vm.reset();
      })
      vm.reload();
    },
    reset: function () {
      vm.data = {
        values: [''],
      };
    },
    reload: function () {
      vm.reset();
      vm.getList(vm.paginate.page);
      vm.getOptionName();
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
      vm.data.values = JSON.parse(tmp.values);

      $('#modalOption').modal('show');
    },
    addOptionValue: function () {
      vm.data.values.push('');
    },
    removeOptionValue: function (index) {
      vm.data.values.splice(index, 1);
    },
    getOptionName: function () {
      axios.get('/api/purchase/option_name').then(function (response) {
        if (response.status == 200) {
          vm.options = response.data.list;
        }
      });
    },
    getList: function (page) {
      if (!page) page = 1;

      var params = makeParams({
        page: page,
        search: vm.search,
        keyword: vm.keyword,
        sort: vm.sort,
        direction: vm.direction,
      });
      axios.get('/api/purchase/option_detail?' + params).then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.paginate = response.data.paginate;
        }
      });
    },
    create: function () {
      vm.data.option = isNaN(parseInt(vm.option_index)) ? {} : vm.options[vm.option_index];

      axios.post('/api/purchase/option_detail', vm.data).then(function (response) {
        if (response.status == 201) {
          alert('등록되었습니다.');
          $('#modalOption').modal('hide');
          vm.reload();
        }
      });
    },
    update: function () {
      // vm.data.option = isNaN(parseInt(vm.option_index)) ? {} : vm.options[vm.option_index];

      axios.patch('/api/purchase/option_detail', vm.data).then(function (response) {
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