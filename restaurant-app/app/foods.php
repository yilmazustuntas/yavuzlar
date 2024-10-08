<?php
session_start();
include "./controllers/auth-controller.php";
if (!IsUserLoggedIn()) {
    header("Location: login.php?message=Lütfen giriş yapınız.");
    exit();
}else if ($_SESSION['role'] != 2) {
    header("Location: ./index.php?message=403 Yetkisiz Giriş");
}
include "./controllers/customer-controller.php";
include "./controllers/admin-controller.php";
$foods = GetFoods();
require_once "header.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/style.css">
    <title>Foods</title>
</head>

<body>
    <div>
        <h1 class="searchbox">Yemekler</h1>
        <?php if (empty($foods)) {
            echo "<p class='searchbox'>Hiçbir yemek bulunamadı.</p>";
        } else { ?>
            <div class="searchbox">
                <input style="border-radius: 8px; width: 200px; height: 25px;" type="search" id="searchbox" placeholder="Yemek Ara" />
            </div>
            <div class="foodSection">
            <?php foreach ($foods as $food): ?>
                    <div class="dataElement container_obj">
                        <div class="foodDiv t<?php echo $_SESSION['role']; ?>">
                            <a href="restaurant.php?r_id=<?php echo $food['restaurant_id']; ?>" class="description container_obj"><?php echo GetRestaurantName($food['restaurant_id']); ?></a>
                            <div class="imageContainer">
                                <img class="foodPhoto" src="<?php echo $food["image_path"]; ?>" alt="Yemek Fotoğrafı">
                                <?php if ($food['discount']) { ?>
                                    <span class="discount"><?php echo "%" . $food["discount"] . "!"; ?></span>
                                <?php } ?>
                            </div>
                            <p><?php echo $food["name"]; ?></p>
                            <span class="description"><?php echo $food["description"]; ?></span>
                            <p <?php echo $food['discount'] ? "class='highlight" . $_SESSION['role'] . "'>" . $food["price"] * (100 - $food['discount']) / 100 : ">" . $food['price']; ?></p>
                                <button style="border-radius: 15px;" class="modalBtn" data_id="<?php echo $food['id']; ?>">Ekle</button>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php } ?>
    </div>
    <div class="centerDiv">
        <a href="index.php" class="b<?php echo $_SESSION['role']; ?>"><button style="border-radius: 15px; background-color: #59b471; color: black;">Ana Sayfa</button></a>
    </div>
    <div id="modal" class="modal">
        <div style="border-radius: 20px; background-color: #59b471" class="modal-content centerDiv">
            <h3>Sipariş Notu Ekle</h3>
            <form action="./scripts/add-to-basket.php" method="post" class="centerDiv">
                <input hidden id="data_id" name="food_id" value="">
                <input style="border-radius: 8px; width: 200px; height: 25px;" name="note" class="container_obj" type="text" placeholder="Sipariş notu ekleyin">
                <button style="border-radius: 15px;" type="submit" class="normal container_obj">Ekle</button>
            </form>
            <button style="border-radius: 15px;" class="close red">İptal</button>
        </div>
    </div>
    <?php require_once "footer.php"; ?>
</body>
<script src="./public/js/searchbox.js"></script>
<script src="./public/js/modal.js"></script>

</html>