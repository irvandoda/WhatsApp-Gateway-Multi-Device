    $(function () {
        (function ($) {
            $.fn.filemanager = function (type, options) {
                type = type || 'file';
                function getLocalePrefix() {
                    var seg = (window.location.pathname || '/').split('/').filter(Boolean)[0] || '';
                    if (/^[a-z]{2}(-[A-Z]{2})?$/.test(seg)) return '/' + seg;
                    return '';
                }
                this.on('click', function (e) {
                    e.preventDefault();
                    var route_prefix = (options && options.prefix) ? options.prefix : (getLocalePrefix() + '/file-manager/fm-button');
                    var target_input = $('#' + $(this).data('input'));
                    var target_preview = $('#' + $(this).data('preview'));
                    window.fmSetLink = function (url) {
                        if (target_input.length) {
                            target_input.val('').val(url).trigger('change');
                        }
                        if (target_preview.length) {
                            target_preview.html('');
                            var img = $('<img>').css('height', '5rem').attr('src', url);
                            target_preview.append(img).trigger('change');
                        }
                    };
                    window.SetUrl = function (items) {
                        var urls = items.map(function (i) { return i.url; });
                        if (target_input.length) {
                            target_input.val('').val(urls.join(',')).trigger('change');
                        }
                        if (target_preview.length) {
                            target_preview.html('');
                            items.forEach(function (i) {
                                var src = i.thumb_url || i.url;
                                target_preview.append($('<img>').css('height', '5rem').attr('src', src));
                            });
                            target_preview.trigger('change');
                        }
                    };
                    window.open(route_prefix, 'FileManager', 'width=1000,height=500,scrollbars=yes');
                    return false;
                });
                return this;
            };
        })(jQuery);

        $('#image').filemanager('file');
        $('#image-button').filemanager('file');
        $('#image-sticker').filemanager('file');
    });