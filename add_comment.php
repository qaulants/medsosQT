<?php 
require_once "koneksi.php";
if (isset($_POST['tess'])) {
    echo $userId = $_POST['user_id'];
    echo $statusId = $_POST['status_id'];
    echo $commentText = mysqli_real_escape_string($koneksi, $_POST['comment_text']);
    
    if (!empty($commentText) && !empty($statusId)) {
        $query = mysqli_query($koneksi, "INSERT INTO comments (status_id, user_id, comment_text, created_at) VALUES ('$statusId', '$userId', '$commentText', NOW())");

        if($query){
            header("location: index.php?pg=profil");
            exit();
        }
        // if(mysqli_query($koneksi, $query)) {
        //     echo json_encode(["status" => "success", "message" => "Komentar berhasil ditambah"]);
        // } else {
        //     echo json_encode(["status" => "error", "message" => "Komentar gagal ditambah".mysqli_error($koneksi)]);
        // }
    // }else {
    //     echo json_encode(["status" => "error", "message" => "Komentar tidak boleh kosong"]);
    // }
    //exit();
}
}
?>