<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Sales extends REST_Controller {

  function __construct()
  {
    parent::__construct();

    if (!is_logged_on()) {
      $this->response(NULL, REST_Controller::HTTP_UNAUTHORIZED);
    }
  }

  //-----------------------------------------------------------------------
  // Currency
  //-----------------------------------------------------------------------
  public function currency_get()
	{
    $prev1Date = date('Y-m-d', strtotime('-1 days', time()));
    $prev2Date = date('Y-m-d', strtotime('-2 days', time()));
    $cny = $this->db->where('currency', 'cny')->where_in('date', [$prev1Date, $prev2Date])->order_by('date')->get('HK_currency')->result_array();
    $jpy = $this->db->where('currency', 'jpy')->where_in('date', [$prev1Date, $prev2Date])->order_by('date')->get('HK_currency')->result_array();
    $usd = $this->db->where('currency', 'usd')->where_in('date', [$prev1Date, $prev2Date])->order_by('date')->get('HK_currency')->result_array();
    $eur = $this->db->where('currency', 'eur')->where_in('date', [$prev1Date, $prev2Date])->order_by('date')->get('HK_currency')->result_array();

    $data_tmp = $this->db->where('date', today())->get('HK_currency')->result_array();
    $data = [];
    foreach ($data_tmp as $i) {
      $data[$i['currency']] = $i['exchange'];
    }

    $list = [
      'cny' => $cny,
      'jpy' => $jpy,
      'usd' => $usd,
      'eur' => $eur,
    ];

    $list = array_map(function ($item) {
      $tmp = [];
      foreach ($item as $i) {
        $tmp[$i['date']] = $i['exchange'];
      }

      return $tmp;
    }, $list);

    $response = [
      'list' => $list,
      'data' => $data
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function currency_put()
  {
    $data = $this->put();

    foreach ($data['currency'] as $cur => $ex) {
      if ($this->db->where('currency', $cur)->where('date', $data['date'])->count_all_results('HK_currency')) {
        $this->db->where('currency', $cur)->where('date', $data['date'])->update('HK_currency', [
          'exchange' => $ex
        ]);
      } else {
        $this->db->insert('HK_currency', [
          'currency' => $cur,
          'date' => $data['date'],
          'exchange' => $ex
        ]);
      }
    }

    $this->response(null, REST_Controller::HTTP_OK);
  }



  //-----------------------------------------------------------------------
  // Common
  //-----------------------------------------------------------------------
  public function customer_creatable_get()
	{
    $list = $this->db->order_by('corp_name ASC, name ASC')->get('HK_customers')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}



  //-----------------------------------------------------------------------
  // Option
  //-----------------------------------------------------------------------
  public function option_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    $this->db->select('oc.*, o.name, o.details, c.corp_name')->from('HK_option_customer oc')
      ->join('HK_customers c', 'oc.customer_id = c.id')->join('HK_option o', 'oc.option_id = o.id');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      if ($search == 'name') {
        $this->db->like('o.name', $keyword);
      } elseif ($search == 'corp_name') {
        $this->db->like('c.corp_name', $keyword);
      }
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->order_by('oc.id desc')->get(null, $limit, $offset)->result_array();
    $this->db->flush_cache();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      $item['prices'] = $this->db->select('p.*, cp.sales_price')->from('HK_option_price p')
                        ->join('HK_option_customer_price cp', ' cp.option_price_id = p.id AND cp.option_customer_id = ' . $item['id'], 'left outer')
                        ->where('p.option_id', $item['option_id'])
                        ->order_by('start_date desc')->get()->result_array();
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function option_creatable_get($id)
	{
    if (!$id) {
      $this->response(null, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
    }

    $list = $this->db->select('o.*')->from('HK_option o')
            ->join('HK_option_customer c', 'c.customer_id = ' . $id . ' AND c.option_id = o.id', 'left outer')
            ->where('c.id IS NULL')->group_by('o.id')->order_by('o.id desc')->get()->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function option_post()
  {
    $data = $this->post();
    $result = $this->db->insert('HK_option_customer', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function option_customer_price_put()
  {
    $datas = $this->put();

    foreach ($datas as $data) {
      if ($this->db->where([
          'option_customer_id' => $data['option_customer_id'],
          'option_id' => $data['option_id'],
          'option_price_id' => $data['option_price_id']
        ])->count_all_results('HK_option_customer_price')
      ) {
        $this->db->where([
          'option_customer_id' => $data['option_customer_id'],
          'option_id' => $data['option_id'],
          'option_price_id' => $data['option_price_id']])->update('HK_option_customer_price', $data);
      } else {
        $this->db->insert('HK_option_customer_price', $data);
      }
    }
    // $result = $this->db->where('id', $data['id'])->update('HK_option_customer_price', $data);
    $result = true;
    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }



  //-----------------------------------------------------------------------
  // Material
  //-----------------------------------------------------------------------
  public function material_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    $this->db->select('mc.*, o.name, o.details, c.corp_name')->from('HK_material_customer mc')
      ->join('HK_customers c', 'mc.customer_id = c.id')->join('HK_material o', 'mc.material_id = o.id');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      if ($search == 'name') {
        $this->db->like('o.name', $keyword);
      } elseif ($search == 'corp_name') {
        $this->db->like('c.corp_name', $keyword);
      }
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->order_by('mc.id desc')->get(null, $limit, $offset)->result_array();
    $this->db->flush_cache();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      $item['prices'] = $this->db->select('p.*, cp.sales_price')->from('HK_material_price p')
                        ->join('HK_material_customer_price cp', ' cp.material_price_id = p.id AND cp.material_customer_id = ' . $item['id'], 'left outer')
                        ->where('p.material_id', $item['material_id'])
                        ->order_by('start_date desc')->get()->result_array();
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function material_creatable_get($id)
	{
    if (!$id) {
      $this->response(null, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
    }

    $list = $this->db->select('m.*')->from('HK_material m')
            ->join('HK_material_customer c', 'c.customer_id = ' . $id . ' AND c.material_id = m.id', 'left outer')
            ->where('c.id IS NULL')->group_by('m.id')->order_by('m.id desc')->get()->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function material_post()
  {
    $data = $this->post();
    $this->db->insert('HK_material_customer', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function material_customer_price_put()
  {
    $datas = $this->put();

    foreach ($datas as $data) {
      if ($this->db->where([
          'material_customer_id' => $data['material_customer_id'],
          'material_id' => $data['material_id'],
          'material_price_id' => $data['material_price_id']
        ])->count_all_results('HK_material_customer_price')
      ) {
        $this->db->where([
          'material_customer_id' => $data['material_customer_id'],
          'material_id' => $data['material_id'],
          'material_price_id' => $data['material_price_id']])->update('HK_material_customer_price', $data);
      } else {
        $this->db->insert('HK_material_customer_price', $data);
      }
    }
    // $result = $this->db->where('id', $data['id'])->update('HK_option_customer_price', $data);
    $result = true;
    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }



  //-----------------------------------------------------------------------
  // Cost
  //-----------------------------------------------------------------------
  public function cost_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    $this->db->select('mc.*, o.name, o.details, c.corp_name')->from('HK_cost_customer mc')
      ->join('HK_customers c', 'mc.customer_id = c.id')->join('HK_cost o', 'mc.cost_id = o.id');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      if ($search == 'name') {
        $this->db->like('o.name', $keyword);
      } elseif ($search == 'corp_name') {
        $this->db->like('c.corp_name', $keyword);
      }
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->order_by('mc.id desc')->get(null, $limit, $offset)->result_array();
    $this->db->flush_cache();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      $item['prices'] = $this->db->select('p.*, cp.sales_price')->from('HK_cost_price p')
                        ->join('HK_cost_customer_price cp', ' cp.cost_price_id = p.id AND cp.cost_customer_id = ' . $item['id'], 'left outer')
                        ->where('p.cost_id', $item['cost_id'])
                        ->order_by('start_date desc')->get()->result_array();
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function cost_creatable_get($id)
	{
    if (!$id) {
      $this->response(null, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
    }

    $list = $this->db->select('m.*')->from('HK_cost m')
            ->join('HK_cost_customer c', 'c.customer_id = ' . $id . ' AND c.cost_id = m.id', 'left outer')
            ->where('c.id IS NULL')->group_by('m.id')->order_by('m.id desc')->get()->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function cost_post()
  {
    $data = $this->post();
    $this->db->insert('HK_cost_customer', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function cost_customer_price_put()
  {
    $datas = $this->put();

    foreach ($datas as $data) {
      if ($this->db->where([
          'cost_customer_id' => $data['cost_customer_id'],
          'cost_id' => $data['cost_id'],
          'cost_price_id' => $data['cost_price_id']
        ])->count_all_results('HK_cost_customer_price')
      ) {
        $this->db->where([
          'cost_customer_id' => $data['cost_customer_id'],
          'cost_id' => $data['cost_id'],
          'cost_price_id' => $data['cost_price_id']])->update('HK_cost_customer_price', $data);
      } else {
        $this->db->insert('HK_cost_customer_price', $data);
      }
    }
    // $result = $this->db->where('id', $data['id'])->update('HK_option_customer_price', $data);
    $result = true;
    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }



  //-----------------------------------------------------------------------
  // Product
  //-----------------------------------------------------------------------
  public function product_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    $this->db->select('mc.*, p.model, c.corp_name')->from('HK_product_customer mc')
      ->join('HK_customers c', 'mc.customer_id = c.id')->join('HK_product p', 'mc.product_id = p.id');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      if ($search == 'name') {
        $this->db->like('p.name', $keyword);
      } elseif ($search == 'corp_name') {
        $this->db->like('c.corp_name', $keyword);
      }
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->order_by('mc.id desc')->get(null, $limit, $offset)->result_array();
    $this->db->flush_cache();

    $list = array_map(function ($item) {
      // $item['details'] = json_decode($item['details']);
      $item['prices'] = $this->db->select('p.*, cp.sales_price')->from('HK_product_price p')
                        ->join('HK_product_customer_price cp', ' cp.product_price_id = p.id AND cp.product_customer_id = ' . $item['id'], 'left outer')
                        ->where('p.product_id', $item['product_id'])
                        ->order_by('start_date desc')->get()->result_array();
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function project_check_get()
  {
    if (!$name = $this->get('name')) {
      $this->response(null, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
    }

    $result = $this->db->where('name', $name)->count_all_results('HK_projects');

    $response = [
      'result' => !$result
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function project_search_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if ($keyword = $this->get('keyword')) {
      $this->db->start_cache();
      $this->db->like('corp_name', $keyword);
      $this->db->or_like('corp_name_en', $keyword);
      $this->db->stop_cache();
    }

    $total = $this->db->count_all_results('HK_customers');
    $list = $this->db->get('HK_customers')->result_array();

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function product_creatable_get($id)
	{
    if (!$id) {
      $this->response(null, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
    }

    $list = $this->db->select('p.*')->from('HK_product p')
            ->join('HK_product_customer c', 'c.customer_id = ' . $id . ' AND c.product_id = p.id', 'left outer')
            ->where('c.id IS NULL')->group_by('p.id')->order_by('p.id desc')->get()->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function product_post()
  {
    $data = $this->post();
    $this->db->insert('HK_product_customer', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function product_customer_price_put()
  {
    $datas = $this->put();

    foreach ($datas as $data) {
      if ($this->db->where([
          'product_customer_id' => $data['product_customer_id'],
          'product_id' => $data['product_id'],
          'product_price_id' => $data['product_price_id']
        ])->count_all_results('HK_product_customer_price')
      ) {
        $this->db->where([
          'product_customer_id' => $data['product_customer_id'],
          'product_id' => $data['product_id'],
          'product_price_id' => $data['product_price_id']])->update('HK_product_customer_price', $data);
      } else {
        $this->db->insert('HK_product_customer_price', $data);
      }
    }
    // $result = $this->db->where('id', $data['id'])->update('HK_option_customer_price', $data);
    $result = true;
    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }



  //-----------------------------------------------------------------------
  // Customer
  //-----------------------------------------------------------------------
  public function customer_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->like($search, $keyword);
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results('HK_customers');
    $list = $this->db->order_by('id desc')->get('HK_customers', $limit, $offset)->result_array();

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function customer_post()
  {
    $data = $this->post();
    $result = $this->db->insert('HK_customers', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function customer_patch()
  {
    $data = $this->patch();
    $result = $this->db->where('id', $data['id'])->update('HK_customers', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }



  //-----------------------------------------------------------------------
  // Project
  //-----------------------------------------------------------------------
  public function project_get($id = 0)
  {
    if ($id) {
      $item = $this->db->select('*, p.name project_name')->from('HK_projects p')->join('HK_customers c', 'p.customer_id = c.id')
              ->where('p.id', $id)->get()->row_array();

      $response = [
        'item' => $item
      ];
    } else {
      $page = $this->get('page');
      $limit = 10;
      $offset = ($page - 1) * $limit;

      $this->db->start_cache();
      $this->db->from('HK_projects p')->join('HK_customers c', 'p.customer_id = c.id');
      if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
        if ($search == 'name') {
          $this->db->like('p.name', $keyword);
        } elseif ($search == 'corp_name') {
          $this->db->like('c.corp_name', $keyword);
        }

      }
      $this->db->stop_cache();

      $total = $this->db->count_all_results();
      $list = $this->db->select('p.*, c.corp_name')->order_by('p.id desc')->get(null, $limit, $offset)->result_array();

      $response = [
        'paginate' => [
          'total' => $total,
          'page' => $page,
          'limit' => $limit
        ],
        'list' => $list
      ];
    }

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function project_post()
  {
    $data = $this->post();
    $result = $this->db->insert('HK_projects', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function project_patch()
  {
    $data = $this->patch();
    $result = $this->db->where('id', $data['id'])->update('HK_projects', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }



  //-----------------------------------------------------------------------
  // Quotation
  //-----------------------------------------------------------------------
  public function quotation_get($id = 0)
  {
    if ($id) {
      $item = $this->db->from('HK_quotations q')->join('HK_projects p', 'q.project_id = p.id')->where('q.id', $id)->get()->row_array();

      $response = [
        'item' => $item,
        // 'selected' => [],
        // 'data' => [],
      ];

      // $details = $this->db->where('quotation_id', $id)->get('HK_quotation_detail')->result_array();
      //
      // foreach ($details as $item) {
      //   if ($item['type'] == 'product') {
      //     $table = 'HK_product';
      //   } else if ($item['type'] == 'option') {
      //     $table = 'HK_option_list';
      //   } else if ($item['type'] == 'material') {
      //     $table = 'HK_material_list';
      //   } else if ($item['type'] == 'cost') {
      //     $table = 'HK_cost_list';
      //   }
      //
      //   $detail = $this->db->where('id', $item['type_id'])->get($table)->row_array();
      //
      //   if (in_array($item['type'], ['option', 'material', 'cost'])) {
      //     $detail['details'] = json_decode($detail['details']);
      //     // $detail = array_map(function ($tmp) {
      //     //   $tmp['details'] = json_decode($tmp['details']);
      //     //   return $tmp;
      //     // }, $detail);
      //   }
      //
      //   $response['selected'][$item['type']] = $detail;
      //   $response['data'][$item['type']] = $item;
      // }
    } else {
      $page = $this->get('page');
      $limit = 10;
      $offset = ($page - 1) * $limit;

      $this->db->start_cache();
      $this->db->from('HK_quotations q')->join('HK_projects p', 'q.project_id = p.id')->join('HK_customers c', 'p.customer_id = c.id');
      if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
        $this->db->like($search, $keyword);
      }
      $this->db->stop_cache();

      $total = $this->db->count_all_results();
      $list = $this->db->select('q.*, p.name, c.corp_name, c.country')->order_by('q.id desc')->get(null, $limit, $offset)->result_array();

      $response = [
        'paginate' => [
          'total' => $total,
          'page' => $page,
          'limit' => $limit
        ],
        'list' => $list
      ];
    }

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function quotation_projects_get()
  {
    $list = $this->db->select('p.*')->from('HK_projects p')->join('HK_quotations q', 'p.id = q.project_id AND q.id IS NULL', 'left outer')->get()->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function quotation_post()
  {
    $data = $this->post();
    $data['version'] = '1.0';
    $this->db->insert('HK_quotations', $data);;
    $new_id = $this->db->insert_id();

    $code = 'AAAA' . str_pad($new_id, 5, '0', STR_PAD_LEFT);
    $this->db->where('id', $new_id)->update('HK_quotations', ['code' => $code]);

    if ($new_id) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function quotation_patch()
  {
    $data = $this->patch();
    $result = $this->db->where('id', $data['id'])->update('HK_quotations', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function quotation_products_get($customer_id = 0)
  {
    $list = $this->db->select('p.*, c.customer_id')->from('HK_product p')->join('HK_product_customer c', 'p.id = c.product_id')
            ->where('c.customer_id', $customer_id)->get()->result_array();

    $list = array_map(function ($item) {
      $this->db->start_cache();
      $this->db->select('cp.sales_price')
        ->from('HK_product_customer c')
        ->from('HK_product_customer_price cp')
        ->from('HK_product_price p')
        ->where('cp.product_id = p.product_id')->where('cp.product_price_id = p.id')->where('c.product_id = p.product_id')
        ->where('c.customer_id', $item['customer_id'])->where('cp.product_id', $item['id']);
      $this->db->stop_cache();

      $row = $this->db->where('p.start_date <= ', today())->where('p.end_date >= ', today())->get()->row_array();
      if (!$row) {
        $row = $this->db->where('p.start_date <= ', today())->order_by('p.start_date DESC')->get()->row_array();
      }

      $item['sales_price'] = $row['sales_price'];
      $this->db->flush_cache();
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function quotation_options_get($customer_id = 0)
  {
    $list = $this->db->get('HK_option_list')->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function quotation_materials_get($customer_id = 0)
  {
    $list = $this->db->get('HK_material_list')->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function quotation_costs_get($customer_id = 0)
  {
    $list = $this->db->get('HK_cost_list')->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function quotation_detail_post()
  {
    $data = $this->post();
    // $loop = ['product', 'option', 'material', 'cost'];
    //
    // foreach ($loop as $type) {
    //   if (isset($data[$type]['type_id'])) {
    //     if ($this->db->where(['quotation_id' => $data['id'], 'type' => $type, 'type_id' => $data[$type]['type_id']])->count_all_results('HK_quotation_detail')) {
    //       $this->db->where(['quotation_id' => $data['id'], 'type' => $type, 'type_id' => $data[$type]['type_id']])->update('HK_quotation_detail', $data[$type]);
    //     } else {
    //       $data[$type]['quotation_id'] = $data['id'];
    //       $data[$type]['type'] = $type;
    //
    //       $this->db->insert('HK_quotation_detail', $data[$type]);
    //     }
    //   }
    // }
    $data['set'] = json_encode($data['set'], JSON_UNESCAPED_UNICODE);
    $this->db->insert('HK_quotation_detail', $data);
    $new_id = $this->db->insert_id();

    $this->db->set('total', $data['total'])->where('id', $data['quotation_id'])->update('HK_quotations');

    if ($new_id) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }




















  //-----------------------------------------------------------------------
  // Option price
  //-----------------------------------------------------------------------
  public function option_price_get()
	{
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    $this->db->where('prices IS NOT NULL');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->like($search, $keyword);
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results('HK_option_list');
    $list = $this->db->get('HK_option_list', $limit, $offset)->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      $item['prices'] = json_decode($item['prices']);
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function option_price_editable_get()
  {
    $id = $this->get('id');
    $list = $this->db->where('id', $id)->get('HK_option_list')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function option_price_patch()
  {
    $data = $this->patch();
    $data['prices'] = json_encode($data['prices'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_option_list', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function option_price_with_relation_get()
	{
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    $this->db->from('HK_option_price p')->join('HK_option_list l', 'p.opt_id = l.id');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->like('l.' . $search, $keyword);
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->select('p.*, l.name, l.details')->get(null, $limit, $offset)->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function option_price_creatabl_with_relatione_get()
	{
    $list = $this->db->select('l.*, l.id opt_id')->from('HK_option_price p')->join('HK_option_list l', 'p.opt_id = l.id', 'right outer')
            ->where('p.id IS NULL')->get()->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function option_price_editable_with_relation_get()
  {
    $opt_id = $this->get('opt_id');
    $list = $this->db->where('opt_id', $opt_id)->order_by('start_date')->get('HK_option_price')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function option_price_with_relation_post()
  {
    $data = $this->post();
    $this->db->insert('HK_option_price', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }



  //-----------------------------------------------------------------------
  // Material parts
  //-----------------------------------------------------------------------
  public function material_parts_get()
	{
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->start_cache();
      $this->db->like($search, $keyword);
      $this->db->stop_cache();
    }

    $total = $this->db->count_all_results('HK_material_parts');
    $list = $this->db->get('HK_material_parts', $limit, $offset)->result_array();

    $list = array_map(function ($item) {
      $item['values'] = json_decode($item['values']);
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function material_parts_name_get()
  {
    $list = $this->db->select('name')->group_by('name')->get('HK_material_parts')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function material_parts_post()
  {
    $data = $this->post();
    $new = [
      'name' => isset($data['option']['name']) ? $data['option']['name'] : $data['name'],
      'type' => $data['type'],
      'values' => json_encode(array_filter($data['values'], function ($v) { if (trim($v) ) return $v; }), JSON_UNESCAPED_UNICODE),
    ];
    $this->db->insert('HK_material_parts', $new);
    $new_id = $this->db->insert_id();

    $code = str_pad($new_id, 6, '0', STR_PAD_LEFT);
    $this->db->where('id', $new_id)->update('HK_material_parts', ['code' => $code]);

    if ($new_id) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function material_parts_patch()
  {
    $data = $this->patch();
    $data['values'] = json_encode(array_filter($data['values'], function ($v) { if (trim($v) ) return $v; }), JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_material_parts', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  //-----------------------------------------------------------------------
  // Material list
  //-----------------------------------------------------------------------
  public function material_list_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->start_cache();
      $this->db->like($search, $keyword);
      $this->db->stop_cache();
    }

    $total = $this->db->count_all_results('HK_material_list');
    $list = $this->db->get('HK_material_list', $limit, $offset)->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      $item['prices'] = json_decode($item['prices']);
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function material_detail2_get()
  {
    $name = $this->get('name');
    $list = $this->db->where('name', $name)->get('HK_material_parts')->result_array();

    $list = array_map(function ($item) {
      $item['values'] = json_decode($item['values']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function material_list_post()
  {
    $data = $this->post();
    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $this->db->insert('HK_material_list', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function material_list_patch()
  {
    $data = $this->patch();
    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_material_list', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  //-----------------------------------------------------------------------
  // Material price
  //-----------------------------------------------------------------------
  public function material_price_get()
	{
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    $this->db->where('prices IS NOT NULL');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->like($search, $keyword);
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results('HK_material_list');
    $list = $this->db->get('HK_material_list', $limit, $offset)->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      $item['prices'] = json_decode($item['prices']);
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function material_price_creatable_get()
	{
    $list = $this->db->where('prices IS NULL')->get('HK_material_list')->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function material_price_editable_get()
  {
    $id = $this->get('id');
    $list = $this->db->where('id', $id)->get('HK_material_list')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function material_price_patch()
  {
    $data = $this->patch();
    $data['prices'] = json_encode($data['prices'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_material_list', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }



  //-----------------------------------------------------------------------
  // Cost parts
  //-----------------------------------------------------------------------
  public function cost_parts_get()
	{
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->start_cache();
      $this->db->like($search, $keyword);
      $this->db->stop_cache();
    }

    $total = $this->db->count_all_results('HK_cost_parts');
    $list = $this->db->get('HK_cost_parts', $limit, $offset)->result_array();

    $list = array_map(function ($item) {
      $item['values'] = json_decode($item['values']);
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function cost_parts_name_get()
  {
    $list = $this->db->select('name')->group_by('name')->get('HK_cost_parts')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function cost_parts_post()
  {
    $data = $this->post();
    $new = [
      'name' => isset($data['option']['name']) ? $data['option']['name'] : $data['name'],
      'type' => $data['type'],
      'values' => json_encode(array_filter($data['values'], function ($v) { if (trim($v) ) return $v; }), JSON_UNESCAPED_UNICODE),
    ];
    $this->db->insert('HK_cost_parts', $new);
    $new_id = $this->db->insert_id();

    $code = str_pad($new_id, 6, '0', STR_PAD_LEFT);
    $this->db->where('id', $new_id)->update('HK_cost_parts', ['code' => $code]);

    if ($new_id) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function cost_parts_patch()
  {
    $data = $this->patch();
    $data['values'] = json_encode(array_filter($data['values'], function ($v) { if (trim($v) ) return $v; }), JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_cost_parts', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  //-----------------------------------------------------------------------
  // Cost list
  //-----------------------------------------------------------------------
  public function cost_list_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->start_cache();
      $this->db->like($search, $keyword);
      $this->db->stop_cache();
    }

    $total = $this->db->count_all_results('HK_cost_list');
    $list = $this->db->get('HK_cost_list', $limit, $offset)->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      $item['prices'] = json_decode($item['prices']);
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function cost_detail2_get()
  {
    $name = $this->get('name');
    $list = $this->db->where('name', $name)->get('HK_cost_parts')->result_array();

    $list = array_map(function ($item) {
      $item['values'] = json_decode($item['values']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function cost_list_post()
  {
    $data = $this->post();
    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $this->db->insert('HK_cost_list', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function cost_list_patch()
  {
    $data = $this->patch();
    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_cost_list', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  //-----------------------------------------------------------------------
  // Cost price
  //-----------------------------------------------------------------------
  public function cost_price_get()
	{
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    $this->db->where('prices IS NOT NULL');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->like($search, $keyword);
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results('HK_cost_list');
    $list = $this->db->get('HK_cost_list', $limit, $offset)->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      $item['prices'] = json_decode($item['prices']);
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function cost_price_creatable_get()
	{
    $list = $this->db->where('prices IS NULL')->get('HK_cost_list')->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function cost_price_editable_get()
  {
    $id = $this->get('id');
    $list = $this->db->where('id', $id)->get('HK_cost_list')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function cost_price_patch()
  {
    $data = $this->patch();
    $data['prices'] = json_encode($data['prices'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_cost_list', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }



  //-----------------------------------------------------------------------
  // Product list
  //-----------------------------------------------------------------------
  public function product_list_get()
	{
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->start_cache();
      $this->db->like($search, $keyword);
      $this->db->stop_cache();
    }

    $total = $this->db->count_all_results('HK_product');
    $list = $this->db->get('HK_product', $limit, $offset)->result_array();

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function product_list_post()
  {
    $data = $this->post();
    $this->db->insert('HK_product', $data);
    $new_id = $this->db->insert_id();

    if ($new_id) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function product_list_patch()
  {
    $data = $this->patch();
    $result = $this->db->where('id', $data['id'])->update('HK_product', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  //-----------------------------------------------------------------------
  // Product price
  //-----------------------------------------------------------------------
  public function product_price_get()
	{
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    $this->db->where('prices IS NOT NULL');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->like($search, $keyword);
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results('HK_product');
    $list = $this->db->get('HK_product', $limit, $offset)->result_array();

    $list = array_map(function ($item) {
      $item['prices'] = json_decode($item['prices']);
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function product_price_creatable_get()
	{
    $list = $this->db->where('prices IS NULL')->get('HK_product')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function product_price_editable_get()
  {
    $id = $this->get('id');
    $list = $this->db->where('id', $id)->get('HK_product')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function product_price_patch()
  {
    $data = $this->patch();
    $data['prices'] = json_encode($data['prices'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_product', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }
}
