define(function(require, exports, module) {
    require("jquery.jcrop-css");
    require("jquery.jcrop");

    exports.run = function() {
        $picture = $("#avatar-crop"),
        $form    = $("#avatar-crop-form");

        var scaledWidth = $picture.attr('width'),
            scaledHeight = $picture.attr('height'),
            ratio = 1,
            naturalWidth = $picture.data('naturalWidth'),
            naturalHeight = $picture.data('naturalHeight'),
            selectWidth = 200 * (naturalWidth/scaledWidth),
            selectHeight = 200 * (naturalHeight/scaledHeight);

        $picture.Jcrop({
            trueSize: [naturalWidth, naturalHeight],
            setSelect: [0,0,selectWidth,selectHeight],
            aspectRatio: ratio,
            minSize: [48,48],
            onSelect: function(c) {
                $form.find('[name=x]').val(c.x);
                $form.find('[name=y]').val(c.y);
                $form.find('[name=width]').val(c.w);
                $form.find('[name=height]').val(c.h);
            }
        });



    };

});