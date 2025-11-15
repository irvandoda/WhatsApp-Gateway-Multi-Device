<label class="form-label">{{__('Image Url')}} </label>
<div class="input-group media-area">
    <span class="input-group-btn">
        <a id="image-sticker" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
            <i class="fa fa-picture-o"></i> {{__('Choose')}}
        </a>
    </span>
    <input id="thumbnail-sticker" class="form-control" type="text" name="url">
</div>


<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    $('#image-sticker').filemanager('file')

    $('.type-audio').hide()


    // on media_type change
    // $('input[name="media_type"]').on('change', function() {
    //     let type = $(this).val()
    //     if (type == 'audio') {
    //         $('.type-audio').show()
    //     } else {
    //         $('.type-audio').hide()
    //     }

    //     if (type == 'image' || type == 'video' || type == 'pdf' || type == 'xls' || type == 'xlsx' || type ==
    //         'doc' || type == 'docx' || type == 'zip') {
    //         $('.caption-area').show()
    //     } else {
    //         $('.caption-area').hide()
    //     }
    // })
</script>