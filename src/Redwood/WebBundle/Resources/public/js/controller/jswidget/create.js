define(function(require, exports, module) {
    require('common/jquery-validate').inject($);

    require('jquery.select2-css');
    require('jquery.select2');

    exports.run = function() {
        
        console.log('kkkkkkkk');
        $("#jswidgetForm").validate({
            rules: {
                'title': {
                    required: true,
                    minlength: 2
                },
                'url': {
                    required: true,
                    ths_gitlab_email:[function (url) {
                        var visitUrl = url.replace(/(?:gitlab)/, function(s0, s1){ return 'demo'; });
                        $('#jswidget_iframeUrl').val(visitUrl);
                    }]
                },
            },
            messages: {
                'title':{
                    required: "请输入标题",
                    minlength: "标题的最小长度为2"
                },
                'url': {
                    required:"请输入URL",
                    ths_gitlab_email:"地址不符合gitlab格式"
                }
            }
        });


        var tagUrl = '/tag/match_jsonp'
        $('#jswidget_tags').select2({

            ajax: {
                url: tagUrl + '#',
                dataType: 'json',
                quietMillis: 100,
                data: function(term, page) {
                    return {
                        q: term,
                        page_limit: 10
                    };
                },
                results: function(data) {
                    var results = [];
                    $.each(data, function(index, item) {

                        results.push({
                            id: item.name,
                            name: item.name
                        });
                    });

                    return {
                        results: results
                    };

                }
            },
            initSelection: function(element, callback) {
                var data = [];
                $(element.val().split(",")).each(function (s0, name) {
                    data.push({
                        id: name,
                        name: name
                    });
                });
                callback(data);
            },
            formatSelection: function(item) {
                return item.name;
            },
            formatResult: function(item) {
                return item.name;
            },
            width: 'off',
            multiple: true,
            placeholder: "请输入标签",
            width: 'off',
            multiple: true,
            createSearchChoice: function() {
                return null;
            },
            maximumSelectionSize: 17
        });




        // tag 转为 后台可识别的字符
        // $('#jswidgetForm').submit(function(e) {
        //     debugger;
        //     e.preventDefault();
        //     return;        
        // });

    };

});

