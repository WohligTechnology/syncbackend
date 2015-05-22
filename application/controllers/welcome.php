<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Welcome extends CI_Controller {
    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */

     public function localtoserver() {
       $data = json_decode(file_get_contents('php://input'), true);
       $user = $data['user'];
       $name = $data['name'];
       $email = $data['email'];
       $timestamp = $data['timestamp'];
       $type = $data['type'];
       $id = $data['serverid'];
       $id2 = $data['serverid2'];

       if(!($id>0) AND $type==2)
       {
         $type=1;
       }
       $timestamp = new DateTime();
       $timestamp=$timestamp->format('Y-m-d H:i:s')."";

       switch($type)
       {
           case 1:
           {
             $this->db->query("INSERT INTO `users` (`id`, `name`, `email`) VALUES (NULL, '$name', '$email')");
             $id = $this->db->insert_id();
             $this->db->query("INSERT INTO `userslog` (`id`,`user`, `timestamp`, `type`) VALUES (NULL,'$id', '$timestamp', '1')");
             $return = new stdClass();
             $return->id = $id;
             $return->timestamp=$timestamp;
             $data["message"] = $return;
           }
           break;
           case 2:
           {
             $this->db->query("UPDATE `users` SET `name`='$name', `email`= '$email' WHERE `id`='$id'");
             $changes = $this->db->affected_rows();
             if ($changes > 0) {
                 $this->db->query("INSERT INTO `userslog` (`id`,`user`, `timestamp`, `type`) VALUES (NULL,'$id', '$timestamp', '2') ");
                 $return = new stdClass();
                 $return->result = true;
                 $return->timestamp=$timestamp;
                 $data["message"] = $return;
             } else {
                 $return = new stdClass();
                 $return->result = false;
                 $return->timestamp=$timestamp;
                 $data["message"] = $return;
             }
           }
           break;
           case 3:
            {
             $this->db->query("DELETE FROM `users` WHERE `id`='$id2'");
             $changes = $this->db->affected_rows();
             if ($changes > 0) {
                 $this->db->query("INSERT INTO `userslog` (`id`,`user`, `timestamp`, `type`) VALUES (NULL,'$id2', '$timestamp', '3') ");
                 $return = new stdClass();
                 $return->result = true;
                 $return->timestamp=$timestamp;
                 $data["message"] = $return;
             } else {
                 $return = new stdClass();
                 $return->result = false;
                 $return->timestamp=$timestamp;
                 $data["message"] = $return;
             }
           }
           break;
         }
         $this->load->view("json", $data);
     }

    public function localtoserver_create() {
        $name = $this->input->get_post("name");
        $email = $this->input->get_post("email");
        $this->db->query("INSERT INTO `users` (`id`, `name`, `email`) VALUES (NULL, '$name', '$email')");
        $id = $this->db->insert_id();
        $this->db->query("INSERT INTO `userslog` (`id`,`user`, `timestamp`, `type`) VALUES (NULL,'$id', CURRENT_TIMESTAMP, '1')");
        $return = new stdClass();
        $return->id = $id;
        $data["message"] = $return;
        $this->load->view("json", $data);
    }
    public function localtoserver_update() {
        $id = $this->input->get_post("id");
        $name = $this->input->get_post("name");
        $email = $this->input->get_post("email");
        $this->db->query("UPDATE `users` SET `name`='$name', `email`= '$email' WHERE `id`='$id'");
        $changes = $this->db->affected_rows();
        if ($changes > 0) {
            $this->db->query("INSERT INTO `userslog` (`id`,`user`, `timestamp`, `type`) VALUES (NULL,'$id', CURRENT_TIMESTAMP, '2') ");
            $data["message"] = true;
        } else {
            $data["message"] = false;
        }
        $this->load->view("json", $data);
    }
    public function localtoserver_delete() {
        $id = $this->input->get_post("id");
        $this->db->query("DELETE FROM `users` WHERE `id`='$id'");
        $changes = $this->db->affected_rows();
        if ($changes > 0) {
            $this->db->query("INSERT INTO `userslog` (`id`,`user`, `timestamp`, `type`) VALUES (NULL,'$id', CURRENT_TIMESTAMP, '3') ");
            $data["message"] = true;
        } else {
            $data["message"] = false;
        }
        $this->load->view("json", $data);
    }
    public function servertolocal() {
        $timestamp = $this->input->get_post('timestamp');

        $limit = $this->input->get_post('limit');
        if ($limit == "") {
            $limit = 50;
        }
        $query = $this->db->query("SELECT `user` as `id`,`name`,`email`,`timestamp` as `timestamp` ,`type` FROM (SELECT `userslog`.`user`,`users`.`name`,`users`.`email`, `userslog`.`timestamp`, `userslog`.`type` FROM `userslog` LEFT OUTER JOIN `users` ON `users`.`id`=`userslog`.`user` WHERE `userslog`.`timestamp` > '$timestamp' ORDER BY `userslog`.`timestamp` DESC) as `tab1` GROUP BY `tab1`.`user` ORDER BY `tab1`.`timestamp` ASC LIMIT 0,$limit");
        if ($query->num_rows() > 0) {
            $data["message"] = $query->result();
        } else {
            $data["message"] = false;
        }
        $this->load->view("json", $data);
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
