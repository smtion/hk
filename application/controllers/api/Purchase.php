<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Purchase extends REST_Controller {

  function __construct()
  {
    parent::__construct();

    if (!is_logged_on()) {
      $this->response(NULL, REST_Controller::HTTP_UNAUTHORIZED);
    }
  }

  //-----------------------------------------------------------------------
  // Aluminum
  //-----------------------------------------------------------------------
  public function aluminum_get()
	{
    $data = $this->get();

    $list = $this->db->where('date >=', implode('-', [$data['year'], $data['month'], '01']))
            ->where('date <=', implode('-', [$data['year'], $data['month'], '31']))
            ->order_by('date ASC')->get('HK_aluminum')->result_array();

    $labels = [];
    $data1 = [];
    $data2 = [];
    foreach ($list as $item) {
      $labels[] = substr($item['date'], 5, 5);
      $data1[] = $item['price'];
      $data2[] = $item['buy_price'];
    }

    $response = [
      'labels' => $labels,
      'data1' => $data1,
      'data2' => $data2,
    ];

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function aluminum2_get()
	{
    $prev1Date = date('Y-m-d', strtotime('-1 days', time()));
    $prev2Date = date('Y-m-d', strtotime('-2 days', time()));
    $prev = $this->db->where_in('date', [$prev1Date, $prev2Date])->get('HK_aluminum')->result_array();
    $data = $this->db->where('date', today())->get('HK_aluminum')->row_array();

    $list = [];
    foreach ($prev as $item) {
      $list[$item['date']] = $item;
    }

    $response = [
      'list' => $list,
      'data' => $data,
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function aluminum_put()
  {
    $data = $this->put();

    if ($this->db->where('date', $data['date'])->count_all_results('HK_aluminum')) {
      $result = $this->db->where('date', $data['date'])->update('HK_aluminum', $data);
    } else {
      $this->db->insert('HK_aluminum', $data);
      $result = $this->db->insert_id();
    }

    activity_log();
    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }



  //-----------------------------------------------------------------------
  // Option parts
  //-----------------------------------------------------------------------
  public function option_parts_get()
	{
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->start_cache();
      $this->db->like($search, $keyword);
      $this->db->stop_cache();
    }

    $total = $this->db->count_all_results('HK_option_parts');
    $list = $this->db->order_by('id desc')->get('HK_option_parts', $limit, $offset)->result_array();

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function option_parts_name_get()
  {
    $list = $this->db->select('name')->group_by('name')->get('HK_option_parts')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function option_parts_post()
  {
    $data = $this->post();
    $new = [
      'name' => isset($data['option']['name']) ? $data['option']['name'] : $data['name'],
      'type' => $data['type'],
      'values' => json_encode(array_filter($data['values'], function ($v) { if (trim($v) ) return $v; }), JSON_UNESCAPED_UNICODE),
    ];
    $this->db->insert('HK_option_parts', $new);
    $new_id = $this->db->insert_id();

    $code = str_pad($new_id, 6, '0', STR_PAD_LEFT);
    $this->db->where('id', $new_id)->update('HK_option_parts', ['code' => $code]);

    activity_log();
    if ($new_id) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function option_parts_patch()
  {
    $data = $this->patch();
    $new = [
      'name' => isset($data['option']['name']) ? $data['option']['name'] : $data['name'],
      'type' => $data['type'],
      'values' => json_encode(array_filter($data['values'], function ($v) { if (trim($v) ) return $v; }), JSON_UNESCAPED_UNICODE),
    ];
    $result = $this->db->where('id', $data['id'])->update('HK_option_parts', $new);

    activity_log();
    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  //-----------------------------------------------------------------------
  // Option list
  //-----------------------------------------------------------------------
  public function option_list_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->start_cache();
      $this->db->like($search, $keyword);
      $this->db->stop_cache();
    }

    $total = $this->db->count_all_results('HK_options');
    $list = $this->db->order_by('id desc')->get('HK_options', $limit, $offset)->result_array();

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function option_detail2_get()
  {
    $name = $this->get('name');
    $list = $this->db->where('name', $name)->get('HK_option_parts')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function option_list_post()
  {
    $data = $this->post();
    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $this->db->insert('HK_options', $data);

    activity_log();
    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function option_list_patch()
  {
    $data = $this->patch();
    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_options', $data);

    activity_log();
    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
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
    $this->db->select('o.*')->from('HK_options o')->join('HK_option_price p', 'o.id = p.option_id')->group_by('o.id');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->like('o.'.$search, $keyword);
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->order_by('o.id desc')->get(null, $limit, $offset)->result_array();
    $this->db->flush_cache();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      $item['prices'] = $this->db->where('option_id', $item['id'])->order_by('start_date desc')->get('HK_option_price')->result_array();
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

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function option_price_creatable_get()
	{
    $list = $this->db->select('o.*')->from('HK_options o')->join('HK_option_price p', 'o.id = p.option_id', 'left outer')
            ->where('p.option_id IS NULL')->group_by('o.id')->get()->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function option_price_post()
  {
    $data = $this->post();
    $result = $this->db->insert('HK_option_price', $data);

    activity_log();
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
    $list = $this->db->order_by('id desc')->get('HK_material_parts', $limit, $offset)->result_array();

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

    activity_log();
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

    activity_log();
    if ($new_id) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function material_parts_patch()
  {
    $data = $this->patch();
    $new = [
      'name' => isset($data['option']['name']) ? $data['option']['name'] : $data['name'],
      'type' => $data['type'],
      'values' => json_encode(array_filter($data['values'], function ($v) { if (trim($v) ) return $v; }), JSON_UNESCAPED_UNICODE),
    ];
    $result = $this->db->where('id', $data['id'])->update('HK_material_parts', $new);

    activity_log();
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

    $total = $this->db->count_all_results('HK_materials');
    $list = $this->db->order_by('id desc')->get('HK_materials', $limit, $offset)->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);;
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

    activity_log();
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
    $this->db->insert('HK_materials', $data);

    activity_log();
    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function material_list_patch()
  {
    $data = $this->patch();
    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_materials', $data);

    activity_log();
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
    $this->db->select('m.*')->from('HK_materials m')->join('HK_material_price p', 'm.id = p.material_id')->group_by('m.id');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->like('m.'.$search, $keyword);
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->order_by('m.id desc')->get(null, $limit, $offset)->result_array();
    $this->db->flush_cache();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      $item['prices'] = $this->db->where('material_id', $item['id'])->order_by('start_date desc')->get('HK_material_price')->result_array();
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

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function material_price_creatable_get()
	{
    $list = $this->db->select('m.*')->from('HK_materials m')->join('HK_material_price p', 'm.id = p.material_id', 'left outer')
            ->where('p.material_id IS NULL')->group_by('m.id')->get()->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function material_price_post()
  {
    $data = $this->post();
    $result = $this->db->insert('HK_material_price', $data);

    activity_log();
    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
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
    $list = $this->db->order_by('id desc')->get('HK_cost_parts', $limit, $offset)->result_array();

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

    activity_log();
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

    activity_log();
    if ($new_id) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function cost_parts_patch()
  {
    $data = $this->patch();
    $new = [
      'name' => isset($data['option']['name']) ? $data['option']['name'] : $data['name'],
      'type' => $data['type'],
      'values' => json_encode(array_filter($data['values'], function ($v) { if (trim($v) ) return $v; }), JSON_UNESCAPED_UNICODE),
    ];
    $result = $this->db->where('id', $data['id'])->update('HK_cost_parts', $new);

    activity_log();
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

    $total = $this->db->count_all_results('HK_costs');
    $list = $this->db->order_by('id desc')->get('HK_costs', $limit, $offset)->result_array();

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

    activity_log();
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
    $this->db->insert('HK_costs', $data);

    activity_log();
    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function cost_list_patch()
  {
    $data = $this->patch();
    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_costs', $data);

    activity_log();
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
    $this->db->select('c.*')->from('HK_costs c')->join('HK_cost_price p', 'c.id = p.cost_id')->group_by('c.id');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->like('c.'.$search, $keyword);
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->order_by('c.id desc')->get(null, $limit, $offset)->result_array();
    $this->db->flush_cache();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      $item['prices'] = $this->db->where('cost_id', $item['id'])->order_by('start_date desc')->get('HK_cost_price')->result_array();
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

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function cost_price_creatable_get()
	{
    $list = $this->db->select('c.*')->from('HK_costs c')->join('HK_cost_price p', 'c.id = p.cost_id', 'left outer')
            ->where('p.cost_id IS NULL')->group_by('c.id')->get()->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function cost_price_post()
  {
    $data = $this->post();
    $result = $this->db->insert('HK_cost_price', $data);

    activity_log();
    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
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

    $total = $this->db->count_all_results('HK_products');
    $list = $this->db->order_by('id desc')->get('HK_products', $limit, $offset)->result_array();

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function product_list_post()
  {
    $data = $this->post();
    $this->db->insert('HK_products', $data);

    activity_log();
    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function product_list_patch()
  {
    $data = $this->patch();
    $result = $this->db->where('id', $data['id'])->update('HK_products', $data);

    activity_log();
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
    $this->db->select('r.*')->from('HK_products r')->join('HK_product_price p', 'r.id = p.product_id')->group_by('r.id');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->like('r.'.$search, $keyword);
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->order_by('r.id desc')->get(null, $limit, $offset)->result_array();
    $this->db->flush_cache();

    $list = array_map(function ($item) {
      $item['prices'] = $this->db->where('product_id', $item['id'])->order_by('start_date desc')->get('HK_product_price')->result_array();
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

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function product_price_creatable_get()
	{
    $list = $this->db->select('r.*')->from('HK_products r')->join('HK_product_price p', 'r.id = p.product_id', 'left outer')
            ->where('p.product_id IS NULL')->group_by('r.id')->get()->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function product_price_post()
  {
    $data = $this->post();
    $result = $this->db->insert('HK_product_price', $data);

    activity_log();
    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }
}
