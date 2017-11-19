<div id="purchase-product-price">
 <div class="title">제품 원가</div>

 <div class="text-right margin-bottom-1">
   <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">등록</button>
 </div>

 <table class="table table-striped table-bordered">
   <thead>
     <tr>
       <th>No</th>
       <th>모델명</th>
       <th>적용 시작일</th>
       <th>적용 종료일</th>
       <th>원가</th>
       <th></th>
     </tr>
   </thead>
   <tbody>
     <tr v-for="(item, index) in list">
       <td>{{ getNo(index) }}</td>
       <td>{{ item.model }}</td>
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
             {{ parseInt(price.price).toLocaleString() }}
           </div>
         </div>
       </td>
       <td><span class="pointer" @click="add(index)">편집</span></td>
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
         <h4 class="modal-title" id="myModalLabel">신규 원가 등록</h4>
       </div>
       <div class="modal-body">
         <div class="form-horizontal">
           <div class="form-group">
             <label class="col-sm-4 control-label">모델명</label>
             <div class="col-sm-8">
               <select class="form-control" v-model="option_index" @change="selectOption()">
                 <option value="">선택하세요.</option>
                 <option v-for="(item, index) in creatable_list" :value="index">{{ item.model }}</option>
               </select>
             </div>
           </div>
           <div class="form-group" v-for="(v, k) in selected_option.details" v-show="selected_option">
             <label class="col-sm-4 control-label">{{ k }}</label>
             <div class="col-sm-8">
               <span class="form-control">{{ v }}</span>
             </div>
           </div>
           <div class="form-group text-center">
             <div class="col-sm-4">
               <label>적용 시작일</label>
               <input type="date" class="form-control" v-model="data.prices[0].start_date">
             </div>
             <div class="col-sm-4">
               <label>적용 종료일</label>
               <input type="date" class="form-control" v-model="data.prices[0].end_date">
             </div>
             <div class="col-sm-4">
               <label>원가 (원)</label>
               <input type="number" class="form-control" v-model="data.prices[0].price">
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
 <div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labelledby="modalAddLabel">
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
               <span class="form-control">{{ selected_option.model }}</span>
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
               <tr v-show="is_add">
                 <td><input type="date" class="form-control" v-model="data.prices[0].start_date"></td>
                 <td><input type="date" class="form-control" v-model="data.prices[0].end_date"></td>
                 <td><input type="number" class="form-control" v-model="data.prices[0].price"></td>
               </tr>
               <tr v-for="item in selected_option.prices">
                 <td>{{ item.start_date }}</td>
                 <td>{{ item.end_date }}</td>
                 <td>{{ (item.price).toLocaleString() }}</td>
               </tr>
             </tbody>
           </table>
         </div>
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-warning" @click="is_add = true">+ 신규</button>
         <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
         <button type="button" class="btn btn-primary" @click="create()">저장</button>
       </div>
     </div>
   </div>
 </div>
</div>

<script>
var vm = new Vue({
 el: '#purchase-product-price',
 data: {
   list: [],
   creatable_list: [],
   selected_option: {},
   selected_price: [],
   is_add: false,
   data: {
     prices: [{}],
   },
   options: [],
   option_detail2: [],
   option_index: '',
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
     $('#modalAdd').on('hidden.bs.modal', function () {
       vm.reset();
     });
     vm.reload();
   },
   reset: function () {
     vm.data = {
       prices: [{}],
     };
     vm.selected_option = {};
     vm.is_add = false;
   },
   reload: function () {
     vm.reset();
     vm.getList(vm.paginate.page);
     vm.getCreatableList();
   },
   getNo: function (i) {
     return (vm.paginate.page - 1) * vm.paginate.limit + i + 1;
   },
   getPrices: function (p) {
     if (typeof p == 'object') return p.slice(0, 1);
   },
   goPage: function (page) {
     vm.getList(page);
   },
   selectOption: function () {
     vm.selected_option = vm.creatable_list[vm.option_index];
   },
   add: function (index) {
     vm.selected_option = vm.list[index];
     $('#modalAdd').modal('show');
   },
   getCreatableList: function () {
     axios.get('/api/purchase/product_price_creatable').then(function (response) {
       if (response.status == 200) {
         vm.creatable_list = response.data.list;
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
     axios.get('/api/purchase/product_price?' + params).then(function (response) {
       if (response.status == 200) {
         vm.list = response.data.list;
         vm.paginate = response.data.paginate;
       }
     });
   },
   create: function () {
     vm.data.id = vm.selected_option.id;
     if (vm.selected_option.prices) vm.data.prices = vm.data.prices.concat(vm.selected_option.prices);

     axios.patch('/api/purchase/product_price', vm.data).then(function (response) {
       if (response.status == 200) {
         alert('등록되었습니다.');
         $('#modalCreate').modal('hide');
         $('#modalAdd').modal('hide');
         vm.reload();
       }
     });
   }
 }
});
vm.init();
</script>
