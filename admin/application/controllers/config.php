<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Config extends CI_Controller {

    public function __construct() {

        parent::__construct();

        if ((int) $this->session->userdata("admin_id") <= 0)
            redirect("users/login");
    }

    public function index() {

        $this->load->model("config_model", "cfg");

        if ($this->input->post("update")) {
            $tables = $this->input->post("tables");
            foreach ($tables as $id => $data) {
                $this->db->update("wa_tables", $data, "id = $id");
            }
        }

        $page['collections'] = $this->cfg->get_collections();

        $tables = $this->cfg->get_tables();

        $existing = array();
        foreach ($page['collections'] as $item)
            $existing[] = $item['name'];

        $page['add_tables'] = array_diff($tables, $existing);

        $page['categories'] = $this->cfg->get_categories();

        $headers = array("meta_title" => "Admin | ". $this->config->item("site_title"));
        $this->load->view('headers.tpl', $headers);

        $this->load->view('config/config.tpl', $page);
    }

    public function remove_table($id) {

        $this->db->delete("wa_tables", array("id" => $id));
        $this->db->delete("wa_cols", array("id_table" => $id));

        redirect("config");
    }

    public function add_table($table) {

        $data = array(
                    "name" => $table,
                    "caption" => ucwords(str_replace("_", " ", $table)),
                    "per_page" => 20,
                    );

        $this->db->insert("wa_tables", $data);

        $collection_id = $this->db->insert_id();

        $r = $this->db->query("DESCRIBE `$table`");

        $fields = array();
        foreach ($r->result_array() as $item) {
            $enum_list = "";
            $relevant = "Yes";

            $f_type = (strpos($item['Type'], "(") ? substr($item['Type'], 0, strpos($item['Type'], "(")) : $item['Type']);

            switch ($f_type) {
                case "int":
                case "tinyint":
                case "smallint":
                case "mediumint":
                case "bigint":
                case "decimal":
                case "float":
                case "double":
                case "real":
                case "bit":
                case "boolean":
                case "serial":
                case "varchar":
                case "char":
                case "tinytext":    $type = "text"; break;

                case "text":
                case "mediumtext":
                case "longtext":
                case "binary":
                case "varbinary":
                case "blob":
                case "tinyblob":
                case "mediumblob":
                case "longblob":    $type = "textarea";
                                    $relevant = "No";
                                    break;

                case "enum":
                case "set":         $type = "enum";
                                    $find = array("'", "enum", "set", "(", ")", "\"", " ", ",");
                                    $replace = array("", "", "", "", "", "", "", "\n");
                                    $enum_list = str_replace($find, $replace, $item['Type']);
                                    break;

                case "date":
                case "year":        $type = "date";
                                    $item['Default'] = "#". $item['Default'] ."#";
                                    break;

                case "datetime":
                case "timestamp":
                case "time":        $type = "datetime";
                                    $item['Default'] = "#". $item['Default'] ."#";
                                    break;
            }

            switch($item['Field']) {
                case "image"    : $type = "image"; break;
                case "file"     : $type = "file"; break;

                default         : ;
            }

            $fields[] = array(
                        "name" => $item['Field'],
                        "caption" => ucfirst(str_replace("_", " ", $item['Field'])),
                        "type" => $type,
                        "not_null" => ($item['Null'] == 'NO' ? "Yes" : "No"),
                        "default_value" => $item['Default'],
                        "id_table" => $collection_id,
                        "enum_list" => $enum_list,
                        "visible" => ($item['Key'] == 'PRI' ? 'No' : 'Yes'),
                        "relevant" => (($item['Key'] == 'PRI' || $relevant == 'No') ? 'No' : 'Yes'),
                        "read_only" => ($item['Key'] == 'PRI' ? 'Yes' : 'No'),
                        "is_pk" => ($item['Key'] == 'PRI' ? 'Yes' : 'No'));
        }

        $this->db->insert_batch("wa_cols", $fields);

        redirect("config");
    }

    public function categories() {

        $this->load->model("config_model", "cfg");

        if ($this->input->post()) {
            $items = $this->input->post("categs");
            foreach ($items as $id => $data) {
                $this->db->update("wa_categories", $data, "id = $id");
            }
        }

        $page['categories'] = $this->cfg->get_categories();

        $headers = array("meta_title" => "Admin | ". $this->config->item("site_title"));
        $this->load->view('headers.tpl', $headers);

        $this->load->view('config/categories.tpl', $page);
    }

    public function add_category() {

        $this->db->insert("wa_categories", array("name" => $this->input->post("category")));

        redirect("config/categories");
    }

    public function remove_category($id) {

        $this->db->delete("wa_categories", array("id" => $id));

        redirect("config/categories");
    }

    public function edit_table($table_id) {

        $this->load->model("config_model", "cfg");

        if ($this->input->post("update")) {
            $fields = $this->input->post("fields");
            foreach ($fields as $id => $data) {
                $this->db->update("wa_cols", $data, "id = $id");
            }
        }

        $page['collections'] = $this->cfg->get_collections();
        $page['fields'] = $this->cfg->get_fields();
        $page['collection'] = $this->cfg->get_collection($table_id);
        $page['field_types'] = array('text','textarea','image','enum','fk','date','datetime','file');
        $page['foreign_keys'] = $this->cfg->get_pks();
        $page['tabs'] = $this->cfg->get_tabs($page['collection']['id']);

        $headers = array("meta_title" => "Admin | ". $this->config->item("site_title"));
        $this->load->view('headers.tpl', $headers);

        $this->load->view('config/table.tpl', $page);
    }

    public function update_tabs() {

        if ($this->input->post("update-tabs")) {

            $data = $this->input->post("tabs");
            foreach ($data as $tab_id => $update)
                $this->db->update("wa_tabs", $update, "id = $tab_id");
        }
        elseif ($this->input->post("add-tab")) {

            $data = array(
                        "parent_id" => $this->input->post('parent_id'),
                        "parent_pk" => $this->input->post('parent_pk'),
                        "tab_id" => $this->input->post('tab_id'),
                        "tab_fk" => $this->input->post('tab_fk'),
                        "caption" => $this->input->post('caption'),
                        );

            $this->db->insert("wa_tabs", $data);
        }

        redirect('config/edit_table/'. $this->input->post('parent_id'));
    }

    public function delete_tab($tab_id) {

        $r = $this->db->get_where("wa_tabs", array("id" => $tab_id));
        $tab = $r->row_array();

        $this->db->delete("wa_tabs", array("id" => $tab_id));

        redirect("config/edit_table/{$tab['parent_id']}");
    }
}

/* End of file config.php */
/* Location: ./application/controllers/config.php */