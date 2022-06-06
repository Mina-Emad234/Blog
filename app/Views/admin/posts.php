<div class="container">
    <h2>All Posts</h2>
    <?php if(Session()->has('activated')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div><?= Session()->get('activated') ?></div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>
    <?php if(Session()->has('deactivated')) : ?>
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <div><?= Session()->get('deactivated') ?></div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>
    <?php if(Session()->has('deleted')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div><?= Session()->get('deleted') ?></div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>
    <table class="table table-success table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">title</th>
                <th scope="col">body</th>
                <th scope="col">Posted On</th>
                <th scope="col">Author</th>
                <th scope="col">Operation</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $i=0;
            foreach ($posts as $post):
            $i++;
            ?>
            <tr>
                <th scope="row"><?= $i ?></th>
                <td><?= substr($post['blog_title'],0,15)."..."; ?></td>
                <td><?= substr($post['blog_body'],0,50)."..."; ?></td>
                <td><?= $post['blog_created_time'] ?></td>
                <td><?= $post['user_name'] ?></td>
                <td><?= $post['active']==0?"<a href='".'activate/'.$post['blog_id']."' class='btn btn-primary m-2'>Activate</a>":
                        "<a href='".'deactivate/'.$post['blog_id']."' class='btn btn-danger m-2'>Deactivate</a>"; ?>
                    <a href="delete/<?= $post['blog_id'] ?>" class='btn btn-info m-2'>Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="pagination display-flex justify-content-center">
        <?php if ($pager) :?>
            <?php $pagi_path='/admin/posts/all'; ?>
            <?php $pager->setPath($pagi_path); ?>
            <?= $pager->links() ?>
        <?php endif ?>
    </div>
</div>
<script>
    $(function (){
        $('ul li').addClass('page-item');
        $('ul li a').addClass('page-link');
    });
</script>