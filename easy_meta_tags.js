$(function() {
    var selectors = ['easy-meta-tags-keywords', 'easy-meta-tags-description'];
    selectors.forEach(function(selector) {
        $('#' + selector).on('keyup', function() {
            var $scope = $(this);
            $('#' + selector + '-length').html($scope.val().length);
        })
        .trigger('keyup');
    });
});