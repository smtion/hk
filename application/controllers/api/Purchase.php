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

    $total = $this->db->count_all_results('HK_option_list');
    $list = $this->db->order_by('id desc')->get('HK_option_list', $limit, $offset)->result_array();

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
    $this->db->insert('HK_option_list', $data);

    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function option_list_patch()
  {
    $data = $this->patch();
    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_option_list', $data);

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
    $this->db->select('o.*')->from('HK_option o')->join('HK_option_price p', 'o.id = p.option_id')->group_by('p.option_id');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->like('o.'.$search, $keyword);
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->order_by('o.id desc')->get(null, $limit, $offset)->result_array();
    $this->db->flush_cache();

    $list = array_map(function ($item) {
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
    $list = $this->db->where('prices IS NULL')->get('HK_option_list')->result_array();

    $list = array_map(function ($item) {
      $item['details'] = json_decode($item['details']);
      return $item;
    }, $list);

    $response = [
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

    $total = $this->db->count_all_results('HK_material_list');
    $list = $this->db->order_by('id desc')->get('HK_material_list', $limit, $offset)->result_array();

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
    $list = $this->db->order_by('id desc')->get('HK_material_list', $limit, $offset)->result_array();

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

    $total = $this->db->count_all_results('HK_cost_list');
    $list = $this->db->order_by('id desc')->get('HK_cost_list', $limit, $offset)->result_array();

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
    $list = $this->db->order_by('id desc')->get('HK_cost_list', $limit, $offset)->result_array();

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

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function product_list_post()
  {
    $data = $this->post();
    $this->db->insert('HK_products', $data);
    $new_id = $this->db->insert_id();

    if ($new_id) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function product_list_patch()
  {
    $data = $this->patch();
    $result = $this->db->where('id', $data['id'])->update('HK_products', $data);

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

    $total = $this->db->count_all_results('HK_products');
    $list = $this->db->order_by('id desc')->get('HK_products', $limit, $offset)->result_array();

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
    $list = $this->db->where('prices IS NULL')->get('HK_products')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function product_price_editable_get()
  {
    $id = $this->get('id');
    $list = $this->db->where('id', $id)->get('HK_products')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function product_price_patch()
  {
    $data = $this->patch();
    $data['prices'] = json_encode($data['prices'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_products', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }
}
