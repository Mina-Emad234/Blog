<div class="container">
    <label for="categories" class=" font-weight-bolder">Create a new categories</label>
    <?= form_open() ?>
    <div class="form-row">
        <div class="col-md-10">
            <?php $cat_data = [
                'id'       =>   'categories',
                'class'    =>   'form-control',
                'name'     =>   'category',
                'placeholder' => 'Create a new categories'
            ]; ?>
            <?= form_input($cat_data) ?>
        </div>
        <div class="col-md-2">
            <?= form_button(['class' => 'btn btn-primary btn-block', 'content' => 'Create', 'id' => 'cat_update']) ?>
        </div>
    </div>
    <?= form_close() ?>
    <div class="card mt-4">
        <div class="card-body">
            <ul id="categories_data">
                <?php $badge_class = ["badge-primary", "badge-secondary", "badge-success", "badge-danger", "badge-warning", "badge-info", "badge-dark"]; ?>
                <?php if (!empty($cat)) ://if categories array not empty ?>
                    <h2>All Categories</h2>
                    <?php if(Session()->has('catUpdated')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div><?= Session()->get('catUpdated') ?></div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif;
                if(Session()->has('catDeleted')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div><?= Session()->get('catDeleted') ?></div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                    <table class="table table-success table-striped">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Category</th>
                            <th scope="col">Created by</th>
                            <th scope="col">Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i=0;
                    foreach ($cat as $key => $value) :
                            $i++;
                            ?>
                            <tr>
                                <th scope="row"><?= $i ?></th>
                                <td><?= $value['cat_name'] ?></td>
                                <td><?= $value['user_name'] ?></td>
                                <td><?= $uuid == $value['user_rand_id']? " <a href='#' class='btn btn-danger' data-toggle='modal' data-target='#exampleCat". $i . "'>Delete</a>
                                    <a href='#' class='btn btn-primary' data-toggle='modal' data-target='#exampleCatUpdate". $i . "'>Update</a>": "<div>Operation Not allowed</div>"; ?>
                                </td>
                            </tr>

                    <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Pagination -->
                <div class="pagination display-flex justify-content-center">
                    <?php if ($pager) :?>
                            <?php $pagi_path='/users/categories'; ?>
                            <?php $pager->setPath($pagi_path); ?>
                            <?= $pager->links() ?>
                        <?php endif ?>
                    </div>
                    <?php
                    $i=0;
                      foreach ($cat as $key => $value) :
                    $i++;
                    ?>
                    <div class="modal fade" id="exampleCat<?= $i; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure that you want to delete <?= $value['cat_name'] ?> category?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                    <a  class="btn btn-primary"  href="/users/deleteCategory/<?= $value['cat_id']?>">Yes</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="exampleCatUpdate<?= $i; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6>Update Category</h6>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <?= form_open('/users/updateCategory'); ?>
                                <div class="modal-body">
                                    <input type="hidden" name="cat_id" value="<?= $value['cat_id']?>">
                                    <textarea name="cat_name"><?= $value['cat_name']?></textarea>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" class="btn btn-primary" name="update_cat" value="update">
                                </div>
                                <?= form_close(); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                <?php else : //if categories array empty?>

                    <script>
                        Swal.fire({//alert
                            icon: 'error',
                            title: 'No data',
                            text: "There is no category available",
                            footer: 'Create a category of your own'
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        $("#cat_update").on('click', function() {//click form button
            var csrf_name = "<?= csrf_token() //create token?>";
            var csrf_hash = $("[name=csrf_test_name]").val();//hidden input value

            var category = $("#categories").val();//categories input value
            var cat_j_data = {
                "category_name": category
            }//category_name
            var xhr = $.ajax({
                url: "<?= base_url() ?>/users/cat_create",//create new category
                method: "POST",
                dataType: "json",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                data: {
                    [csrf_name]: csrf_hash,
                    "cat_data": JSON.stringify(cat_j_data)
                },//data sent
                success: function(data) {
                    if (data.msg == "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Created',
                            text: 'Your category has been created',
                        });//success alert
                        setTimeout(function() {
                            location.reload();
                        }, 2000);//reload page
                    } else {
                        if (data.msg == "error") {//in case of error validation
                            Swal.fire({
                                icon: 'error',
                                title: "Invalid Input",
                                text: data.category_name
                            });//error alert
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Already exists',
                                text: "The category already exists"
                            });//alert data exists
                        }
                    }
                    var csrf = xhr.getResponseHeader("X-CSRF-TOKEN");//csrf_hashed_token
                    $("[name=csrf_test_name]").val(csrf);//add csrf
                },
                error: function(err) {//error if cat_data not inserted
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Something went wrong",
                        footer: '<a href>What went wrong?</a>'
                    });//if error
                    var csrf = xhr.getResponseHeader("X-CSRF-TOKEN");
                    $("[name=csrf_test_name]").val(csrf);
                }
            });
        });
    </script>
<script>
    $(function (){
        $('ul li').addClass('page-item');
        $('ul li a').addClass('page-link');
    });
</script>
</div>