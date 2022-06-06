<div class="container">
    <?php foreach ($blog as $key => $post) : ?>
        <h1 class="mb-3"><?= $post['blog_title'] ?></h1>
        <hr style="border-top: 3px solid black !important">
        <div id="blog_content">
            <?= htmlspecialchars_decode($post['blog_body'], ENT_HTML5) //Convert special HTML entities back to html tag ?>
        </div>
    <?php if (session()->has('session-id')): ?>
            <div id="msg" class="alert alert-dismissible fade hidden" role="alert">
                <div>Wrong Username and Password.</div>
                <button type="button" class="close pl-0" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
    <div class="alert alert-info">
        <form action="" method="POST" id="commentform">
            <?= csrf_field(); ?>
            <input type="hidden" id="post_id" name="post_id" value="<?= $post['blog_id'] ?>">
            <div class="form-group">
                <label for="comment">Add Comment</label>
                <textarea name="comment" class="form-control" id="comment" cols="80"></textarea>
            </div>
            <input type="submit" id="add_comment" class="btn btn-primary submit-btn" value="Add">
        </form>
    </div>
        <?php $i=1; ?>
        <?php foreach ($comments as $key => $comment) :;?>
            <div class="card">
            <h5 class="card-header">
                <img src="<?= '/uploads/' . $comment['image']?>" class="rounded-circle" width="20" height="20" title="<?= $comment['user_name']?>">
                <?= $comment['user_name'] ?></h5>
            <div class="card-body">
                <p class="card-text"><?= $comment['comment_text'] ?></p>
                <?php   $sess = \Config\Services::session();
                        $id=(array)verify_jwt((string)$sess->get('session-id'));
                    if($sess->has('session-id') && $id['id'] == $comment['user_rand_id']):

                ?>
                <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal<?= $i; ?>">Delete</a>
                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalUpdate<?= $i; ?>">Update</a>
                    <small class="float-right"><?= $comment['created_at'] ?></small>
                <?php endif; ?>
            </div>
        </div>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal<?= $i; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Are you sure that you want to delete this comment?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary delete" data-dismiss="modal" data-id="<?= $comment['comment_id']?>">Yes</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="exampleModalUpdate<?= $i; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6>Update Comment</h6>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?= form_open(); ?>
                        <div class="modal-body">
                           <input type="hidden" id="comment_id" value="<?= $comment['comment_id']?>">
                            <textarea id="comment_text"><?= $comment['comment_text']?></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-primary update">Update</button>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        <?php
                $i++;
            endforeach; ?>
        <?php endif; ?>
        <?php endforeach; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script>
    $("#add_comment").on('click', function(e){
        e.preventDefault();
        $("#comment").val();
        $("#post_id").val();
        var format = new FormData($('#commentform')[0]);


         $.ajax({
            url: "<?= base_url() ?>/comment/create",
            method: "POST",
            dataType: "json",

             data:format,
            processData: false,
            contentType:false,
            cache:false,
            success: function(data) {
                Swal.fire({
                    icon: 'success',
                    title: 'Added',
                    text: 'comment Created successfully',
                });//alert success
                if(data){
                    setTimeout(function (){
                        location.reload();
                    },5000);
                }            },
            error: function(err) {

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "Something went wrong",
                    footer: '<a href>What went wrong?</a>'
                });//alert error
                if(err){
                    // setTimeout(function (){
                    //     location.reload();
                    // },5000);
                }
            }
        });
    });
    $(".delete").click(function() {//form button
        var csrf_name = "<?= csrf_token() ?>";
         var csrf_hash = $("[name=csrf_test_name]").val();
        var xhr = $.ajax({
            url: "<?= base_url() ?>/comment/delete",
            method: "POST",
            dataType: "json",
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            },
            data: {
                [csrf_name]: csrf_hash,
                'id': JSON.stringify($(this).data('id'))
            },
            success: function(data) {
                //A warning message, with a function attached to the "Confirm" button
                Swal.fire({
                    icon: 'success',
                    title: 'deleted',
                    text: 'comment Deleted successfully',
                });//alert success
                var csrf = xhr.getResponseHeader("X-CSRF-TOKEN");
                $("[name=csrf_test_name]").val(csrf);//add csrf to hidden input
                if(data){
                    setTimeout(function (){
                       location.reload();
                    },5000);
                }
            },
        });
    });
    $(".update").click(function() {//form button
        var csrf_name = "<?= csrf_token() ?>";
        var csrf_hash = $("[name=csrf_test_name]").val();
        var xhr = $.ajax({
            url: "<?= base_url() ?>/comment/update",
            method: "POST",
            dataType: "json",
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            },
            data: {
                [csrf_name]: csrf_hash,
                'id': JSON.stringify($("#comment_id").val()),
                'comment': JSON.stringify($("#comment_text").val())
            },
            success: function(data) {
                //A warning message, with a function attached to the "Confirm" button
                Swal.fire({
                    icon: 'success',
                    title: data.msg,
                    text: 'comment Deleted successfully',
                });//alert success
                var csrf = xhr.getResponseHeader("X-CSRF-TOKEN");
                $("[name=csrf_test_name]").val(csrf);//add csrf to hidden input
                if(data){
                    setTimeout(function (){
                        location.reload();
                    },5000);
                }
            },
        });
    });
</script>
