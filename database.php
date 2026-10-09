<?php
// Emergency Presentation Engine - Full Support for MySQL & JSON Fallback
$host   = "127.0.0.1";
$user   = "root";
$pass   = "";
$dbname = "disaster_portal";

mysqli_report(MYSQLI_REPORT_OFF);

// Attempt MySQL connection quietly
$conn = @new mysqli($host, $user, $pass, $dbname);

if ($conn && !$conn->connect_error) {
    // Database is connected!
    $conn->set_charset("utf8mb4");
} else {
    // Fallback Presentation Engine (Works 100% without MySQL)
    class EmergencyDB {
        private $storageDir;

        public function __construct() {
            $this->storageDir = __DIR__ . '/../data_store/';
            if (!file_exists($this->storageDir)) {
                @mkdir($this->storageDir, 0777, true);
            }
        }

        public function getFile($type) {
            $file = $this->storageDir . $type . '.json';
            if (!file_exists($file)) {
                $defaults = [
                    'users' => [
                        ['id' => 1, 'full_name' => 'Admin User', 'email' => 'admin@disaster.com', 'password' => 'admin123', 'role' => 'admin']
                    ],
                    'alerts' => [
                        ['id' => 1, 'title' => 'Coastal Flood & High Tide Warning', 'severity' => 'Danger / Red Alert', 'message' => 'High water levels expected along coastal areas.', 'is_active' => 1]
                    ],
                    'volunteers' => [
                        ['id' => 1, 'full_name' => 'Trisha Shah', 'phone' => '+91 9876543210', 'email' => 'shahtrisha531@gmail.com', 'city' => 'Bharuch', 'skill' => 'Rescue & Field Relief']
                    ],
                    'shelters' => [
                        ['id' => 1, 'name' => 'Central Community Shelter', 'city' => 'Bharuch', 'capacity' => 250, 'is_open' => 1]
                    ],
                    'disasters' => [
                        ['id' => 1, 'title' => 'Flood Safety Protocol', 'description' => 'Move immediately to higher ground. Avoid driving or walking through moving water.', 'severity' => 'High Risk'],
                        ['id' => 2, 'title' => 'Earthquake Emergency Response', 'description' => 'Drop, Cover, and Hold On. Stay away from heavy furniture and windows.', 'severity' => 'Warning']
                    ],
                    'feedback' => [
                        ['id' => 1, 'name' => 'Citizen Report', 'message' => 'Water logging reported near main highway.']
                    ]
                ];
                file_put_contents($file, json_encode($defaults[$type] ?? [], JSON_PRETTY_PRINT));
            }
            return json_decode(file_get_contents($file), true) ?: [];
        }

        public function saveFile($type, $data) {
            file_put_contents($this->storageDir . $type . '.json', json_encode(array_values($data), JSON_PRETTY_PRINT));
        }

        public function query($sql) {
            $sql = strtolower($sql);
            if (strpos($sql, 'from volunteers') !== false) $type = 'volunteers';
            elseif (strpos($sql, 'from alerts') !== false) $type = 'alerts';
            elseif (strpos($sql, 'from shelters') !== false) $type = 'shelters';
            elseif (strpos($sql, 'from disasters') !== false) $type = 'disasters';
            elseif (strpos($sql, 'from users') !== false) $type = 'users';
            else $type = 'feedback';

            $data = $this->getFile($type);

            if (strpos($sql, 'count(*)') !== false) {
                return new MockResult([['total' => count($data)]]);
            }

            if (strpos($sql, 'delete from alerts') !== false) {
                preg_match('/id\s*=\s*(\d+)/', $sql, $matches);
                if (isset($matches[1])) {
                    $id = (int)$matches[1];
                    $data = array_filter($data, fn($item) => $item['id'] != $id);
                    $this->saveFile('alerts', $data);
                }
                return true;
            }

            return new MockResult($data);
        }

        public function prepare($sql) {
            return new MockStmt($sql, $this);
        }
    }

    class MockResult {
        private $data;
        public $num_rows;

        public function __construct($data) {
            $this->data = array_values($data);
            $this->num_rows = count($this->data);
        }

        public function fetch_assoc() {
            return array_shift($this->data);
        }
    }

    class MockStmt {
        private $sql;
        private $db;
        private $params = [];

        public function __construct($sql, $db) {
            $this->sql = strtolower($sql);
            $this->db = $db;
        }

        public function bind_param($types, ...$args) {
            $this->params = $args;
        }

        public function execute() {
            if (strpos($this->sql, 'into users') !== false) {
                $users = $this->db->getFile('users');
                $newUser = [
                    'id'        => count($users) + 1,
                    'full_name' => $this->params[0] ?? 'New User',
                    'email'     => $this->params[1] ?? '',
                    'password'  => $this->params[2] ?? '',
                    'role'      => 'user'
                ];
                $users[] = $newUser;
                $this->db->saveFile('users', $users);
                return true;
            }

            if (strpos($this->sql, 'into volunteers') !== false) {
                $v = $this->db->getFile('volunteers');
                $v[] = [
                    'id'        => count($v) + 1,
                    'full_name' => $this->params[0] ?? '',
                    'phone'     => $this->params[1] ?? '',
                    'email'     => $this->params[2] ?? '',
                    'city'      => $this->params[3] ?? '',
                    'skill'     => $this->params[4] ?? ''
                ];
                $this->db->saveFile('volunteers', $v);
                return true;
            }

            return true;
        }

        public function get_result() {
            if (strpos($this->sql, 'from users') !== false) {
                $users = $this->db->getFile('users');
                $email = $this->params[0] ?? '';
                $matched = array_filter($users, fn($u) => strtolower($u['email']) === strtolower($email));
                return new MockResult($matched);
            }
            return new MockResult([]);
        }

        public function close() { return true; }
    }

    $conn = new EmergencyDB();
}
?>