;
(function($) {
    $(document).ready(function() {
        var current_value = $("#qr_code_toggle").val();
        $('#toggle1').minitoggle();
        if (current_value) {
            $('#toggle1 .minitoggle').addClass('active');
        }
        $('#toggle1').on("toggle", function(e) {
            if (e.isActive)
                $("#qr_code_toggle").val(1)
            else
                $("#qr_code_toggle").val(0)
        });
    });
})(jQuery);