window.onload = function() {
$(document).ready(() => {
    $(document).on('show.bs.modal', '[id^=editAutoRespond]', function (event) {
        const button = $(event.relatedTarget);
        const type = button.data('type');
        const id = button.data('id');
        const select = $(this).find(`select[id^=typeEdit]`);

        select.val(type);

        loadAjaxContent(type, id);
    });

    $(document).on('change', 'select[id^=typeEdit]', function() {
        const type = $(this).val();
        const id = $(this).data('id');
        loadAjaxContent(type, id);
    });

    function loadAjaxContent(types, id) {
        $.ajax({
            url: `/form-message-edit/${types}`,
            type: "GET",
            data: { id: id, type: types, table: 'autoreplies', column: 'reply' },
            dataType: "html",
            success: (result) => {
                $(`.ajaxplaceEdit${id}`).html(result);
            },
            error: (error) => {
                console.log(error);
            },
        });
    }
});



function viewReply(id) {
    $.ajax({
        url: `/preview-message`,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        data: {
            id: id,
            table: "autoreplies",
            column: "reply",
        },
        dataType: "html",
        success: (result) => {
            $(".showReply").html(result);
            $("#modalView").modal("show");
        },
        error: (error) => {
            console.log(error);
        },
    });
    // 
}

$(".toggle-status").on("click", function () {
    let dataId = $(this).data("id");
    let isChecked = $(this).is(":checked");
    let url = $(this).data("url");
    $.ajax({
        url: url,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            status: isChecked ? "Active" : "Inactive",
            
            id: dataId,
        },
        success: function (result) {
            if (!result.error) {
                // find label
                let label = $(`.toggle-status[data-id=${dataId}]`)
                    .parent()
                    .find("label");
                // change label
                if (isChecked) {
                    label.text("Active");
                } else {
                    label.text("Inactive");
                }
                notyf.success(result.msg);
            }
        },
    });
});
$(".toggle-quoted").on("click", function () {
    let dataId = $(this).data("id");
    let isChecked = $(this).is(":checked");
    let url = $(this).data("url");
    $.ajax({
        url: url,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            quoted: isChecked ? "1" : "0",
            id: dataId,
        },
        success: function (result) {
            if(!result.error){
                // find label
                let label = $(`.toggle-quoted[data-id=${dataId}]`)
                    .parent()
                    .find("label");
                // change label
                if (isChecked) {
                    label.text("Yes");
                } else {
                    label.text("No");
                }
                notyf.success(result.msg);
            }
        },
    });
});
$(".toggle-read").on("click", function () {
    let dataId = $(this).data("id");
    let isChecked = $(this).is(":checked");
    let url = $(this).data("url");
    $.ajax({
        url: url,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            read: isChecked ? "1" : "0",
            id: dataId,
        },
        success: function (result) {
            if(!result.error){
                // find label
                let label = $(`.toggle-read[data-id=${dataId}]`)
                    .parent()
                    .find("label");
                // change label
                if (isChecked) {
                    label.text("Yes");
                } else {
                    label.text("No");
                }
                notyf.success(result.msg);
            }
        },
    });
});

$(".toggle-typing").on("click", function () {
    let dataId = $(this).data("id");
    let isChecked = $(this).is(":checked");
    let url = $(this).data("url");
    $.ajax({
        url: url,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            typing: isChecked ? "1" : "0",
            id: dataId,
        },
        success: function (result) {
            if(!result.error){
                // find label
                let label = $(`.toggle-typing[data-id=${dataId}]`)
                    .parent()
                    .find("label");
                // change label
                if (isChecked) {
                    label.text("Yes");
                } else {
                    label.text("No");
                }
                notyf.success(result.msg);
            }
        },
    });
});

$(document).ready(function () {
    $('.tags-input-add').each(function() {
        $(this).tagsinput({
            confirmKeys: [13, 32]
        });

        $(this).on('keypress', function (e) {
            if (e.which === 32) {
                e.preventDefault();
                $(this).tagsinput('add', $(this).val().trim());
                $(this).val('');
            }
        });
    });
});

$(document).ready(function () {
    $('.tags-input').each(function() {
        $(this).tagsinput({
            confirmKeys: [13, 32]
        });

        $(this).on('keypress', function (e) {
            if (e.which === 32) {
                e.preventDefault();
                $(this).tagsinput('add', $(this).val().trim());
                $(this).val('');
            }
        });

        $(this).on('itemAdded', function(event) {
            triggerAjax($(this));
        });

        $(this).on("change", function () {
            triggerAjax($(this));
        });
    });

    function triggerAjax(element) {
        // debouncing
        let timer;
        clearTimeout(timer);
        timer = setTimeout(() => {
            let keyword = element.val();
            let url = element.data("url");
            $.ajax({
                url: url,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                type: "POST",
                data: {
                    keyword: keyword,
                },
                success: (result) => {
                    notyf.success(result.msg);
                },
                error: (error) => {
                    console.log(error);
                },
            });
        }, 1000);
    }
});


$(".delay-update").on("keyup", function () {
    // debouncing
    let timer;

    clearTimeout(timer);
    timer = setTimeout(() => {
        let delay = $(this).val();
        let url = $(this).data("url");
		let dataId = $(this).data("id");
        $.ajax({
            url: url,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            data: {
                delay: delay,
				id: dataId,
            },
            success: (result) => {
               toastr.success(result.msg);
            },
            error: (error) => {
                console.log(error);
            },
        });
    }, 1000);
});


};