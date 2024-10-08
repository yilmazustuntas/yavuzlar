<?php
session_start();
include "../controllers/auth-controller.php";
if (!IsUserLoggedIn()) {
    header("Location: ../login.php?message=Lütfen giriş yapınız.");
    exit();
} else if ($_SESSION['role'] != 1) {
    header("Location: ../index.php?message=403 Yetkisiz Giriş");
} else {
    if (isset($_POST['food_id'])  && isset($_POST['restaurant_id']) && isset($_POST['name']) && isset($_POST['description']) && $_POST['discount'] >= 0 && $_POST['discount'] <= 100 && $_POST['price'] > 0) {
        include "../controllers/company-controller.php";
        $restaurant_id = $_POST['restaurant_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $discount = $_POST['discount'];
        $food_id = $_POST['food_id'];

        $food = GetFoodById($food_id);
        $old_image_path = $food["image_path"];

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
            $max_size = 2 * 1024 * 1024;
            $target_dir = "./uploads/food/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $new_file_name = time() . "." . $image_file_type;
            $image_path = $target_dir . $new_file_name;

            if (filesize($_FILES["image"]["tmp_name"]) > $max_size) {
                header("Location: ../add-food.php?message=Resim boyutu 2MB altında olmalıdır.");
                exit();
            }
            if (move_uploaded_file($_FILES["image"]["tmp_name"], "../uploads/food/" . $new_file_name)) {
                if (file_exists($old_image_path) && $old_image_path) {
                    unlink($old_image_path);
                }
            } else {
                header("Location: ../add-food.php?message=Resim yüklenirken bir hata oluştu!");
                exit();
            }
        } else {
            $image_path = $old_image_path;
        }
        UpdateFood($food_id, $restaurant_id, $name, $description, $image_path, $price, $discount);
        header("Location: ../food-list.php?message=Başarıyla kaydedildi!");
        exit();
    }
    header("Location: ../add-food.php?message=Eksik veya hatalı bilgi girdiniz.");
    exit();
}
