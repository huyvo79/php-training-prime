<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel {

    public function findUserById($id) {
        $sql = 'SELECT * FROM users WHERE id = '.$id;
        $user = $this->select($sql);

        return $user;
    }

    public function findUser($keyword) {
        $sql = 'SELECT * FROM users WHERE user_name LIKE %'.$keyword.'%'. ' OR user_email LIKE %'.$keyword.'%';
        $user = $this->select($sql);

        return $user;
    }

    /**
     * Authentication user
     * @param $userName
     * @param $password
     * @return array
     */
    public function auth($userName, $password) {
        $md5Password = md5($password);
        $sql = 'SELECT * FROM users WHERE name = "' . $userName . '" AND password = "'.$md5Password.'"';

        $user = $this->select($sql);
        return $user;
    }

    /**
     * Delete user by id
     * @param $id
     * @return mixed
     */
    public function deleteUserById($id) {
        $sql = 'DELETE FROM users WHERE id = '.$id;
        return $this->delete($sql);

    }

    /**
     * Update user
     * @param $input
     * @return mixed
     */
    public function updateUser($input) {
        $sql = 'UPDATE users SET 
                 name = "' . mysqli_real_escape_string(self::$_connection, $input['name']) .'", 
                 password="'. md5($input['password']) .'"
                WHERE id = ' . $input['id'];

        $user = $this->update($sql);

        return $user;
    }

    /**
     * Insert user
     * @param $input
     * @return mixed
     */
    public function insertUser($input) {
        $sql = "INSERT INTO `app_web1`.`users` (`name`, `password`) VALUES (" .
                "'" . $input['name'] . "', '".md5($input['password'])."')";

        $user = $this->insert($sql);

        return $user;
    }

    /**
     * Search users
     * @param array $params
     * @return array
     */
    // public function getUsers($params = []) {
    //     //Keyword
    //     if (!empty($params['keyword'])) {
    //         $sql = 'SELECT * FROM users WHERE name LIKE "%' . $params['keyword'] .'%"';

    //         //Keep this line to use Sql Injection
    //         //Don't change
    //         //Example keyword: abcef%";TRUNCATE banks;##
    //         $users = self::$_connection->multi_query($sql);

    //         //Get data
    //         $users = $this->query($sql);
    //     } else {
    //         $sql = 'SELECT * FROM users';
    //         $users = $this->select($sql);
    //     }

    //     return $users;
    // }

    public function getUsers($params = []) {
    // Nếu có keyword -> dùng prepared statement cho LIKE
    if (!empty($params['keyword'])) {
        $keyword = $params['keyword'];
        $kw = "%{$keyword}%";

        // Chuẩn bị statement
        $stmt = self::$_connection->prepare("SELECT id, name, fullname, email, type FROM users WHERE name LIKE ?");
        if ($stmt === false) {
            // xử lý lỗi chuẩn (không in lỗi DB ra user trong production)
            throw new Exception('Prepare failed: ' . self::$_connection->error);
        }

        // bind và execute
        $stmt->bind_param('s', $kw);
        $stmt->execute();

        // lấy kết quả dưới dạng mảng associative
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        $stmt->close();

        return $rows;
    } else {
        // Không có keyword: lấy tất cả (đơn giản)
        $res = self::$_connection->query("SELECT id, name, fullname, email, type FROM users");
        if ($res === false) {
            throw new Exception('Query failed: ' . self::$_connection->error);
        }
        return $res->fetch_all(MYSQLI_ASSOC);
    }
}

}