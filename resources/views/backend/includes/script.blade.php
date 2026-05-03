<script src="{{ asset('public/web-assets/backend/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('public/web-assets/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/web-assets/backend/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}">
</script>
<script src="{{ asset('public/web-assets/backend/plugins/toastr/toastr.min.js') }}"></script>
{!! Toastr::message() !!}
<script src="{{ asset('public/web-assets/backend/dist/js/adminlte.js') }}"></script>
<script src="{{ asset('public/web-assets/backend/plugins/dropzone/min/dropzone.min.js') }}"></script>
<script>
    //Gobal variable for media
    let perPage = 30;
    let current_page = 1;
    let filter_by_user = false;
    let input_name = "";
    let selected_items = [];
    let is_multiple = false;
    let total_items = 0;
    let for_media_manager = true;
    let ready_for_insert_items = [];
    let media_search_query = "";
    let media_filter_type = "all";
    let multi_select_mode = false;
    let media_search_timer = null;
    /**
     * 
     *Initinalization media Manager
     * 
     **/

    function initMediaManager() {
        "use strict";

        let file_limit = 0
        let $dropzoneElement = $("#media-upload");

        // Destroy existing instance if present
        if ($dropzoneElement[0] && $dropzoneElement[0].dropzone) {
            $dropzoneElement[0].dropzone.destroy();
        }

        Dropzone.autoDiscover = false;
        $("#media-upload").dropzone({
            url: '{{ route('upload.media.file') }}',
            parallelUploads: 3,
            uploadMultiple: true,
            maxFilesize: 256,
            timeout: 3600000,
            accept: function(file, done) {
                if (file_limit > 3) {
                    toastr.error("You cannot upload more than 9 files", "Error!");
                }
                if (file_limit <= 3) {
                    if (file.type != "image/webp" && file.type != "image/jpeg" && file.type !=
                        "image/svg" && file.type != "image/jpg" &&
                        file.type != "image/png" &&
                        file
                        .type != "image/gif" && file.type != "application/zip" && file.type !=
                        "application/pdf" &&
                        file.type != "video/mp4") {
                        toastr.error("Please upload a file of type jpeg/jpg/svg/png/gif/pdf/zip/mp4",
                            "Error!");
                    } else {
                        done();
                    }
                }
            },
            success: function(file, response) {
                if (response.result.success) {
                    $('#upload-files-tab').removeClass('active');
                    $('#upload-files').removeClass('active show');
                    $('#media-library-tab').addClass('active');
                    $('#media-library').addClass('active show');
                    current_page = 1;
                    getMediaItemsList();
                }
            },
            error: function(file, message) {
                if (file.size > 256000000) {
                    toastr.error("Unable to upload file larger tha 20MB ", "Error!");
                } else {
                    toastr.error(message, "Error!");
                }
                return false;
            },
            init: function() {
                this.on("addedfile", function(file) {
                    file_limit = file_limit + 1
                });
            }
        });
    }
    /**
     * 
     *Will get media  modal date
     * 
     **/
    function getMediaModalData(name, value, input_is_multiple) {
        "use strict";
        current_page = 1;
        input_name = name;
        is_multiple = input_is_multiple == 'true' ? true : false;
        selected_items = value;
        for_media_manager = false;
        getMediaItemsList();
        previewSelectedFiles();
        $('.media-input-insert-btn').removeClass('d-none');

    }
    /**
     *
     * Remove file input field value
     *
     **/
    function removeFileInputValue(name, input_is_multiple) {
        "use strict";
        let is_multiple_remove = input_is_multiple == 'true' ? true : false;
        if (!is_multiple_remove) {
            document.getElementById('input-' + name).value = '';
            // Hide image container, show placeholder
            var imgWrap = document.getElementById('single-img-wrap-' + name);
            var placeholder = document.getElementById('single-placeholder-' + name);
            if (imgWrap) imgWrap.style.display = 'none';
            if (placeholder) placeholder.style.display = '';
            var img = document.getElementById('media-input-preview-' + name);
            if (img) img.src = '';
        }
    }
    /**
     * Select file items
     * 
     **/
    function selectMedia(e, id) {
        "use strict";
        let item_id = "list-item-" + id;

        if (!e.ctrlKey && !e.shiftKey && !multi_select_mode) {
            // Single-select: deselect all others
            $('.single-media-item').removeClass('selected');
            selected_items = [];
            selected_items.push(id);
            $("#" + item_id).addClass('selected');
        } else {
            // Multi-select: toggle clicked item
            let duplicate_item = selected_items.includes(id);
            if (duplicate_item) {
                $("#" + item_id).removeClass('selected');
                selected_items = selected_items.filter(x => x !== id);
            } else {
                selected_items.push(id);
                $("#" + item_id).addClass('selected');
            }
        }

        previewSelectedFiles();
    }
    /**
     * Display selected files details
     * 
     **/
    function previewSelectedFiles() {
        "use strict";
        let selected_ids_string = JSON.stringify(selected_items);
        //Preview Items
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            type: "POST",
            data: {
                items: selected_ids_string
            },
            url: '{{ route('media.selected.file.details') }}',
            success: function(response) {
                if (response.success) {
                    $("#media-preview-section").html(response.view);
                    ready_for_insert_items = response.items;
                }
            },
            error: function(response) {
                console.log(response);
            }
        });
    }
    /**
     * Set file input field value
     * 
     **/
    function setMediaInputValue() {
        "use strict";
        //No Items Selected
        if (ready_for_insert_items.length < 1) {
            toastr.error('No Item Selected', "Error!");
            return;
        }
        //Multiple selected for single input
        if (!is_multiple && ready_for_insert_items.length > 1) {
            toastr.error('You can not insert multiple items', "Error!");
            return;
        }
        //Set single input filed value
        if (!is_multiple) {
            let file_path = ready_for_insert_items[0].path;

            document.getElementById('input-' + input_name).value = file_path;

            // Show image container, hide placeholder
            var imgWrap = document.getElementById('single-img-wrap-' + input_name);
            var placeholder = document.getElementById('single-placeholder-' + input_name);
            var img = document.getElementById('media-input-preview-' + input_name);
            if (imgWrap) imgWrap.style.display = '';
            if (placeholder) placeholder.style.display = 'none';
            if (img) img.src = '/public/' + file_path;

            $("#mediaManagerModal").modal('hide');
            return;
        }

        // Set multiple input field values
        if (is_multiple) {
            let inputEl = document.getElementById('input-' + input_name);
            let thumbsEl = document.getElementById('multi-thumbs-' + input_name);
            let currentPaths = inputEl.value ? inputEl.value.split(',').filter(function(p) {
                return p.trim();
            }) : [];

            ready_for_insert_items.forEach(function(item) {
                let p = item.path;
                if (!currentPaths.includes(p)) {
                    currentPaths.push(p);
                    // Remove placeholder if present
                    let placeholder = document.getElementById('multi-placeholder-' + input_name);
                    if (placeholder) placeholder.remove();

                    let div = document.createElement('div');
                    div.className = 'media-input-container';
                    div.dataset.path = p;
                    div.innerHTML = '<img src="/public/' + p + '" class="media-input-preview" alt="">' +
                        '<button type="button" class="input-remove-btn multi-remove-btn" data-input="' +
                        input_name + '" data-path="' + p + '"><i class="fas fa-times"></i></button>';
                    thumbsEl.appendChild(div);
                }
            });

            inputEl.value = currentPaths.join(',');
            $('#mediaManagerModal').modal('hide');
            return;
        }
    }

    /**
     * Will get files list
     * 
     **/
    function getMediaItemsList(pagination) {
        "use strict";
        if (pagination) {
            $('.spinner').addClass("lds-ellipsis");
            $(".media-load-more-btn-text").text("");
        }

        $.post("{{ route('media.file.list') }}", {
                _token: '{{ csrf_token() }}',
                page: current_page,
                per_page: perPage,
                selected_items: JSON.stringify(selected_items),
                filter_by_user: filter_by_user,
                search: media_search_query,
                filter_type: media_filter_type
            },
            function(response, status) {
                if (pagination) {
                    $('.spinner').removeClass("lds-ellipsis");
                }
                if (response.success) {
                    $('#filtered_media').removeClass('area-disabled');
                    if (current_page < 2) {
                        $('#filtered_media').html(response.items);
                    } else {
                        $('#filtered_media').append(response.items);
                    }

                    current_page = response.currentPage + 1;
                    if (!response.has_more_page) {
                        if (!$(".media-load-more-btn").hasClass(".d-none")) {
                            $(".media-load-more-btn").addClass('d-none');

                        }
                    }
                    if (response.has_more_page) {
                        $(".media-load-more-btn").removeClass('d-none');
                        $('.media-load-more-btn').addClass('btn-primary');
                        $(".media-load-more-btn-text").text("Load More");
                    }
                }

            }).fail(function(xhr, status, error) {
            if (pagination) {
                $('.spinner').removeClass("lds-ellipsis");
            }
        });
    }

    (function($) {
        "use strict";
        $(document).on('click', '.media-load-more-btn', function(e) {
            console.log(e);
        });
    })


    $(document).ready(function() {
        getNotification();
        setInterval(getNotification, 1000 * 30);
        /**
         * Will get notification
         * 
         **/
        function getNotification() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: "Get",
                url: '{{ route('admin.notification.list') }}',
                success: function(response) {
                    if (response.success) {
                        $(".notification-list-items").html('');
                        let total_notification = response.notifications.length;
                        $('.notification-counter').html(total_notification);
                        if (total_notification > 0) {
                            $('.mark-as-read-all').removeClass('d-none');
                            for (let i = 0; i < total_notification; i++) {
                                let id = response.notifications[i]['id'];
                                let item =
                                    "<a href='#' id=" + id +
                                    " data-id=" + id +
                                    " class='single-notification dropdown-item'><p>" +
                                    response.notifications[i]['message'] +
                                    " </p><span class =' text-muted text-sm'> " +
                                    response.notifications[i]['time'] +
                                    " </span></a><div class='dropdown-divider'></div> ";
                                //Append list   
                                $(".notification-list-items").append(item);
                                $('.mark-as-read-all').removeClass('d-none');
                            }
                        } else {
                            $(".notification-list-items").html(
                                '<p class="p-2">{{ __tr('You have no unread notification') }}</p>'
                            );
                            $('.mark-as-read-all').addClass('d-none');
                        }
                    }
                },
                error: function(response) {
                    $(".notification-list-items").html(
                        '<p class="p-2">{{ __tr('You have no unread notification') }}</p>'
                    );
                }
            })
        }

        /**
         * Will mark as read single notification
         **/
        $(document).on('click', '.single-notification', function(e) {
            "use strict";
            let id = $(this).data('id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('admin.notification.mark.as.read.single') }}',
                data: {
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        if (response.link != null) {
                            let link = response.link;
                            window.location.href = link;
                        } else {
                            $("#" + id).remove();
                        }
                    }
                },
                error: function(response) {
                    $("#" + id).remove();
                }
            })
        });
        /**
         * Will mark as read all notifications
         **/
        $(".mark-as-read-all").on("click", function(e) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('admin.notification.mark.as.read.all') }}',
                success: function(response) {
                    if (response.success) {
                        $('.notification-counter').html(0);
                        $(".notification-list-items").html(
                            '{{ __tr('You have no unread notification') }}');
                        $('.mark-as-all-read').addClass('d-none');
                    }
                },
                error: function(response) {}
            })
        });
    });

    // Remove individual image from multiple media input
    $(document).on('click', '.multi-remove-btn', function() {
        var inputName = $(this).data('input');
        var path = $(this).data('path');
        var inputEl = document.getElementById('input-' + inputName);
        var paths = inputEl.value.split(',').filter(function(p) {
            return p.trim() && p.trim() !== path;
        });
        inputEl.value = paths.join(',');
        $(this).closest('.media-input-container').remove();
        // Show placeholder when no images remain
        if (paths.length === 0) {
            var thumbsEl = document.getElementById('multi-thumbs-' + inputName);
            var placeholder = document.createElement('div');
            placeholder.className = 'multi-media-placeholder';
            placeholder.id = 'multi-placeholder-' + inputName;
            placeholder.innerHTML = '<i class="fas fa-images"></i><span>No images selected</span>';
            thumbsEl.appendChild(placeholder);
        }
    });

    // Media search input (debounced)
    $(document).on('input', '#media-search-input', function() {
        var val = $(this).val();
        $('#media-search-clear-btn').toggleClass('d-none', val.length === 0);
        clearTimeout(media_search_timer);
        media_search_timer = setTimeout(function() {
            media_search_query = val;
            current_page = 1;
            getMediaItemsList();
        }, 400);
    });

    // Clear search
    function clearMediaSearch() {
        $('#media-search-input').val('');
        $('#media-search-clear-btn').addClass('d-none');
        media_search_query = '';
        current_page = 1;
        getMediaItemsList();
    }

    // Media type filter pills
    $(document).on('click', '.media-filter-pill', function() {
        $('.media-filter-pill').removeClass('active');
        $(this).addClass('active');
        media_filter_type = $(this).data('type');
        current_page = 1;
        getMediaItemsList();
    });

    // Multi-select toggle button
    $(document).on('click', '#multi-select-btn', function() {
        multi_select_mode = !multi_select_mode;
        $(this).toggleClass('active', multi_select_mode);
        if (!multi_select_mode && selected_items.length > 1) {
            var last = selected_items[selected_items.length - 1];
            $('.single-media-item').removeClass('selected');
            $('#list-item-' + last).addClass('selected');
            selected_items = [last];
            previewSelectedFiles();
        }
    });

    // Reset toolbar state when media modal opens
    $(document).on('show.bs.modal', '#mediaManagerModal', function() {
        media_search_query = '';
        media_filter_type = 'all';
        multi_select_mode = false;
        $('#media-search-input').val('');
        $('#media-search-clear-btn').addClass('d-none');
        $('.media-filter-pill').removeClass('active');
        $('.media-filter-pill[data-type="all"]').addClass('active');
        $('#multi-select-btn').removeClass('active');
    });

    // Copy URL button in media preview sidebar (delegated — content is AJAX-injected)
    $(document).on('click', '.copy-url-btn', function() {
        var url = $(this).closest('.input-group').find('.media-url-input').val();
        if (!url) return;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(function() {
                toastr.success('URL copied to clipboard', 'Copied');
            });
        } else {
            var $tmp = $('<input>');
            $('body').append($tmp);
            $tmp.val(url).select();
            document.execCommand('copy');
            $tmp.remove();
            toastr.success('URL copied to clipboard', 'Copied');
        }
    });

    function sendFile(image, editor, section_id) {
        "use strict";
        let data = new FormData();
        data.append("image", image);
        data.append("_token", '{{ csrf_token() }}');
        $.ajax({
            data: data,
            type: "POST",
            url: '{{ route('utility.store.editor.image') }}',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.url) {
                    var image = $('<img>').attr('src', data.url);
                    $('#' + section_id).summernote("insertNode", image[0]);
                }
            },
            error: function(data) {
                toastr.error('Image Insert Failed', "Error!");
            }
        });
    }
</script>
