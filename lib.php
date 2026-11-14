<?php
/**
 * ============================================================
 *  THU VIEN LOP DUNG CHUNG
 * ------------------------------------------------------------
 *  Gom 2 lop chinh, viet theo dung mau thay giao cung cap:
 *    - Database : ket noi va truy van CSDL bang PDO
 *    - Session  : luu / lay / xoa du lieu trong session
 *
 *  Sinh vien chi can nho 4 ham cua lop Database:
 *    Database::db_execute($sql, $params)           -> them / sua / xoa
 *    Database::db_get_list($sql)                   -> lay nhieu dong
 *    Database::db_get_list_condition($sql, $params)-> lay nhieu dong co dieu kien
 *    Database::db_get_row($sql, $params)           -> lay 1 dong
 * ============================================================
 */


/* ============================================================
 *  LOP DATABASE
 * ========================================================== */
class Database
{
    // Sua 4 dong duoi cho khop may cua ban
    private static $dsn      = 'mysql:host=localhost;dbname=tech_news;charset=utf8mb4';
    private static $username = "root";
    private static $password = "";
    private static $con      = null;

    public function __construct()
    {
        self::db_connect();
    }

    /** Mo ket noi toi CSDL (chi mo 1 lan) */
    public static function db_connect()
    {
        try {
            if (is_null(self::$con)) {
                self::$con = new PDO(self::$dsn, self::$username, self::$password);
                self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                self::$con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            echo 'Loi ket noi CSDL: ' . $e->getMessage();
            exit();
        }
    }

    /** Dong ket noi */
    public static function db_disconnect()
    {
        if (!is_null(self::$con)) {
            self::$con = null;
        }
    }

    /**
     * Dung cho INSERT, UPDATE, DELETE
     * Vi du: Database::db_execute("delete from news where id=:id", ['id'=>5]);
     */
    public static function db_execute($sql = '', $params = [])
    {
        if (!is_null(self::$con)) {
            $result = self::$con->prepare($sql);
            $result->execute($params);
            if ($result->rowCount() > 0) {
                $result->closeCursor();
                return true;
            }
        }
        return false;
    }

    /**
     * Lay danh sach nhieu dong, KHONG co dieu kien
     * Vi du: Database::db_get_list("select * from news");
     */
    public static function db_get_list($sql = '')
    {
        if (!is_null(self::$con)) {
            $result = self::$con->prepare($sql);
            $result->execute();
            if ($result->rowCount() > 0) {
                $rows = $result->fetchAll();
                $result->closeCursor();
                return $rows;
            }
        }
        return false;
    }

    /**
     * Lay danh sach nhieu dong CO dieu kien
     * Vi du: Database::db_get_list_condition("select * from news where status=:st", ['st'=>'published']);
     */
    public static function db_get_list_condition($sql = '', $params = [])
    {
        if (!is_null(self::$con)) {
            $result = self::$con->prepare($sql);
            $result->execute($params);
            if ($result->rowCount() > 0) {
                $rows = $result->fetchAll();
                $result->closeCursor();
                return $rows;
            }
        }
        return false;
    }

    /**
     * Lay DUY NHAT 1 dong
     * Vi du: Database::db_get_row("select * from news where id=:id", ['id'=>5]);
     */
    public static function db_get_row($sql = '', $params = [])
    {
        if (!is_null(self::$con)) {
            $result = self::$con->prepare($sql);
            $result->execute($params);
            if ($result->rowCount() > 0) {
                $row = $result->fetch();
                $result->closeCursor();
                return $row;
            }
        }
        return false;
    }

    /**
     * Lay 1 gia tri duy nhat (dung cho dem so luong, tinh tong...)
     * Vi du: Database::db_get_value("select count(*) from news");
     */
    public static function db_get_value($sql = '', $params = [])
    {
        if (!is_null(self::$con)) {
            $result = self::$con->prepare($sql);
            $result->execute($params);
            $value = $result->fetchColumn();
            $result->closeCursor();
            return $value;
        }
        return 0;
    }
}


/* ============================================================
 *  LOP SESSION
 * ========================================================== */
class Session
{
    /** Luu gia tri vao session */
    public static function set_session($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /** Lay gia tri tu session, khong co thi tra ve false */
    public static function get_session($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }

    /** Xoa mot gia tri trong session */
    public static function delete_session($key)
    {
        if (isset($_SESSION[$key])) {
            $_SESSION[$key] = array();
            unset($_SESSION[$key]);
        }
    }
}


/* ============================================================
 *  HAM PHAN TRANG paging()
 * ------------------------------------------------------------
 *  Dat o day de CA khu vuc admin LAN cac trang ngoai
 *  (index.php, tin-tuc.php) deu dung duoc.
 *
 *  Cach dung:
 *      $pg = paging('tin-tuc.php?trang={page}', $tongso, $trang, 9);
 *      // $pg['start'], $pg['limit'] -> dua vao cau SQL LIMIT
 *      // $pg['html']                -> in ra cac nut so trang
 * ========================================================== */

function paging($link, $total_records, $current_page, $limit)
{
    $total_page = ceil($total_records / $limit);
    if ($total_page < 1) {
        $total_page = 1;
    }

    // Gioi han trang hien tai trong khoang 1 .. total_page
    if ($current_page > $total_page) {
        $current_page = $total_page;
    } else if ($current_page < 1) {
        $current_page = 1;
    }

    $start = ($current_page - 1) * $limit;

    // ----------------------------------------------------------
    //  Tao HTML cac nut so trang theo dung mau cua BOOTSTRAP 5:
    //      <ul class="pagination">
    //          <li class="page-item"><a class="page-link">1</a></li>
    //          <li class="page-item active"><span class="page-link">2</span></li>
    //      </ul>
    //  Trang dang xem dung lop "active" (Bootstrap tu to mau xanh).
    // ----------------------------------------------------------
    $html = '<ul class="pagination justify-content-center mb-0">';

    if ($current_page > 1 && $total_page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="'
               . str_replace('{page}', $current_page - 1, $link) . '">Trước</a></li>';
    }

    for ($i = 1; $i <= $total_page; $i++) {
        if ($i == $current_page) {
            $html .= '<li class="page-item active" aria-current="page">'
                   . '<span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="'
                   . str_replace('{page}', $i, $link) . '">' . $i . '</a></li>';
        }
    }

    if ($current_page < $total_page && $total_page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="'
               . str_replace('{page}', $current_page + 1, $link) . '">Sau</a></li>';
    }

    $html .= '</ul>';

    return array(
        'start' => $start,
        'limit' => $limit,
        'html'  => $html,
    );
}


/* ============================================================
 *  LOP HELPER - cac ham nhap lieu tu form
 * ========================================================== */
class Helper
{
    /**
     * Lay gia tri o input tu form (POST truoc, khong co thi lay GET)
     * Vi du: Helper::input_value('title')
     */
    public static function input_value($inputname)
    {
        if (isset($_POST[$inputname])) {
            return $_POST[$inputname];
        }
        if (isset($_GET[$inputname])) {
            return $_GET[$inputname];
        }
        return '';
    }

    /** Kiem tra form da bam nut Submit chua */
    public static function is_submit($hidden)
    {
        $action = self::input_value('action');
        return (!empty($action) && $action == $hidden);
    }

    /** Chuyen trang */
    public static function redirect($url)
    {
        header("Location:{$url}");
        exit();
    }
}
