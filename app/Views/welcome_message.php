<div class="container">
  <?php
  $blog = $posts;
  $blog_cat = $categories;//[blog_id=>[cat_name,cat_name]]
  $blogs_chunk = array_chunk($blog, 2);//chunk posts array into arrays
  $badge_class = ["badge-primary", "badge-secondary", "badge-success", "badge-danger", "badge-warning", "badge-info", "badge-dark"];
  $sess = \Config\Services::session();
    ?>
  <?php if ($blog === null) : ?>
    <div class="row mb-2">
      <h2>No blogs are present </h2>
    </div>
  <?php else : ?>
    <?php foreach ($blogs_chunk as $key => $items) ://get chunked arrays ?>
      <div class="row mb-2">
        <?php foreach ($items as $key => $value) ://get elements from chunked array ?>
          <div class="col-md-6">
            <div class="card mb-3">
              <div class="row no-gutters border rounded overflow-hidden flex-md-row">
                  <div class="card-body">
                    <?php $temp_array = $blog_cat[$value['blog_id']];//cats[blog_id]-->get cat_name array ?>
                    <?php foreach ($temp_array as $key => $catg) : ?>
                      <?php $single_badge = array_rand($badge_class, 1);//Pick one or more random keys out of an array ?>
                      <a href="/category/<?= $catg ?>" class="d-inline-block mb-2 badge <?=$badge_class[$single_badge]//get random class?>"><?= $catg //cat_name?></a>
                    <?php endforeach; ?>
                    <h2 class="card-title mb-0"><?= $value['blog_title'] ?></h2>
                    <p class="card-text mb-1"><small class="text-muted"><?=$value['blog_created_time'] ?></small></p>
                    <p class="card-text"><?= strip_tags(htmlspecialchars_decode(word_limiter($value['blog_body'], 19)), ENT_HTML5)?></p>
                    <a href="/post/display/<?= $value['blog_id'] ?>" class="stretched-link">Continue reading</a>
                  </div>
                </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<?php
#display session value in js notification
if($sess->has('success_verify')){
    echo '<script>Swal.fire({
        icon: "success",
        title: "Verified",
        text: "Email Verified Successfully!",
    });</script>';
    $sess->destroy();
}
?>
