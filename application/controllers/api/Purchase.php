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

    if ($new_id) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function option_parts_patch()
  {
    $data = $this->patch();
    $data['values'] = json_encode(array_filter($data['values'], function ($v) { if (trim($v) ) return $v; }), JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_option_parts', $data);

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

    $total = $this->db->count_all_results('HK_option');
    $list = $this->db->order_by('id desc')->get('HK_option', $limit, $offset)->result_array();

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
    $this->db->insert('HK_option', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function option_list_patch()
  {
    $data = $this->patch();
    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_option', $data);

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
    $this->db->select('o.*')->from('HK_option o')->join('HK_option_price p', 'o.id = p.option_id')->group_by('o.id');
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

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function option_price_creatable_get()
	{
    $list = $this->db->select('o.*')->from('HK_option o')->join('HK_option_price p', 'o.id = p.option_id', 'left outer')
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

    $total = $this->db->count_all_results('HK_material');
    $list = $this->db->order_by('id desc')->get('HK_material', $limit, $offset)->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      // $item['prices'] = json_decode($item['prices']);
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
    $this->db->insert('HK_material', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function material_list_patch()
  {
    $data = $this->patch();
    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_material', $data);

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
    $this->db->select('m.*')->from('HK_material m')->join('HK_material_price p', 'm.id = p.material_id')->group_by('m.id');
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

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function material_price_creatable_get()
	{
    $list = $this->db->select('m.*')->from('HK_material m')->join('HK_material_price p', 'm.id = p.material_id', 'left outer')
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

    $total = $this->db->count_all_results('HK_cost');
    $list = $this->db->order_by('id desc')->get('HK_cost', $limit, $offset)->result_array();

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
    $this->db->insert('HK_cost', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function cost_list_patch()
  {
    $data = $this->patch();
    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_cost', $data);

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
    $this->db->select('c.*')->from('HK_cost c')->join('HK_cost_price p', 'c.id = p.cost_id')->group_by('c.id');
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

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function cost_price_creatable_get()
	{
    $list = $this->db->select('c.*')->from('HK_cost c')->join('HK_cost_price p', 'c.id = p.cost_id', 'left outer')
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

    $total = $this->db->count_all_results('HK_product');
    $list = $this->db->order_by('id desc')->get('HK_product', $limit, $offset)->result_array();

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

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
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
    $this->db->select('r.*')->from('HK_product r')->join('HK_product_price p', 'r.id = p.product_id')->group_by('r.id');
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

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function product_price_creatable_get()
	{
    $list = $this->db->select('r.*')->from('HK_product r')->join('HK_product_price p', 'r.id = p.product_id', 'left outer')
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

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }
}
