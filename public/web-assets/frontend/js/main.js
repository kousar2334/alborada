(function ($) {
    "use strict";

    jQuery(document).ready(function ($) {
        $(".select2_activation").select2();

       
        /*-----------------------------------
            Navbar Toggler Icon
        ------------------------------*/
        $(document).on("click", ".navbar-toggler", function () {
            $(this).toggleClass("active");
        });

        /*-----------------------------------
            Sticky Header on Scroll
        -----------------------------------*/
        var $header = $("header.header-style-01");
        $(window).on("scroll", function () {
            if ($(this).scrollTop() > 100) {
                if (!$header.hasClass("sticky-bar")) {
                    $header.removeClass("sticky-bar")[0].offsetHeight; // force reflow to replay animation
                    $header.addClass("sticky-bar");
                }
            } else {
                $header.removeClass("sticky-bar");
            }
        });

        
        /*-----------------------------------
        Popup Modal
        -----------------------------------*/
        $(document).on(
            "click",
            ".close-icon, .body-overlay-desktop",
            function () {
                $(".modal-wrapper, .body-overlay-desktop").removeClass(
                    "active",
                );
            },
        );
        $(document).on("click", ".popup-modal", function () {
            $(".modal-wrapper, .body-overlay-desktop").addClass("active");
        });

        /*-----------------------------------
            WOW active
        -----------------------------------*/
        new WOW().init();

    
        /*-----------------------------------
            Back To TOP
        -----------------------------------*/
        (function () {
            var progressPath = document.querySelector(".progressParent path");
            var pathLength = progressPath.getTotalLength();
            progressPath.style.transition =
                progressPath.style.WebkitTransition = "none";
            progressPath.style.strokeDasharray = pathLength + " " + pathLength;
            progressPath.style.strokeDashoffset = pathLength;
            progressPath.getBoundingClientRect();
            progressPath.style.transition =
                progressPath.style.WebkitTransition =
                    "stroke-dashoffset 10ms linear";
            var updateProgress = function () {
                var scroll = $(window).scrollTop();
                var height = $(document).height() - $(window).height();
                var progress = pathLength - (scroll * pathLength) / height;
                progressPath.style.strokeDashoffset = progress;
            };
            updateProgress();
            $(window).scroll(updateProgress);
            var offset = 50;
            var duration = 550;
            jQuery(window).on("scroll", function () {
                if (jQuery(this).scrollTop() > offset) {
                    jQuery(".progressParent").addClass("rn-backto-top-active");
                } else {
                    jQuery(".progressParent").removeClass(
                        "rn-backto-top-active",
                    );
                }
            });
            jQuery(".progressParent").on("click", function (event) {
                event.preventDefault();
                jQuery("html, body").animate({ scrollTop: 0 }, duration);
                return false;
            });
        })();


        //sidebar btn - open
        $(".sidebar-btn a").on("click", function () {
            $(".listing-filter-area").addClass("show");
            $("#sidebar_overlay").addClass("show");
            $("body").css("overflow", "hidden");
        });

        // sidebar close btn & overlay - close
        $("#sidebar_close_btn, #sidebar_overlay").on("click", function () {
            $(".listing-filter-area").removeClass("show");
            $("#sidebar_overlay").removeClass("show");
            $("body").css("overflow", "");
        });

       
    });

    $(window).on("load", function () {
        /*------------------------------
            Preloader
        -------------------------------*/
        $(".preloader-inner").fadeOut(1000);
    });

    // close media image modal
    $(".popup_close").on("click", function () {
        $(".modal").modal("hide");
    });
})(jQuery);
