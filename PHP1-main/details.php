<?php require "layout/header.php"; ?>
<?php
require_once('database/config.php');
require_once('database/dbhelper.php');
require_once('utils/utility.php');
// Lấy id từ trang index.php truyền sang rồi hiển thị nó
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = 'select * from product where id=' . $id;
    $product = executeSingleResult($sql);
    // Kiểm tra nếu ko có id sp đó thì trả về index.php
    if ($product == null) {
        header('Location: index.php');
        die();
    }
}
?>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v11.0&appId=264339598396676&autoLogAppEvents=1" nonce="8sTfFiF4"></script>

<!-- END HEADR -->
<main>
    <div class="container">
        <div id="ant-layout">
        <section class="search-quan">
                <i class="fas fa-search"></i>
                <form action="hang.php" method="GET">
                    <input name="search" type="text" placeholder="Tìm món hoặc thức ăn">
                </form>
            </section>
        </div>

        <!-- END LAYOUT  -->
        <section class="main">
            <section class="oder-product">
                <div class="title">
                    <section class="main-order">
                        <h1><?= $product['title'] ?></h1><br><br>
                        <div class="box">
                            <img src="<?='admin/product/'.$product['thumbnail'] ?>" alt="">
                            <div class="about">
                                <p><?= $product['content'] ?></p>
                                <div class="size">
                                    <p>Size:</p>
                                    <select>
                                        <option value="value01">38</option>
                                        <option value="value02">39</option>
                                        <option value="value03">40</option>
                                        <option value="value04">41</option>
                                    </select>
                                </div>
                                <div class="number">
                                    <span class="number-buy">Số lượng</span>
                                    <input id="num" type="number" value="1" min="1" onchange="updatePrice()">
                                </div>

                                <p class="price">Giá: <span id="price"><?= number_format($product['price'], 0, ',', '.') ?></span><span> VNĐ</span><span class="gia none"><?= $product['price'] ?></span></p>
                               
                                <button class="add-cart" onclick="addToCart(<?= $id ?>)"><i class="fas fa-cart-plus"></i>Thêm vào giỏ hàng</button>
                                
                                <button class="buy-now" onclick="buyNow(<?= $id ?>)">Mua ngay</button>

                                <script>
                                    function updatePrice() {
                                        var price = document.getElementById('price').innerText;
                                        var num = document.querySelector('#num').value;
                                        var gia1 = document.querySelector('.gia').innerText;
                                        var gia = price.match(/\d/g);
                                        gia = gia.join("");
                                        var tong = gia1 * num;
                                        document.getElementById('price').innerHTML = tong.toLocaleString();
                                    }
                                </script>
                            </div>
                        </div>

                    </section>
                </div>
                <aside>
                    <h1>Gợi ý cho bạn</h1>
                    <div class="row">
                        <?php
                        $sql = 'select * from product limit 5';
                        $productList = executeResult($sql);
                        $index = 1;
                        foreach ($productList as $item) {
                            echo '
                                    <div class="col">
                                    <a href="details.php?id=' . $item['id'] . '">
                                        <img src="admin/product/'.$item['thumbnail'] . '" alt="">
                                        <div class="about">
                                            <div class="title">
                                                <p>' . $item['title'] . '</p>
                                                <span>Giá: ' . number_format($product['price'], 0, ',', '.') . ' VNĐ' . '</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                ';
                        }
                        ?>
                    </div>
                </aside>
            </section>

            <section class="restaurants">
                <div class="title">
                    <h1>Sản phẩm tại <span class="green">Shoe Store</span></h1>
                </div>
                <div class="product-restaurants">
                    <div class="row">
                        <?php
                        $sql = 'select * from product';
                        $productList = executeResult($sql);
                        $index = 1;
                        foreach ($productList as $item) {
                            echo '
                                <div class="col">
                                    <a href="details.php?id=' . $item['id'] . '">
                                        <img class="thumbnail" src="admin/product/' . $item['thumbnail'] . '" alt="">
                                        <div class="title">
                                            <p>' . $item['title'] . '</p>
                                        </div>
                                        <div class="price">
                                            <span>' . number_format($item['price'], 0, ',', '.') . ' VNĐ</span>
                                        </div>
                                        <div class="more">
                                        <div class="star">
                                            <span>Shoe Store</span>
                                        </div>
                                        <div class="time">
                                            <img src="images/icon/icon-clock.svg" alt="">
                                            <span>1 Colour</span>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                                ';
                        }
                        ?>
                    </div>
                </div>
            </section>
        </section>
    </div>
</main>
<?php require_once('layout/footer.php'); ?>
</div>
<script type="text/javascript">
    function addToCart(id) {
        var num = document.querySelector('#num').value;
        $.post('api/cookie.php', {
            'action': 'add',
            'id': id,
            'num': num
        }, function(data) {
            location.reload()
        })
    }

    function buyNow(id) {
            $.post('api/cookie.php', {
                'action': 'add',
                'id': id,
                'num': 1
            }, function(data) {
                location.assign("checkout.php");
            })
    }
</script>
</body>

</html>