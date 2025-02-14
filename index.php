<?php 
    session_start();
    ob_start();
    if(!isset($_SESSION["giohang"])){
        $_SESSION["giohang"]=[];
    }
    include_once "dao/pdo.php";
    include_once "dao/user.php";
    include_once "dao/danhmuc.php";
    include_once "dao/sanpham.php";

    include_once "view/header.php";
    $dssp_nb = get_dssp_nb(8);
    $dssp_sale = get_dssp_sale(12);

    if(!isset($_GET['page'])){
        include_once "view/home.php";
    }else{
        switch ($_GET['page']) {
            case 'sanpham':
                $key = "";
                $titlepage = "";
                $dsdm = danhmuc_all();
                
                if(!isset($_GET['iddm'])){
                    $iddm=0;
                }else{
                    $iddm = $_GET['iddm'];
                    $titlepage = get_name_dm($iddm);
                }

                // kiểm tra có phải seach không?
                if(isset($_POST['timkiem']) && ($_POST['timkiem'])){
                    $key = $_POST['key'];
                    $titlepage ="Kết Quả Tìm Kiếm Với Từ Khóa: ".$key;
                }

                $dssp = get_dssp($key, $iddm,12);
                include_once "view/sanpham.php";
                break;
            case 'chitiet':
                if(isset($_GET['idsp'])){
                    $id = $_GET['idsp'];
                    $spct =get_sp_by_id($id);
                    $dsdm = danhmuc_all();
                    $iddm = $spct['iddm'];
                    $splq =get_dssp_lienquan($iddm, $id, 4);
                    include_once "view/chitiet.php";
                }else{
                    include_once "view/home.php";
                }
                break;
            case 'addcart':
                if (isset($_POST["addcart"])) {
                    $name = $_POST["name"];
                    $img = $_POST["img"];
                    $price = $_POST["price"];
                    $soluong = $_POST["soluong"];
            
                    $sp = array(
                        "name" => $name,
                        "img" => $img,
                        "price" => $price,
                        "soluong" => $soluong
                    );
            
                    $giohang = &$_SESSION["giohang"]; // Sử dụng biến tham chiếu để thao tác trực tiếp với mảng $_SESSION["giohang"]
            
                    // Kiểm tra xem sản phẩm đã tồn tại trong giỏ hàng chưa
                    $productExists = false;
                    foreach ($giohang as $key => $item) {
                        if ($item["name"] == $name) {
                            $giohang[$key]["soluong"] += $soluong; // Nếu sản phẩm đã tồn tại, tăng số lượng
                            $productExists = true;
                            break;
                        }
                    }
            
                    if (!$productExists) {
                        array_push($giohang, $sp); // Nếu sản phẩm chưa tồn tại, thêm vào mảng
                    }
            
                    header('location: index.php?page=viewcart');
                }
            break;                
            case 'delsp':
                if(isset($_GET['ind']) && ($_GET['ind']>=0)){
                    array_splice($_SESSION['giohang'], $_GET['ind'],1);
                    header('location: index.php?page=viewcart');
                }
                break;
            case 'viewcart':
                if(isset($_GET['del']) && ($_GET['del']==1)){
                    unset($_SESSION['giohang']);
                    header('location: index.php?page=viewcart');
                }else{
                    include_once "view/viewcart.php";
                }
                break;
            case 'login':
                if(isset($_POST["dangnhap"]) && ($_POST["dangnhap"])){
                    $username = $_POST["username"];
                    $password = $_POST["password"];
                    // xử lý: kiểm tra
                    $kq = checkuser( $username, $password);
                    if(is_array($kq)&&(count($kq))){
                        $_SESSION['s_user'] = $kq;
                        header('location: index.php'); 
                    }else{
                        $tb = "Tài Khoản Không Tồn Tại";
                        $_SESSION['tb_dangnhap'] = $tb;
                        header('location: index.php?page=dangnhap'); 
                    }
                    
                }
                
                break;
            case 'dangnhap':
                include_once "view/dangnhap.php";
                break;
            case 'myaccount':
                if(isset($_SESSION['s_user']) && (count($_SESSION['s_user']) > 0 )){
                    include_once "view/myaccount.php";
                }
                break;
            case 'logout':
                if(isset($_SESSION['s_user']) && (count($_SESSION['s_user']) > 0 )){
                    unset($_SESSION['s_user']);
                }
                header('location: index.php');
                break;
            case 'adduser':
                // xác định giá trị input 
                if(isset($_POST["dangky"]) && ($_POST["dangky"])){
                    $username = $_POST["username"];
                    $password = $_POST["password"];
                    $email = $_POST["email"];

                    // xử lý 
                    user_insert($username, $password, $email);
                }
                include_once "view/dangnhap.php";
                break;
            case 'updateuser':
                // xác định giá trị input 
                if(isset($_POST["capnhat"]) && ($_POST["capnhat"])){
                    $username = $_POST["username"];
                    $password = $_POST["password"];
                    $email = $_POST["email"];
                    $diachi = $_POST["diachi"];
                    $dienthoai = $_POST["dienthoai"];
                    $id = $_POST["id"];
                    $role = 0;
                    // xử lý 
                    update_user($username, $password, $email, $diachi, $dienthoai, $role, $id);
                    include_once "view/myaccount_confirm.php";
                }
               
                break;
            
            case 'dangky':
                include_once "view/dangky.php";
                break;
            case 'donhang':
                if(isset($_POST['donhang'])){
                    $name = $_POST['name'];
                    $diachi = $_POST['diachi'];
                    $email = $_POST['email'];
                    $dienthoai = $_POST['dienthoai'];
                    $pttt = $_POST['pttt'];
                    // tạo đơn hàng
                }
                include_once "view/donhang.php";
                break;
            default:
                include_once "view/home.php";
                break;
        }
    }

    include_once "view/footer.php";
?>