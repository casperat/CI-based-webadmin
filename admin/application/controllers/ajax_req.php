<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// check ajax request
if( ! isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
    exit('No direct script access allowed');

class Ajax_req extends CI_Controller {

    function update_cookie($name, $value, $expire = 86400, $prefix = 'bzb_') {

        $this->load->helper('cookie');

        $cookie = array(
            'name'   => $name,
            'value'  => $value,
            'expire' => $expire,
            'path'   => '/',
            'prefix' => $prefix,
            'secure' => TRUE
        );
        $this->input->set_cookie($cookie);
    }

    function get_tab_item() {

        $collection_id = $this->input->get("cid");
        $fk_name = $this->input->get("fk_name");
        $fk_id = $this->input->get("fk_id");
        $index = $this->input->get("elem_index");
        $fields = $this->input->get("fields");

        $values = array();
        if (count($fields)) {
            foreach ($fields as $field)
                $values[$field['name']] = $field;
        }

        $this->load->model("collections_model", "collections");

        $collection = $this->collections->get($collection_id);
        $collection['fields'] = $this->collections->get_fields($collection['id'], null, false, array($fk_name));

        if (count($values))
            foreach ($collection['fields'] as $key => $field)
                if (isset($values[$field['name']])) {
                    $collection['fields'][$key]['value'] = $values[$field['name']]['value'];
                    if (strlen($values[$field['name']]['wa_thumb']))
                        $collection['fields'][$key]['wa_thumb'] = $values[$field['name']]['wa_thumb'];
                }

        if ($collection) {

            $collection['settings'] = array("prefix" => "tabs[$collection_id][fk_$index][",
                                        "suffix" => "]",
                                        "id_prefix" => "tab". $collection['id'] ."_". $index ."_");
            $collection['fk_name'] = $fk_name;

            echo $this->load->view("collections/add_fk_item.tpl", $collection, true);
        }
    }

    function add_simple($collection_id) {

        $this->load->model("collections_model", "collections");

        $collection = $this->collections->get($collection_id);
        $collection['fields'] = $this->collections->get_fields($collection_id);

        foreach ($collection['fields'] as $key => $field)
            if ($field['type'] == 'image' || $field['type'] == 'file') {
                $collection['fields'][$key]['visible'] = 'No';
            }

        $this->load->view("collections/ajax_add_collection.tpl", $collection);
    }

    function save_record() {

        $this->load->model("collections_model", "collections");
        $this->load->library("image_lib");

        $data = $this->input->post();

        $this->db->insert($data['collection'], $data['fields']);
        $id = $this->db->insert_id();

        if ($data['return_all']) {
            $collection = $this->collections->get($data['collection_id']);
            $setup = array("page" => 1, "per_page" => 1000);
            $records = $this->collections->get_records($collection, $setup);

            $ret = "<select name='{$data['select_name']}'>";

            foreach ($records as $item)
                $ret .= "<option value='{$item[$collection['pk']]}'". ($id == $item[$collection['pk']] ? " selected=''" : "") .">{$item[$collection['name_equiv_for_pk']]}</option>";

            $ret .= "</select>";

            echo $ret;
        }
        else
            echo $id;
    }

    function get_editorialist_div() {

        $this->load->model("collections_model", "collections");

        $collection = $this->collections->get_by_name("editorialists");

        $this->db->order_by("name");
        $r = $this->db->get("editorialists");

        $ret = "<div class='control-group' id='editorialist_holder' style='display: none'>";
        $ret .= "<label class='control-label'>Editorialist</label>
                <div class='controls'>
                    <div id='editorialist_id' style='display: inline-block;'>
                    <select name='editorialist_id'>";

        foreach ($r->result_array() as $item)
            $ret .= "<option value='{$item['id']}'>{$item['name']}</option>";

        $ret .= "</select>
                    </div><a data-collection-id='{$collection['id']}' data-fancybox-type='ajax' class='fancy-add-collection input-right-links' href='ajax/add_simple/{$collection['id']}'><i class='icon-plus'></i> Add</a>
                </div>
            </div>";

        echo $ret;
    }
}

/* End of file ajax_req.php */
/* Location: ./admin/application/controllers/ajax_req.php */