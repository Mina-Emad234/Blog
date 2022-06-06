<div class="container">
    <h2>All Comments</h2>
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
            <th scope="col">comment</th>
            <th scope="col">Created At</th>
            <th scope="col">Author</th>
            <th scope="col">Operation</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i=0;
        foreach ($comments as $comment):
            $i++;
            ?>
            <tr>
                <th scope="row"><?= $i ?></th>
                <td><?= $comment['comment_text'] ?></td>
                <td><?= $comment['created_at'] ?></td>
                <td><?= $comment['user_name'] ?></td>
                <td><?= $comment['active']==0?"<a href='".'activate/'.$comment['comment_id']."' class='btn btn-primary m-2'>Activate</a>":
                        "<a href='".'deactivate/'.$comment['comment_id']."' class='btn btn-danger m-2'>Deactivate</a>"; ?>
                    <a href="delete/<?= $comment['comment_id'] ?>" class='btn btn-info m-2'>Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="pagination display-flex justify-content-center">
        <?php if ($pager) :?>
            <?php $pagi_path='/admin/comments/all'; ?>
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