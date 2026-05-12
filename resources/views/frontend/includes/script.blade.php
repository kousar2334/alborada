<script src="{{ asset('/public/web-assets/common/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('/public/web-assets/frontend/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/public/web-assets/frontend/js/plugin.js') }}"></script>
<script src="{{ asset('/public/web-assets/frontend/js/main.js') }}"></script>
<script src="{{ asset('/public/web-assets/frontend/js/dynamic-script.js') }}"></script>
<script src="{{ asset('/public/web-assets/common/js/toastr.min.js') }}"></script>
<script src="{{ asset('/public/web-assets/backend/plugins/select2/js/select2.min.js') }}"></script>
<script>
    (function($) {
        "use strict";

        $(document).ready(function() {

            /* =====================================
             * Newsletter Subscription
             * ===================================== */
            var $form = $('#footer-newsletter-form');

            if ($form.length) {

                var $btn = $form.find('button[type="submit"]');
                var $msg = $('#newsletter-msg');
                var $email = $('#newsletter-email');

                $form.on('submit', function(e) {
                    e.preventDefault();

                    var emailVal = $.trim($email.val());

                    // UI Reset
                    $btn.prop('disabled', true).text('Subscribing...');
                    $msg.hide().removeClass('text-success text-danger');

                    $.ajax({
                        url: '{{ route('newsletter.subscribe') }}',
                        type: 'POST',
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: JSON.stringify({
                            email: emailVal
                        }),

                        success: function(res) {
                            showMessage($msg, res.success, res.message);

                            if (res.success) {
                                $email.val('');
                            }
                        },

                        error: function(xhr) {
                            var msg = xhr.responseJSON?.message ||
                                'Something went wrong.';
                            showMessage($msg, false, msg);
                        },

                        complete: function() {
                            $btn.prop('disabled', false).text('Subscribe');
                        }
                    });
                });
            }


        });

        /* =====================================
         * Helper Function
         * ===================================== */
        function showMessage($el, isSuccess, text) {
            $el
                .removeClass('text-success text-danger')
                .addClass(isSuccess ? 'text-success' : 'text-danger')
                .text(text || 'Something went wrong.')
                .fadeIn();
        }

    })(jQuery);
</script>
{!! Toastr::message() !!}
