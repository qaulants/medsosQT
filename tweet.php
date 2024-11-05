<?php
if (isset($_POST['posting'])) {
    $content = $_POST['content'];

    // jika gambar mau diubah
    if (!empty($_FILES['foto']['name'])) {
        $nama_foto =  $_FILES['foto']['name'];
        $ukuran_foto =  $_FILES['foto']['size'];

        $ext = array('png', 'jpg', 'jpeg');
        $extFoto  = pathinfo($nama_foto, PATHINFO_EXTENSION);

        //jika extension foto tidak ada ext yang terdaftar di array ext maka foto tidak boleh diupload
        if (!in_array($extFoto, $ext)) {
            echo "Ext tidak ditemukan";
            die;
        } else {
            //pindahkan gambar dari tmp folder ke folder yang sudah kita buat
            // unlink() : menghapus file
            unlink('upload/' . $rowPosting['foto']);
            move_uploaded_file($_FILES['foto']['tmp_name'], 'upload/' . $nama_foto);

            $insert = mysqli_query($koneksi, "INSERT INTO tweet(content, id_user, foto) VALUES('$content', '$id_user', '$nama_foto') ");
        }
    }  //jika tidak mau diubah
    else {
        $insert = mysqli_query($koneksi, "INSERT INTO tweet(content, id_user) VALUES('$content', '$id_user') ");
    }
    header("location:?pg=profil&tweet=berhasil");
}

$queryPosting = mysqli_query($koneksi, "SELECT tweet.* FROM tweet WHERE id_user='$id_user' ");


?>
<div class="row">
    <div class="col-sm-12" align="right">
        <button class="btn btn-outline-success fw-bold" style="border-radius: 18px;" data-bs-toggle="modal" data-bs-target="#exampleModal">Tweet</button>
    </div>
    <div class="col-sm-12 mt-3">
        <?php while ($rowPosting = mysqli_fetch_assoc($queryPosting)): ?>
            <div class="d-flex mb-5">
                <div class="flex-shrink-0">
                    <img src="upload/<?php echo !empty($rowUser['foto']) ? $rowUser['foto'] : 'https://placehold.co/500x200' ?>" alt="..." width="80" class="border border-2 rounded-circle">
                </div>
                <div class="flex-grow-1 ms-3">
                    <?php echo $rowPosting['content'] ?>
                    <?php if (!empty($rowPosting['foto'])): ?>
                        <img style="border-radius: 18px;" width="300" src="upload/<?php echo $rowPosting['foto'] ?>" alt="">
                    <?php endif; ?>
                </div>

                <!-- LIKE -->
                 <div class="status mt-1">
                    <input type="text" name="" id="user_id_like" value="<?php echo $rowPosting['id_user'] ?>">
                    <button class="btn btn-success btn-sm" onclick="toggleLike(<?php echo $rowPosting['id']; ?>)">Like (0)</button>
                 </div>

                <div class="flex-grow-1 ms-3">
                    <form method="POST" action="add_comment.php">
                        <input class="mb-2" type="text" name="status_id" value="<?php echo $rowPosting['id'] ?>">
                        <input type="text" name="user_id" value="<?php echo $rowPosting['id_user'] ?>">
                        <textarea class="form-control" name="comment_text" id="comment_text" placeholder="Tulis Balasan Anda..."></textarea>
                        <button class="btn btn-primary btn-sm mt-2" name="tess" type="submit">Kirim Balasan</button>
                    </form>

                    <div class="mt-2 alert" id="comment-alert" style="display: none;"></div>
                    <div class="mt-1">
                        <?php
                        if (isset($rowPosting['id']) && isset($rowPosting['id_user'])) {
                            $idStatus = $rowPosting['id'];
                            $userId = $rowPosting['id_user'];
                            $queryComment = mysqli_query($koneksi, "SELECT * FROM comments WHERE status_id = $idStatus AND user_id = $userId");
                            $rowCounts = mysqli_fetch_all($queryComment, MYSQLI_ASSOC);

                            foreach ($rowCounts as $rowCount) {

                        ?>
                                <span>
                                    <pre>Komentar : <?php echo $rowCount['comment_text'] ?></pre>
                                </span>

                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <hr>
        <?php endwhile ?>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Tweet</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <textarea name="content" id="summernote" class="form-control" placeholder="Apa yang sedang hangat dibicarakan?!"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="formFile" class="form-label">Default file input example</label>
                        <input class="form-control" name='foto' type="file" id="formFile">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="posting">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- <script>
    document.getElementById('comment-form').addEventListener('submit', function(e) {e.preventDefault();
        const formData = new FormData(this);

        fetch("add_comment.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const alertBox = document.getElementById('comment-alert');
                if (data.status === "success") {
                    alertBox.className = "alert alert-success";
                    alertBox.innerHTML = data.message;

                    // bersihkan textarea
                    document.getElementById('comment_text').value = '';
                    location.reload();
                } else {
                    alertBox.className = "alert alert-danger";
                    alertBox.innerHTML = data.message;
                }
                alertBox.style.display = "block";
            })
            .catch(error => console.error('Error:', error));
    })
</script> -->
<script>
    function toggleLike(statusId) {
        const userId = document.getElementById('user_id_like').value;
        fetch("like_status.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `status_id=${statusId}&user_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "liked") {
                alert("Liked!");
            } else if (data.status === "unliked") {
                alert("Unliked");
            }
            location.reload();
        })
        .catch(error => console.error("Error:", error));
    }
</script>