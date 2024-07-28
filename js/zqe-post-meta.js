jQuery(document).ready(function($) {
    $(document).on('click', '.add-repeatable', function(e) {
        e.preventDefault();
        var $this = $(this);
        var $repeatable = $this.closest('.zqe-post-meta-repeatable');
        var $item = $repeatable.find('.repeatable-item:first').clone();
        $item.find('input, textarea, select').each(function() {
            var $input = $(this);
            $input.val('');
            var name = $input.attr('name').replace(/\[(\d+)\]/, function(full, match) {
                return '[' + (parseInt(match) + 1) + ']';
            });
            $input.attr('name', name);
        });
        $repeatable.append($item);
    });

    $(document).on('click', '.remove-repeatable', function(e) {
        e.preventDefault();
        $(this).closest('.repeatable-item').remove();
    });
});
