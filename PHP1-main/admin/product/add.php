<?php
require_once('../database/dbhelper.php');
$id = $title = $price = $number = $size = $thumbnail = $content = $id_category = "";
if (!empty($_POST['title'])) {
    if (isset($_POST['title'])) {
        $title = $_POST['title'];
        $title = str_replace('"', '\\"', $title);
    }
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $id = str_replace('"', '\\"', $id);
    }
    if (isset($_POST['price'])) {
        $price = $_POST['price'];
        $price = str_replace('"', '\\"', $price);
    }
    if (isset($_POST['number'])) {
        $number = $_POST['number'];
        $number = str_replace('"', '\\"', $number);
    }
    if (isset($_POST['size'])) {
        $size = $_POST['size'];
        $size = str_replace('"', '\\"', $size);      
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "Phải Post dữ liệu";
        die;
    }
    if (!isset($_FILES["thumbnail"])) {
        echo "Dữ liệu không đúng cấu trúc";
        die;
    }

    if ($_FILES["thumbnail"]['error'] != 0) {
        echo "Dữ liệu upload bị lỗi";
        die;
    }

    $target_dir    = "uploads/";

    $target_file   = $target_dir . basename($_FILES["thumbnail"]["name"]);

    $allowUpload   = true;

    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

    $maxfilesize   = 800000;

    $allowtypes    = array('jpg', 'png', 'jpeg', 'gif');


    if (isset($_POST["submit"])) {

        $check = getimagesize($_FILES["thumbnail"]["tmp_name"]);
        if ($check !== false) {
            echo "Đây là file ảnh - " . $check["mime"] . ".";
            $allowUpload = true;
        } else {
            echo "Không phải file ảnh.";
            $allowUpload = false;
        }
    }
    if ($_FILES["thumbnail"]["size"] > $maxfilesize) {
        echo "Không được upload ảnh lớn hơn $maxfilesize (bytes).";
        $allowUpload = false;
    }

    if (!in_array($imageFileType, $allowtypes)) {
        echo "Chỉ được upload các định dạng JPG, PNG, JPEG, GIF";
        $allowUpload = false;
    }

    if ($allowUpload) {
        if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
        } else {
            echo "Có lỗi xảy ra khi upload file.";
        }
    } else {
        echo "Không upload được file, có thể do file lớn, kiểu file không đúng ...";
    }

    if (isset($_POST['content'])) {
        $content = $_POST['content'];
        $content = str_replace('"', '\\"', $content);
    }
    if (isset($_POST['id_category'])) {
        $id_category = $_POST['id_category'];
        $id_category = str_replace('"', '\\"', $id_category);
    }
    if (!empty($title)) {
        $created_at = $updated_at = date('Y-m-d H:s:i');
        // Lưu vào DB
        if ($id == '') {
            // Thêm danh mục
            $sql = 'insert into product(title, price, number, size, thumbnail, content, id_category, created_at, updated_at) 
            values ("' . $title . '","' . $price . '","' . $number . '","' . $size . '","' . $target_file . '","' . $content . '","' . $id_category . '","' . $created_at . '","' . $updated_at . '")';
        } else {
            // Sửa danh mục
            $sql = 'update product set title="' . $title . '",price="' . $price . '",number="' . $number . '",size="' . $size . '",thumbnail="' . $target_file . '",content="' . $content . '",id_category="' . $id_category . '", updated_at="' . $updated_at . '" where id=' . $id;
        }
        execute($sql);
        header('Location: index.php');
        die();
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = 'select * from product where id=' . $id;
    $product = executeSingleResult($sql);
    if ($product != null) {
        $title = $product['title'];
        $price = $product['price'];
        $number = $product['number'];
        $size = $product['size'];
        $thumbnail = $product['thumbnail'];
        $content = $product['content'];
        $id_category = $product['id_category'];
        $created_at = $product['created_at'];
        $updated_at = $product['updated_at'];
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Thêm Sản Phẩm</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <!-- summernote -->
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
</head>

<body>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="../index.php">Thống kê</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="index.php">Quản lý danh mục</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../product/">Quản lý sản phẩm</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../dashboard.php">Quản lý giỏ hàng</a>
        </li>
        <li class="nav-item ">
            <a class="nav-link active" href="../user.php">Quản lý tài khoản</a>
        </li>
    </ul>
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2 class="text-center">Thêm/Sửa Sản Phẩm</h2>
            </div>
            <div class="panel-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Tên Sản Phẩm:</label>
                        <input type="text" id="id" name="id" value="<?= $id ?>" hidden="true">
                        <input required="true" type="text" class="form-control" id="title" name="title" value="<?= $title ?>">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Chọn Danh Mục</label>
                        <select class="form-control" id="id_category" name="id_category">
                            <option>Chọn danh mục</option>
                            <?php
                            $sql = 'select * from category';
                            $categoryList = executeResult($sql);
                            foreach ($categoryList as $item) {
                                if ($item['id'] == $id_category) {
                                    echo '<option selected value="' . $item['id'] . '">' . $item['name'] . '</option>';
                                } else {
                                    echo '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Giá Sản Phẩm:</label>
                        <input required="true" type="text" class="form-control" id="price" name="price" value="<?= $price ?>">
                    </div>
                    <div class="form-group">
                        <label for="name">Số Lượng Sản Phẩm:</label>
                        <input required="true" type="number" class="form-control" id="number" name="number" value="<?= $number ?>">
                    </div>
                    <div class="form-group">
                        <label for="name">Size Sản Phẩm:</label>
                        <input required="true" type="size" class="form-control" id="size" name="size" value="<?= $size ?>">
                    </div>
                    <div class="form-group">
                        <label for="name">Thumbnail:</label>
                        <input type="file" class="form-control-file" id="exampleFormControlFile1" id="thumbnail" name="thumbnail">
                        <img src="<?= $thumbnail ?>" style="max-width: 200px" id="img_thumbnail">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Nội dung</label>
                        <textarea class="form-control" id="content" rows="3" name="content"><?= $content ?></textarea>
                    </div>
                    <button class="btn btn-success">Lưu</button>
                    <?php
                    $previous = "javascript:history.go(-1)";
                    if (isset($_SERVER['HTTP_REFERER'])) {
                        $previous = $_SERVER['HTTP_REFERER'];
                    }
                    ?>
                    <a href="<?= $previous ?>" class="btn btn-warning">Back</a>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function updateThumbnail() {
            $('#img_thumbnail').attr('src', $('#thumbnail').val())
        }
        $(function() {
            $('#content').summernote({
                height: 200
            });
        })
    </script>
</body>

</html>