<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Initial_System_Setup extends CI_Migration {

    public function up() {
        // 1. Roles Table
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => FALSE, 'auto_increment' => TRUE],
            'role_name' => ['type' => 'VARCHAR', 'constraint' => 50],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('roles');

        // 2. Permissions Table
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => FALSE, 'auto_increment' => TRUE],
            'permission_name' => ['type' => 'VARCHAR', 'constraint' => 50],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('permissions');

        // 3. Role Permissions Table
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => FALSE, 'auto_increment' => TRUE],
            'role_id' => ['type' => 'INT', 'constraint' => 11],
            'permission_id' => ['type' => 'INT', 'constraint' => 11],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('role_permissions');
        // FKs and Indexes for role_permissions
        $this->db->query('ALTER TABLE role_permissions ADD CONSTRAINT role_permissions_ibfk_1 FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE');
        $this->db->query('ALTER TABLE role_permissions ADD CONSTRAINT role_permissions_ibfk_2 FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE');

        // 4. Entities Table
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => 155],
            'code' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => TRUE],
            'type' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => TRUE],
            'status' => ['type' => 'ENUM("Active","Not Active")', 'default' => 'Not Active'],
            'createdAt' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            'updatedAt' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('entities');
        $this->db->query('ALTER TABLE entities ADD INDEX idx_code (code)');
        $this->db->query('ALTER TABLE entities ADD INDEX idx_name (name)');
        $this->db->query('ALTER TABLE entities ADD INDEX idx_status (status)');

        // 5. Admin Table
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'ref_entity' => ['type' => 'INT', 'null' => TRUE],
            'role_id' => ['type' => 'INT', 'constraint' => 11],
            'c_email' => ['type' => 'VARCHAR', 'constraint' => 200],
            'c_name' => ['type' => 'VARCHAR', 'constraint' => 200],
            'c_password' => ['type' => 'CHAR', 'constraint' => 250, 'null' => TRUE],
            'c_status' => ['type' => 'ENUM("Pending","Active","Blocked","Freeze")'],
            'c_level' => ['type' => 'ENUM("1","2")', 'default' => '1'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('admin');
        $this->db->query('ALTER TABLE admin ADD UNIQUE KEY admin_UN_1 (c_email)');
        $this->db->query('ALTER TABLE admin ADD INDEX admin_IDX_1 (c_email, c_status)');
        $this->db->query('ALTER TABLE admin ADD INDEX admin_IDX_3 (c_status)');
        $this->db->query('ALTER TABLE admin ADD INDEX admin_ref_entity_IDX (ref_entity)');
        $this->db->query('ALTER TABLE admin ADD INDEX admin_c_level_IDX (c_level)');

        // 6. Admin Action Table
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'ref_adminId' => ['type' => 'INT', 'constraint' => 11],
            'c_datetime' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'c_ip' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_description' => ['type' => 'TEXT'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('admin_action');
        $this->db->query('ALTER TABLE admin_action ADD INDEX admin_action_IDX_2 (ref_adminId, c_datetime)');
        $this->db->query('ALTER TABLE admin_action ADD INDEX admin_action_IDX_4 (c_datetime)');
        $this->db->query('ALTER TABLE admin_action ADD INDEX admin_action_IDX_5 (c_ip)');

        // 7. Configs Table
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => TRUE],
            'value' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'deleted_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('configs');

        // 8. CI Sessions Table
        $this->dbforge->add_field([
            'id' => ['type' => 'VARCHAR', 'constraint' => 40],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
            'timestamp' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'default' => 0],
            'data' => ['type' => 'BLOB'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('ci_sessions');
        $this->db->query('ALTER TABLE ci_sessions ADD INDEX ci_sessions_timestamp (timestamp)');


        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'ref_userId' => ['type' => 'INT', 'constraint' => 11],
            'c_userType' => ['type' => 'ENUM("Admin","Merchant")', 'default' => 'Admin'],
            'c_email' => ['type' => 'VARCHAR', 'constraint' => 200],
            'c_sessionId' => ['type' => 'VARCHAR', 'constraint' => 100],
            'c_ipAddress' => ['type' => 'VARCHAR', 'constraint' => 45],
            'c_userAgent' => ['type' => 'TEXT', 'null' => TRUE],
            'c_loginAt' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'c_logoutAt' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('user_session_log');
        
        $this->db->query('ALTER TABLE user_session_log ADD INDEX idx_userId (ref_userId)');
        $this->db->query('ALTER TABLE user_session_log ADD INDEX idx_sessionId (c_sessionId)');
        $this->db->query('ALTER TABLE user_session_log ADD INDEX idx_loginAt (c_loginAt)');
    }

    public function down() {
        $this->dbforge->drop_table('admin_action', TRUE);
        $this->dbforge->drop_table('admin', TRUE);
        $this->dbforge->drop_table('role_permissions', TRUE);
        $this->dbforge->drop_table('roles', TRUE);
        $this->dbforge->drop_table('permissions', TRUE);
        $this->dbforge->drop_table('entities', TRUE);
        $this->dbforge->drop_table('configs', TRUE);
        $this->dbforge->drop_table('ci_sessions', TRUE);
    }
}
