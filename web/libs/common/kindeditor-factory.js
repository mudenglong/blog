define(function(require, exports, module) {
    require('kindeditor');
    var simpleItems = [
        'bold', 'italic', 'underline', 'forecolor', '|', 
        'insertorderedlist', 'insertunorderedlist', '|', 
        'link', 'unlink', 'image', '|', 
        'removeformat', 'source'
    ];

    var fullItems = [
        'bold', 'italic', 'underline', 'strikethrough', '|',
        'link', 'unlink', '|',
        'insertorderedlist', 'insertunorderedlist','indent', 'outdent', '|',
         'image', 'flash', 'insertfile', 'code', 'table', 'hr', '/',
        'formatblock', 'fontname', 'fontsize', '|',
        'forecolor', 'hilitecolor',   '|', 
        'justifyleft', 'justifycenter', 'justifyright', 'justifyfull',  '|',
        'removeformat', 'clearhtml', '|',
        'source', 'preview',  'fullscreen', '|',
        'about'
    ];

    var contentCss = [];
    contentCss.push('body {font-size: 14px; line-height: 1.428571429;color: #333333;}');
    contentCss.push('a {color: #428bca;}');
    contentCss.push('p {margin: 0 0 10px;}');
    contentCss.push('img {max-width: 100%;}');
    contentCss.push('p {font-size:14px;}');
    
    var defaultConfig = {
        width: '100%',
        resizeType: 1,
        uploadJson: app.config.editor_upload_path,
        filePostName: 'file',
        cssData: contentCss.join('\n'),
        extraFileUploadParams: {}
    };

    var customConfigs = {};
    customConfigs.simple = { items : simpleItems };
    customConfigs.full = { items : fullItems };


    function getConfig (name, extendConfig) {
        if (!extendConfig) {
            extendConfig = {};
        };
        return $.extend({}, defaultConfig, customConfigs[name], extendConfig);
    }

    exports.create = function(select, name, extendConfig) {
        return KindEditor.create(select, getConfig(name, extendConfig));
    }
});