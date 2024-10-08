<?php
session_start();
include "../controllers/auth-controller.php";
if (!IsUserLoggedIn()) {
    header("Location: ../login.php?message=Lütfen giriş yapınız.");
    exit();
} else if ($_SESSION['role'] != 1) {
    header("Location: ../index.php?message=403 Yetkisiz Giriş");
} else {
    if (isset($_POST['name']) && isset($_POST['description'])) {
        include "../controllers/company-controller.php";
        $restaurant_id = $_POST['restaurant_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];

        $restaurant = GetRestaurantById($restaurant_id);
        $old_image_path = $restaurant["image_path"];

        $image_path = $old_image_path;

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
            $max_size = 2 * 1024 * 1024;
            $target_dir = "./uploads/restaurant/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $new_file_name = time() . "." . $image_file_type;
            $image_path = $target_dir . $new_file_name;

            if (filesize($_FILES["image"]["tmp_name"]) > $max_size) {
                header("Location: ../add-restaurant.php?message=Resim boyutu 2MB altında olmalıdır.");
                exit();
            }
            if (move_uploaded_file($_FILES["image"]["tmp_name"], "../uploads/restaurant/" . $new_file_name)) {
                if (file_exists($old_image_path) && $old_image_path) {
                    unlink($old_image_path);
                }
            } else {
                header("Location: ../add-restaurant.php?message=Resim yüklenirken bir hata oluştu!");
                exit();
            }
        }
        UpdateRestaurant($restaurant_id, $name, $description, $image_path);
        header("Location: ../restaurant-list.php?message=Başarıyla kaydedildi!");
        exit();
    }
    header("Location: ../restaurant-list.php?message=Eksik veya hatalı bilgi girdiniz.");
    exit();
}
