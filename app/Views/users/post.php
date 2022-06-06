<script src="https://cdn.ckeditor.com/ckeditor5/17.0.0/classic/ckeditor.js"></script>
<link rel="stylesheet" href="/chosen/chosen_v1.8.7/chosen.css">
<div class="container">
    <?= form_open("post/create", [
        'enctype' => 'multipart/form-data'
    ]) ?>
    <div class="form-group">
        <label for="post_title" class="font-weight-bolder">Post Title:</label>
        <?php $post_title = ["id" => "post_title", "class" => "form-control", "name" => "post_title", "placeholder" => "Enter your post title", "required" => "true","value"=>set_value('post_title')]; ?>
        <?= form_input($post_title) ?>
    </div>
    <div class="form-group">
        <label for="post_title" class="font-weight-bolder">Post Category:</label>
        <select id="post_categories" name="post_category[]" class="chosen-select" data-placeholder="Search Category" multiple="multiple" style="width:100%;">
            <?php foreach ($cat_options as $key => $value) ://categories data ?>
                <option id="<?= $value["cat_id"]//ids of cats ?>"><?= ucfirst($value["cat_name"]) //names of cats?></option>';
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="editor" class="font-weight-bolder">Post content:</label>
        <textarea name="post_content" id="editor" cols="30" rows="10"></textarea>
    </div>
    <input class="btn btn-success submit-btn" type="submit" name="" value="SAVE AND POST">
    <?= form_close() ?>
</div>
<!--CKEditor (formerly known as FCKeditor) is a WYSIWYG rich text editor which enables
writing content directly inside of web pages or online applications.-->
<script>
    let post_editor_data;
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote', 'insertTable', '|', 'undo', 'redo']
        })
        .then(newEditor => {
            post_editor_data = newEditor;
        })
        .catch(error => {
            console.error(error);
        });
</script>
<!--Chosen is a jQuery plugin that makes long, unwieldy select boxes much more user-friendly.-->
<script src="/chosen/chosen_v1.8.7/chosen.jquery.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script>
    $(".chosen-select").chosen({
        no_results_text: "Oops, nothing found!",
        max_selected_options: 4,
    });//select box validation
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "3000",
        "hideDuration": "1000",
        "timeOut": "7000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
</script>
<?php
#display error using notification
$validation_error = \Config\Services::validation()->getErrors();
if (!empty($validation_error)) {//validation errors
    foreach ($validation_error as $key => $value) {
        echo '<script>tostr.error("'.$value.'")</script>';
    }
}
?>